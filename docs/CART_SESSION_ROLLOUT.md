# Documentación de Despliegue: Carrito por Sesión

Este documento detalla cómo activar, probar y, si es necesario, desactivar el nuevo sistema de carrito de compras basado en Sesión PHP (Session Cart) que reemplaza progresivamente al carrito Legacy (Vue + LocalStorage).

## 1. Configuración de Feature Flags

El sistema utiliza "Feature Flags" (Banderas de funcionalidad) definidas en `config/cart.php`. Estas banderas permiten activar o desactivar la funcionalidad sin cambiar el código.

Ubicación: `config/cart.php`

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Session Cart Feature Flag
    |--------------------------------------------------------------------------
    |
    | Habilita el nuevo sistema de carrito basado en PHP Session.
    | - true: Usa el nuevo sistema y las nuevas vistas Blade.
    | - false: Usa el sistema antiguo (Vue + LocalStorage).
    |
    */
    'session_enabled' => env('CART_SESSION_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Products using Session Cart
    |--------------------------------------------------------------------------
    |
    | Define qué IDs de producto usarán la nueva vista 'standard' y el botón
    | de "Agregar al Carrito" que conecta con el backend de sesión.
    | Si está vacío [], ningún producto usará el nuevo sistema, 
    | a menos que session_enabled fuerce algún comportamiento global.
    |
    */
    'session_products' => [
        1, // ID del producto "Corte a Medida" (Refactorizado)
    ],
];
```

### Cómo activar (Producción)
Para activar el carrito nuevo, debes editar el archivo `.env` en el servidor:

```env
CART_SESSION_ENABLED=true
```

Para aplicar los cambios de configuración en producción, recuerda limpiar el caché:
```bash
php artisan config:clear
```

### Cómo desactivar (Rollback inmediato)
Si se encuentra un error crítico, simplemente cambia el valor a `false` en el `.env`:

```env
CART_SESSION_ENABLED=false
```
Y limpia el caché nuevamente (`php artisan config:clear`). El sitio volverá inmediatamente a usar el carrito viejo (Vue).

## 2. Flujo de Prueba (Testing)

Para verificar que todo funciona correctamente, sigue estos pasos:

1.  **Producto Refactorizado (ID 1):**
    *   Ve a un producto configurado en `session_products` (ej. ID 1).
    *   Verifica que la vista se carga correctamente (Layout standard).
    *   Prueba los botones de cantidad (+ / -).
    *   Clic en "Agregar al pedido". Debería redirigir al `/carrito` o mostrar notificación.

2.  **Vista de Carrito (`/carrito`):**
    *   Si tienes items en el "Session Cart", deberías ver una tabla HTML (no el componente Vue).
    *   Verifica que los items agregados estén ahí.
    *   Prueba cambiar cantidades desde el carrito.
    *   Prueba eliminar un item.

3.  **Cálculo de Envío:**
    *   En `/carrito`, selecciona un "Destino / Localidad" del dropdown.
    *   El sistema debería mostrar "Calculando..." y luego el costo.
    *   Verifica que el "Total" se actualice sumando el envío.

4.  **Checkout (`/finalizar-compra`):**
    *   Clic en "FINALIZAR COMPRA".
    *   Debes llegar a la página de formulario (`finalizar_compra.blade.php`).
    *   Verifica que el campo "Total" (oculto) incluya el costo de envío calculado.
    *   Completa el formulario (usa datos de prueba).
    *   Clic en "REALIZAR PEDIDO".

5.  **Confirmación:**
    *   Debes ser redirigido a la página de éxito (`/pago/message/success/...`).
    *   Verifica en el Panel Administrativo (o Base de Datos) que el pedido se haya creado con los items correctos.

## 3. Limitaciones Conocidas

*   **Persistencia Mixta:** Los items agregados al "Carrito Viejo" (LocalStorage) NO son visibles en el "Carrito Nuevo" (Sesión) y viceversa. Al activar el flag, los usuarios empezarán con un carrito de sesión vacío.
*   **Productos Legacy:** Los productos que NO estén en la lista `session_products` seguirán usando el botón de agregar viejo (JS puro/Vue). Si `session_enabled` es true, deberías eventualmente migrar todos los productos o asegurar compatibilidad hibrida (actualmente el controlador prioriza la Sesión si existe).

## 4. Archivos Clave Modificados

*   `app/Services/CartSessionService.php`: Logica del carrito.
*   `app/Http/Controllers/CartSessionController.php`: API y Calculo de envío.
*   `app/Http/Controllers/CarritoController.php`: Procesamiento de ordenes (Intercepta sesión).
*   `resources/views/web/carrito.blade.php`: Vista condicional.
*   `public/js/cart-session.js`: Lógica frontend.
