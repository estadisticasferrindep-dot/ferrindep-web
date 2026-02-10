<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

use App\Models\Producto;
use App\Models\Galeria;
use App\Http\Controllers\CarritoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('adm', function () {
    return view('auth/login');
});

Route::get('clientes', [App\Http\Controllers\WebController::class, 'login'])->name('web.clientes');
Route::post('clientes/login', [App\Http\Controllers\Usuario\LoginController::class, 'login'])->name('web.clientes.login');
Route::get('clientes/logout', [App\Http\Controllers\Usuario\LoginController::class, 'logout'])->name('web.clientes.logout');
Route::post('clientes/register', [App\Http\Controllers\Usuario\RegisterController::class, 'register'])->name('web.clientes.register');

/* ====== MIS COMPRAS (SIN PASSWORD) ====== */
Route::get('mis-compras', [App\Http\Controllers\MisComprasController::class, 'login'])->name('web.mis_compras');
Route::post('mis-compras/buscar', [App\Http\Controllers\MisComprasController::class, 'historial'])->name('web.mis_compras.buscar');
/* ======================================== */
Auth::routes();

Route::post('carrito', [App\Http\Controllers\CarritoController::class, 'enviar'])->name('web.envio_pedido');
Route::get('cartdata', [App\Http\Controllers\CarritoController::class, 'cartdata'])->name('cartdata');

Route::get('pago/{id}', [App\Http\Controllers\CarritoController::class, 'confirm'])->name('web.confirm');
Route::get('pago/message/success/{id}', [App\Http\Controllers\WebController::class, 'success'])->name('web.success');
Route::get('pago/message/failure', [App\Http\Controllers\WebController::class, 'failure'])->name('web.failure');
Route::get('pago/message/pending', [App\Http\Controllers\WebController::class, 'pending'])->name('web.pending');

Route::post('pago/message/success', [App\Http\Controllers\WebController::class, 'enviar_pedido'])->name('web.pedido');

/* ====== MERCADO PAGO (Checkout Pro, sin webhook) ====== */
Route::match(['GET', 'POST'], 'pagar/{pedido}', [CarritoController::class, 'pagar'])->name('mp.pagar');
Route::get('mp/retorno', [CarritoController::class, 'retorno'])->name('mp.retorno');
/* ====================================================== */

Route::get('/', [App\Http\Controllers\WebController::class, 'index'])->name('web.home');
Route::post('/email', [App\Http\Controllers\EmailController::class, 'store'])->name('web.email');

Route::get('buscar', [App\Http\Controllers\WebController::class, 'index_buscar'])->name('web.home.buscar');
Route::get('resultados', [App\Http\Controllers\WebController::class, 'busqueda'])->name('web.busqueda');

Route::get('empresa', [App\Http\Controllers\WebController::class, 'empresa'])->name('web.empresa');

Route::get('carrito', [App\Http\Controllers\WebController::class, 'carrito'])->name('web.carrito');
Route::get('/api/carrito', [App\Http\Controllers\WebController::class, 'carritoData'])->name('web.carrito.data');
//Route::get('carrito/finalizar-compra', [App\Http\Controllers\WebController::class, 'finalizar_compra'])->name('web.finalizar_compra');

/* ====== SESSION CART RUTAS (FEATURE FLAG) ====== */
Route::prefix('carrito/session')->name('cart.session.')->group(function () {
    Route::post('/set-qty', [App\Http\Controllers\CartSessionController::class, 'setQty'])->name('setQty');
    Route::get('/summary', [App\Http\Controllers\CartSessionController::class, 'summary'])->name('summary');
    Route::post('/clear', [App\Http\Controllers\CartSessionController::class, 'clear'])->name('clear');
    Route::post('/calculate-shipping', [App\Http\Controllers\CartSessionController::class, 'calculateShipping'])->name('calculate_shipping');
});
/* =============================================== */

Route::get('/finalizar-compra', [App\Http\Controllers\WebController::class, 'finalizar_compra'])->name('web.finalizar_compra');
Route::get('/fin', [App\Http\Controllers\WebController::class, 'fin'])->name('web.fin');

Route::get('equipos', [App\Http\Controllers\WebController::class, 'equipos'])->name('web.equipos.equipos');
Route::get('equipos/buscar/filtro', [App\Http\Controllers\WebController::class, 'equipos_buscar'])->name('web.equipos.buscar');

Route::get('equipos/clase/{clase}', [App\Http\Controllers\WebController::class, 'equipos_clase'])->name('web.equipos.clase');
Route::get('equipos/{equipo}', [App\Http\Controllers\WebController::class, 'equipos_equipo'])->name('web.equipos.equipo');
Route::post('equipos/enviar/{equipo}', [App\Http\Controllers\WebController::class, 'contactanos_experto_equipo'])->name('web.contactanos_experto_equipo');

Route::get('trabajos', [App\Http\Controllers\WebController::class, 'trabajos'])->name('web.trabajos.trabajos');
Route::get('trabajos/{trabajo}', [App\Http\Controllers\WebController::class, 'trabajos_trabajo'])->name('web.trabajos.trabajo');

Route::get('productos/{familia}', [App\Http\Controllers\WebController::class, 'productos'])->name('web.productos.productos2');
Route::get('productos/{familia}#mobile', [App\Http\Controllers\WebController::class, 'productos'])->name('web.productos.productos2.mobile');

Route::get('productos/categoria/{categoria}/{familia}', [App\Http\Controllers\WebController::class, 'productos_categoria'])->name('web.productos.categoria');
Route::get('productos/categoria/{categoria}/{familia}#mobile', [App\Http\Controllers\WebController::class, 'productos_categoria'])->name('web.productos.categoria.mobile');

Route::get('productos/producto/{producto}', [App\Http\Controllers\WebController::class, 'productos_producto'])->name('web.productos.producto');
Route::get('productos/producto/{producto}#mobile', [App\Http\Controllers\WebController::class, 'productos_producto'])->name('web.productos.producto.mobile');

Route::post('productos/enviar/{producto}', [App\Http\Controllers\WebController::class, 'contactanos_experto'])->name('web.contactanos_experto');

Route::get('servicios', [App\Http\Controllers\WebController::class, 'servicios'])->name('web.servicios');
Route::post('servicios/enviar', [App\Http\Controllers\WebController::class, 'contactanos_post_venta'])->name('web.contactanos_post_venta');

Route::get('ofertas', [App\Http\Controllers\WebController::class, 'ofertas'])->name('web.ofertas');

Route::get('blogs/{filtro}', [App\Http\Controllers\WebController::class, 'blogs'])->name('web.blogs');
Route::get('blogs/noticia/{noticia}', [App\Http\Controllers\WebController::class, 'blogs_noticia'])->name('web.blogs.noticia');

Route::get('descargas/{descargable}', [App\Http\Controllers\WebController::class, 'descargas'])->name('web.descargas');
// Route::get('clientes', [App\Http\Controllers\WebController::class, 'clientes'])->name('web.clientes');
Route::get('videos', [App\Http\Controllers\WebController::class, 'videos'])->name('web.videos');
Route::get('preguntas_frecuentes', [App\Http\Controllers\WebController::class, 'preguntas_frecuentes'])->name('web.preguntas_frecuentes');
Route::get('solicitud_de_presupuesto', [App\Http\Controllers\WebController::class, 'solicitud_de_presupuesto'])->name('web.solicitud_de_presupuesto');
Route::post('solicitud_de_presupuesto', [App\Http\Controllers\WebController::class, 'presupuesto'])->name('web.presupuesto');

Route::get('galeria', [App\Http\Controllers\WebController::class, 'galeria'])->name('web.galeria');

Route::get('contacto', [App\Http\Controllers\WebController::class, 'contacto'])->name('web.contacto');
Route::post('contacto', [App\Http\Controllers\WebController::class, 'contactanos'])->name('web.contactanos');

Route::get('adm/productos/index', [App\Http\Controllers\ProductoController::class, 'index'])->name('productos.index');
Route::get('adm/productos/create', [App\Http\Controllers\ProductoController::class, 'create'])->name('productos.create');
Route::post('adm/productos', [App\Http\Controllers\ProductoController::class, 'store'])->name('productos.store');
Route::get('adm/productos/{producto}/edit', [App\Http\Controllers\ProductoController::class, 'edit'])->name('productos.edit');
Route::put('adm/productos/{producto}/update', [App\Http\Controllers\ProductoController::class, 'update'])->name('productos.update');
Route::delete('adm/productos/{producto}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('productos.destroy');

Route::get('familias/index', [App\Http\Controllers\FamiliaController::class, 'index'])->name('familias.index');
Route::get('familias/create', [App\Http\Controllers\FamiliaController::class, 'create'])->name('familias.create');
Route::post('familias', [App\Http\Controllers\FamiliaController::class, 'store'])->name('familias.store');
Route::get('familias/{familia}/edit', [App\Http\Controllers\FamiliaController::class, 'edit'])->name('familias.edit');
Route::put('familias/{familia}/update', [App\Http\Controllers\FamiliaController::class, 'update'])->name('familias.update');
Route::delete('familias/{familia}', [App\Http\Controllers\FamiliaController::class, 'destroy'])->name('familias.destroy');

Route::get('categorias/index', [App\Http\Controllers\CategoriaController::class, 'index'])->name('categorias.index');
Route::get('categorias/create', [App\Http\Controllers\CategoriaController::class, 'create'])->name('categorias.create');
Route::post('categorias', [App\Http\Controllers\CategoriaController::class, 'store'])->name('categorias.store');
Route::get('categorias/{categoria}/edit', [App\Http\Controllers\CategoriaController::class, 'edit'])->name('categorias.edit');
Route::put('categorias/{categoria}/update', [App\Http\Controllers\CategoriaController::class, 'update'])->name('categorias.update');
Route::delete('categorias/{categoria}', [App\Http\Controllers\CategoriaController::class, 'destroy'])->name('categorias.destroy');

Route::get('redes.index', [App\Http\Controllers\RedController::class, 'index'])->name('redes.index');
Route::get('redes/create', [App\Http\Controllers\RedController::class, 'create'])->name('redes.create');
Route::post('redes', [App\Http\Controllers\RedController::class, 'store'])->name('redes.store');
Route::get('redes/{red}/edit', [App\Http\Controllers\RedController::class, 'edit'])->name('redes.edit');
Route::put('redes/{red}/update', [App\Http\Controllers\RedController::class, 'update'])->name('redes.update');
Route::delete('redes/{red}', [App\Http\Controllers\RedController::class, 'destroy'])->name('redes.destroy');

Route::get('metadatos.index', [App\Http\Controllers\MetadatoController::class, 'index'])->name('metadatos.index');
Route::get('metadatos/create', [App\Http\Controllers\MetadatoController::class, 'create'])->name('metadatos.create');
Route::post('metadatos', [App\Http\Controllers\MetadatoController::class, 'store'])->name('metadatos.store');
Route::get('metadatos/{metadato}/edit', [App\Http\Controllers\MetadatoController::class, 'edit'])->name('metadatos.edit');
Route::put('metadatos/{metadato}/update', [App\Http\Controllers\MetadatoController::class, 'update'])->name('metadatos.update');
Route::delete('metadatos/{metadato}', [App\Http\Controllers\MetadatoController::class, 'destroy'])->name('metadatos.destroy');

Route::get('configuraciones', [App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuraciones.index');
Route::put('configuraciones/{configuracion}/update', [App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuraciones.update');

Route::get('imagenes', [App\Http\Controllers\ImagenController::class, 'index'])->name('imagenes.index');
Route::get('imagenes/create', [App\Http\Controllers\ImagenController::class, 'create'])->name('imagenes.create');
Route::post('imagenes', [App\Http\Controllers\ImagenController::class, 'store'])->name('imagenes.store');
Route::get('imagenes/{imagen}/edit', [App\Http\Controllers\ImagenController::class, 'edit'])->name('imagenes.edit');
Route::put('imagenes/{imagen}/update', [App\Http\Controllers\ImagenController::class, 'update'])->name('imagenes.update');
Route::delete('imagenes/{imagen}', [App\Http\Controllers\ImagenController::class, 'destroy'])->name('imagenes.destroy');

Route::get('fotos', [App\Http\Controllers\FotoController::class, 'index'])->name('fotos.index');
Route::get('fotos/create', [App\Http\Controllers\FotoController::class, 'create'])->name('fotos.create');
Route::post('fotos', [App\Http\Controllers\FotoController::class, 'store'])->name('fotos.store');
Route::get('fotos/{foto}/edit', [App\Http\Controllers\FotoController::class, 'edit'])->name('fotos.edit');
Route::put('fotos/{foto}/update', [App\Http\Controllers\FotoController::class, 'update'])->name('fotos.update');
Route::delete('fotos/{foto}', [App\Http\Controllers\FotoController::class, 'destroy'])->name('fotos.destroy');

Route::get('colores', [App\Http\Controllers\ColorController::class, 'index'])->name('colores.index');
Route::get('colores/create', [App\Http\Controllers\ColorController::class, 'create'])->name('colores.create');
Route::post('colores', [App\Http\Controllers\ColorController::class, 'store'])->name('colores.store');
Route::get('colores/{color}/edit', [App\Http\Controllers\ColorController::class, 'edit'])->name('colores.edit');
Route::put('colores/{color}/update', [App\Http\Controllers\ColorController::class, 'update'])->name('colores.update');
Route::delete('colores/{color}', [App\Http\Controllers\ColorController::class, 'destroy'])->name('colores.destroy');

Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('homes.index');
Route::put('home/{home}/update', [App\Http\Controllers\HomeController::class, 'update'])->name('homes.update');

Route::get('empresas.index', [App\Http\Controllers\EmpresaController::class, 'index'])->name('empresas.index');
Route::put('empresas/{empresa}/update', [App\Http\Controllers\EmpresaController::class, 'update'])->name('empresas.update');

Route::get('show_trabajos.index', [App\Http\Controllers\TrabajoController::class, 'index'])->name('show_trabajos.index');
Route::get('show_trabajos/create_trabajo', [App\Http\Controllers\TrabajoController::class, 'create_trabajo'])->name('trabajos.create');
Route::post('show_trabajos/store_trabajo', [App\Http\Controllers\TrabajoController::class, 'store_trabajo'])->name('trabajos.store');
Route::get('show_trabajos/{trabajo}/edit_trabajo', [App\Http\Controllers\TrabajoController::class, 'edit_trabajo'])->name('trabajos.edit');
Route::put('show_trabajos/{trabajo}/update_trabajo', [App\Http\Controllers\TrabajoController::class, 'update_trabajo'])->name('trabajos.update');
Route::delete('show_trabajos/{trabajo}/delete_trabajo', [App\Http\Controllers\TrabajoController::class, 'destroy_trabajo'])->name('trabajos.destroy');

Route::get('show_trabajos/{trabajo}/create_seccion', [App\Http\Controllers\TrabajoController::class, 'create_seccion'])->name('secciones.create');
Route::post('show_trabajos/{trabajo}/store_seccion', [App\Http\Controllers\TrabajoController::class, 'store_seccion'])->name('secciones.store');
Route::get('show_trabajos/{seccion}/edit_seccion', [App\Http\Controllers\TrabajoController::class, 'edit_seccion'])->name('secciones.edit');
Route::put('show_trabajos/{seccion}/update_seccion', [App\Http\Controllers\TrabajoController::class, 'update_seccion'])->name('secciones.update');
Route::delete('show_trabajos/{seccion}/delete_seccion', [App\Http\Controllers\TrabajoController::class, 'destroy_seccion'])->name('secciones.destroy');

Route::get('show_trabajos/{trabajo}/create_galeria', [App\Http\Controllers\TrabajoController::class, 'create_galeria'])->name('galerias.create');
Route::post('show_trabajos/{trabajo}/store_galeria', [App\Http\Controllers\TrabajoController::class, 'store_galeria'])->name('galerias.store');
Route::get('show_trabajos/{galeria}/edit_galeria', [App\Http\Controllers\TrabajoController::class, 'edit_galeria'])->name('galerias.edit');
Route::put('show_trabajos/{galeria}/update_galeria', [App\Http\Controllers\TrabajoController::class, 'update_galeria'])->name('galerias.update');
Route::delete('show_trabajos/{galeria}/delete_galeria', [App\Http\Controllers\TrabajoController::class, 'destroy_galeria'])->name('galerias.destroy');

Route::get('show_productos.index', [App\Http\Controllers\ProductoController::class, 'index'])->name('show_productos.index');
Route::get('show_productos/create_producto', [App\Http\Controllers\ProductoController::class, 'create_producto'])->name('productos.create_producto');
Route::post('show_productos/store_producto', [App\Http\Controllers\ProductoController::class, 'store_producto'])->name('productos.store_producto');
Route::get('show_productos/{producto}/edit_producto', [App\Http\Controllers\ProductoController::class, 'edit_producto'])->name('productos.edit');
Route::put('show_productos/{producto}/update_producto', [App\Http\Controllers\ProductoController::class, 'update_producto'])->name('productos.update');
Route::delete('show_productos/{producto}/delete_producto', [App\Http\Controllers\ProductoController::class, 'destroy_producto'])->name('productos.destroy');

Route::get('show_productos/{producto}/create_color', [App\Http\Controllers\ProductoController::class, 'create_color'])->name('coloresP.create');
Route::post('show_productos/{producto}/store_color', [App\Http\Controllers\ProductoController::class, 'store_color'])->name('coloresP.store');
Route::get('show_productos/{cp}/edit_color/{producto}', [App\Http\Controllers\ProductoController::class, 'edit_color'])->name('coloresP.edit');
Route::put('show_productos/{cp}/update_color/{producto}', [App\Http\Controllers\ProductoController::class, 'update_color'])->name('coloresP.update');
Route::delete('show_productos/{cp}/delete_color/{producto}', [App\Http\Controllers\ProductoController::class, 'destroy_color'])->name('coloresP.destroy');

Route::get('show_productos/{producto}/create_galeria', [App\Http\Controllers\ProductoController::class, 'create_galeria'])->name('galeriasP.create');
Route::post('show_productos/{producto}/store_galeria', [App\Http\Controllers\ProductoController::class, 'store_galeria'])->name('galeriasP.store');
Route::get('show_productos/{galeria}/edit_galeria', [App\Http\Controllers\ProductoController::class, 'edit_galeria'])->name('galeriasP.edit');
Route::put('show_productos/{galeria}/update_galeria', [App\Http\Controllers\ProductoController::class, 'update_galeria'])->name('galeriasP.update');
Route::delete('show_productos/{galeria}/delete_galeria', [App\Http\Controllers\ProductoController::class, 'destroy_galeria'])->name('galeriasP.destroy');

Route::get('show_productos/{producto}/create_diametro', [App\Http\Controllers\ProductoController::class, 'create_diametro'])->name('diametrosP.create');
Route::post('show_productos/{producto}/store_diametro', [App\Http\Controllers\ProductoController::class, 'store_diametro'])->name('diametrosP.store');
Route::get('show_productos/{diametro}/edit_diametro/{producto}', [App\Http\Controllers\ProductoController::class, 'edit_diametro'])->name('diametrosP.edit');
Route::put('show_productos/{diametro}/update_diametro/{producto}', [App\Http\Controllers\ProductoController::class, 'update_diametro'])->name('diametrosP.update');
Route::delete('show_productos/{diametro}/delete_diametro/{producto}', [App\Http\Controllers\ProductoController::class, 'destroy_diametro'])->name('diametrosP.destroy');

Route::get('show_productos/{producto}/create_rango', [App\Http\Controllers\ProductoController::class, 'create_rango'])->name('rangosP.create');
Route::post('show_productos/{producto}/store_rango', [App\Http\Controllers\ProductoController::class, 'store_rango'])->name('rangosP.store');
Route::get('show_productos/{rango}/edit_rango/{producto}', [App\Http\Controllers\ProductoController::class, 'edit_rango'])->name('rangosP.edit');
Route::put('show_productos/{rango}/update_rango/{producto}', [App\Http\Controllers\ProductoController::class, 'update_rango'])->name('rangosP.update');
Route::delete('show_productos/{rango}/delete_rango/{producto}', [App\Http\Controllers\ProductoController::class, 'destroy_rango'])->name('rangosP.destroy');

Route::get('show_productos/{producto}/create_presentacion', [App\Http\Controllers\ProductoController::class, 'create_presentacion'])->name('presentacionesP.create');
Route::post('show_productos/{producto}/store_presentacion', [App\Http\Controllers\ProductoController::class, 'store_presentacion'])->name('presentacionesP.store');
Route::get('show_productos/{presentacion}/edit_presentacion/{producto}', [App\Http\Controllers\ProductoController::class, 'edit_presentacion'])->name('presentacionesP.edit');
Route::put('show_productos/{presentacion}/update_presentacion/{producto}', [App\Http\Controllers\ProductoController::class, 'update_presentacion'])->name('presentacionesP.update');
Route::put('show_productos/{presentacion}/update_presentacion2/{producto}', [App\Http\Controllers\ProductoController::class, 'update_presentacion_2'])->name('presentacionesP2.update');
Route::delete('show_productos/{presentacion}/delete_presentacion/{producto}', [App\Http\Controllers\ProductoController::class, 'destroy_presentacion'])->name('presentacionesP.destroy');

Route::get('usuarios.index', [App\Http\Controllers\UsuarioController::class, 'index'])->name('usuarios.index');
Route::get('usuarios/create', [App\Http\Controllers\UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('usuarios', [App\Http\Controllers\UsuarioController::class, 'store'])->name('usuarios.store');
Route::get('usuarios/{usuario}/edit', [App\Http\Controllers\UsuarioController::class, 'edit'])->name('usuarios.edit');
Route::put('usuarios/{usuario}/update', [App\Http\Controllers\UsuarioController::class, 'update'])->name('usuarios.update');
Route::delete('usuarios/{usuario}', [App\Http\Controllers\UsuarioController::class, 'destroy'])->name('usuarios.destroy');

Route::get('pedidos.index', [App\Http\Controllers\PedidoController::class, 'index'])->name('pedidos.index');
Route::get('pedidos/create', [App\Http\Controllers\PedidoController::class, 'create'])->name('pedidos.create');
Route::post('pedidos', [App\Http\Controllers\PedidoController::class, 'store'])->name('pedidos.store');
Route::get('pedidos/{pedido}/edit', [App\Http\Controllers\PedidoController::class, 'edit'])->name('pedidos.edit');
Route::put('pedidos/{pedido}/update', [App\Http\Controllers\PedidoController::class, 'update'])->name('pedidos.update');
Route::delete('pedidos/{pedido}', [App\Http\Controllers\PedidoController::class, 'destroy'])->name('pedidos.destroy');

Route::get('configpedidos.index', [App\Http\Controllers\ConfigpedidoController::class, 'index'])->name('configpedidos.index');
Route::get('configpedidos/create', [App\Http\Controllers\ConfigpedidoController::class, 'create'])->name('configpedidos.create');
Route::post('configpedidos', [App\Http\Controllers\ConfigpedidoController::class, 'store'])->name('configpedidos.store');
Route::get('configpedidos/{configpedido}/edit', [App\Http\Controllers\ConfigpedidoController::class, 'edit'])->name('configpedidos.edit');
Route::put('configpedidos/{configpedido}/update', [App\Http\Controllers\ConfigpedidoController::class, 'update'])->name('configpedidos.update');
Route::delete('configpedidos/{configpedido}', [App\Http\Controllers\ConfigpedidoController::class, 'destroy'])->name('configpedidos.destroy');

Route::get('excel.index', [App\Http\Controllers\ExcelController::class, 'index'])->name('excel.index');
Route::post('excel/import', [App\Http\Controllers\ExcelController::class, 'import'])->name('excel.import');
Route::get('excel/export', [App\Http\Controllers\ExcelController::class, 'export'])->name('excel.export');
Route::put('excel/{excel}/update', [App\Http\Controllers\ExcelController::class, 'update'])->name('excel.update');

Route::get('excelcp.index', [App\Http\Controllers\ExcelcpController::class, 'index'])->name('excelcp.index');
Route::post('excelcp/import', [App\Http\Controllers\ExcelcpController::class, 'import'])->name('excelcp.import');
Route::get('excelcp/export', [App\Http\Controllers\ExcelcpController::class, 'export'])->name('excelcp.export');
Route::put('excelcp/{excelcp}/update', [App\Http\Controllers\ExcelcpController::class, 'update'])->name('excelcp.update');

Route::get('servicios.index', [App\Http\Controllers\ServicioController::class, 'index'])->name('servicios.index');
Route::get('servicios/create', [App\Http\Controllers\ServicioController::class, 'create'])->name('servicios.create');
Route::post('servicios', [App\Http\Controllers\ServicioController::class, 'store'])->name('servicios.store');
Route::get('servicios/{servicio}/edit', [App\Http\Controllers\ServicioController::class, 'edit'])->name('servicios.edit');
Route::put('servicios/{servicio}/update', [App\Http\Controllers\ServicioController::class, 'update'])->name('servicios.update');
Route::delete('servicios/{servicio}', [App\Http\Controllers\ServicioController::class, 'destroy'])->name('servicios.destroy');

Route::get('preguntas.index', [App\Http\Controllers\PreguntaController::class, 'index'])->name('preguntas.index');
Route::get('preguntas/create', [App\Http\Controllers\PreguntaController::class, 'create'])->name('preguntas.create');
Route::post('preguntas', [App\Http\Controllers\PreguntaController::class, 'store'])->name('preguntas.store');
Route::get('preguntas/{pregunta}/edit', [App\Http\Controllers\PreguntaController::class, 'edit'])->name('preguntas.edit');
Route::put('preguntas/{pregunta}/update', [App\Http\Controllers\PreguntaController::class, 'update'])->name('preguntas.update');
Route::delete('preguntas/{pregunta}', [App\Http\Controllers\PreguntaController::class, 'destroy'])->name('preguntas.destroy');

Route::get('videos.index', [App\Http\Controllers\VideoController::class, 'index'])->name('videos.index');
Route::get('videos/create', [App\Http\Controllers\VideoController::class, 'create'])->name('videos.create');
Route::post('videos', [App\Http\Controllers\VideoController::class, 'store'])->name('videos.store');
Route::get('videos/{video}/edit', [App\Http\Controllers\VideoController::class, 'edit'])->name('videos.edit');
Route::put('videos/{video}/update', [App\Http\Controllers\VideoController::class, 'update'])->name('videos.update');
Route::delete('videos/{video}', [App\Http\Controllers\VideoController::class, 'destroy'])->name('videos.destroy');

Route::get('espesores', [App\Http\Controllers\EspesorController::class, 'index'])->name('espesores.index');
Route::get('espesores/create', [App\Http\Controllers\EspesorController::class, 'create'])->name('espesores.create');
Route::post('espesores', [App\Http\Controllers\EspesorController::class, 'store'])->name('espesores.store');
Route::get('espesores/{espesor}/edit', [App\Http\Controllers\EspesorController::class, 'edit'])->name('espesores.edit');
Route::put('espesores/{espesor}/update', [App\Http\Controllers\EspesorController::class, 'update'])->name('espesores.update');
Route::delete('espesores/{espesor}', [App\Http\Controllers\EspesorController::class, 'destroy'])->name('espesores.destroy');

Route::get('medidas', [App\Http\Controllers\MedidaController::class, 'index'])->name('medidas.index');
Route::get('medidas/create', [App\Http\Controllers\MedidaController::class, 'create'])->name('medidas.create');
Route::post('medidas', [App\Http\Controllers\MedidaController::class, 'store'])->name('medidas.store');
Route::get('medidas/{medida}/edit', [App\Http\Controllers\MedidaController::class, 'edit'])->name('medidas.edit');
Route::put('medidas/{medida}/update', [App\Http\Controllers\MedidaController::class, 'update'])->name('medidas.update');
Route::delete('medidas/{medida}', [App\Http\Controllers\MedidaController::class, 'destroy'])->name('medidas.destroy');

Route::get('envios', [App\Http\Controllers\EnvioController::class, 'index'])->name('envios.index');
Route::get('envios/create', [App\Http\Controllers\EnvioController::class, 'create'])->name('envios.create');
Route::post('envios', [App\Http\Controllers\EnvioController::class, 'store'])->name('envios.store');
Route::get('envios/{envio}/edit', [App\Http\Controllers\EnvioController::class, 'edit'])->name('envios.edit');
Route::put('envios/{envio}/update', [App\Http\Controllers\EnvioController::class, 'update'])->name('envios.update');
Route::delete('envios/{envio}', [App\Http\Controllers\EnvioController::class, 'destroy'])->name('envios.destroy');

Route::get('destinos', [App\Http\Controllers\DestinoController::class, 'index'])->name('destinos.index');
Route::get('destinos/create', [App\Http\Controllers\DestinoController::class, 'create'])->name('destinos.create');
Route::post('destinos', [App\Http\Controllers\DestinoController::class, 'store'])->name('destinos.store');
Route::get('destinos/{destino}/edit', [App\Http\Controllers\DestinoController::class, 'edit'])->name('destinos.edit');
Route::put('destinos/{destino}/update', [App\Http\Controllers\DestinoController::class, 'update'])->name('destinos.update');
Route::delete('destinos/{destino}', [App\Http\Controllers\DestinoController::class, 'destroy'])->name('destinos.destroy');

Route::get('zonas', [App\Http\Controllers\ZonaController::class, 'index'])->name('zonas.index');
Route::get('zonas/create', [App\Http\Controllers\ZonaController::class, 'create'])->name('zonas.create');
Route::post('zonas', [App\Http\Controllers\ZonaController::class, 'store'])->name('zonas.store');
Route::get('zonas/{zona}/edit', [App\Http\Controllers\ZonaController::class, 'edit'])->name('zonas.edit');
Route::put('zonas/{zona}/update', [App\Http\Controllers\ZonaController::class, 'update'])->name('zonas.update');
Route::delete('zonas/{zona}', [App\Http\Controllers\ZonaController::class, 'destroy'])->name('zonas.destroy');

Route::resource('configuracion_ubicacion', App\Http\Controllers\ConfiguracionUbicacionController::class)->names('configuracion_ubicacion');

Route::get('destinozonas', [App\Http\Controllers\DestinozonaController::class, 'index'])->name('destinozonas.index');
Route::get('destinozonas/create', [App\Http\Controllers\DestinozonaController::class, 'create'])->name('destinozonas.create');
Route::post('destinozonas', [App\Http\Controllers\DestinozonaController::class, 'store'])->name('destinozonas.store');
Route::get('destinozonas/{destinozona}/edit', [App\Http\Controllers\DestinozonaController::class, 'edit'])->name('destinozonas.edit');
Route::put('destinozonas/{destinozona}/update', [App\Http\Controllers\DestinozonaController::class, 'update'])->name('destinozonas.update');
Route::delete('destinozonas/{destinozona}', [App\Http\Controllers\DestinozonaController::class, 'destroy'])->name('destinozonas.destroy');

Route::get('pesozonas', [App\Http\Controllers\PesozonaController::class, 'index'])->name('pesozonas.index');
Route::get('pesozonas/create', [App\Http\Controllers\PesozonaController::class, 'create'])->name('pesozonas.create');
Route::post('pesozonas', [App\Http\Controllers\PesozonaController::class, 'store'])->name('pesozonas.store');
Route::get('pesozonas/{pesozona}/edit', [App\Http\Controllers\PesozonaController::class, 'edit'])->name('pesozonas.edit');
Route::put('pesozonas/{pesozona}/update', [App\Http\Controllers\PesozonaController::class, 'update'])->name('pesozonas.update');
Route::delete('pesozonas/{pesozona}', [App\Http\Controllers\PesozonaController::class, 'destroy'])->name('pesozonas.destroy');

Route::controller(App\Http\Controllers\ShippingPricesController::class)
    ->prefix('shipping-prices')->name('shipping-prices.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });

// Route::get('clientes.index', [App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
// ...

Route::get('emails.index', [App\Http\Controllers\EmailController::class, 'index'])->name('emails.index');
Route::delete('emails/{email}', [App\Http\Controllers\EmailController::class, 'destroy'])->name('emails.destroy');

Route::get('/buscar', [\App\Http\Controllers\WebController::class, 'buscar'])->name('buscar');
Route::get('/api/sugerencias', [\App\Http\Controllers\WebController::class, 'sugerencias'])->name('sugerencias');

/* ========= RUTAS DE DIAGNÓSTICO (PROTEGIDAS CON AUTH) =========
   Solo accesibles para usuarios autenticados (admin).
   Visitá después de loguearte:
   - https://www.ferrindep.com.ar/debug/img
   - https://www.ferrindep.com.ar/debug/galeria
====================================================== */
Route::middleware('auth')->group(function () {

    Route::get('/debug/img', function () {
        $p = Producto::whereNotNull('imagen')->first();
        if (!$p) {
            return 'No encontré productos con campo imagen.';
        }
        $bdPath = $p->imagen;                               // valor guardado en BD (suele ser "public/...")
        $url = asset(Storage::url($bdPath));             // URL pública (debería salir /storage/...)

        return <<<HTML
<h3>Debug Producto->imagen</h3>
<p><b>Path en BD:</b> {$bdPath}</p>
<p><b>URL resuelta:</b> {$url}</p>
<p>Vista previa:</p>
<img src="{$url}" style="max-width:320px;border:1px solid #ccc;padding:4px">
HTML;
    });

    Route::get('/debug/galeria', function () {
        $g = Galeria::whereNotNull('imagen')->first();
        if (!$g) {
            return 'No encontré registros en galeria con campo imagen.';
        }
        $bdPath = $g->imagen;
        $url = asset(Storage::url($bdPath));

        return <<<HTML
<h3>Debug Galeria->imagen</h3>
<p><b>Path en BD:</b> {$bdPath}</p>
<p><b>URL resuelta:</b> {$url}</p>
<p>Vista previa:</p>
<img src="{$url}" style="max-width:320px;border:1px solid #ccc;padding:4px">
HTML;
    });


    // (opcional) chequeo del token de MP
    Route::get('/mp-check', function () {
        $token = env('MP_ACCESS_TOKEN');
        if (!$token)
            return 'MP_ACCESS_TOKEN vacío';
        $res = Http::withToken($token)->get('https://api.mercadopago.com/users/me');
        return $res->ok() ? $res->json() : $res->body();
    });

    /* ====== SETUP DB STATUS (TEMPORAL) ====== */
    Route::get('/setup-status-db', function () {
        try {
            $tabla = (new \App\Models\Pedido)->getTable();
            if (!\Illuminate\Support\Facades\Schema::hasColumn($tabla, 'estado_personalizado')) {
                \Illuminate\Support\Facades\Schema::table($tabla, function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('estado_personalizado')->nullable()->after('pago');
                });
                return "Columna 'estado_personalizado' agregada correctamente.";
            }
            return "La columna 'estado_personalizado' ya existe.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

    /* ====== UPDATE STATUS (AJAX ADMIN) ====== */
    Route::post('/adm/pedidos/update-status', [App\Http\Controllers\PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus');
    Route::post('/adm/pedidos/reenviar-email', [App\Http\Controllers\PedidoController::class, 'reenviarEmail'])->name('pedidos.reenviarEmail');
    Route::post('/adm/pedidos/update-nota', [App\Http\Controllers\PedidoController::class, 'updateNota'])->name('pedidos.updateNota');

    // RUTA TEMPORAL PARA PROBAR EL EMAIL MANUALMENTE
    Route::get('/forzar-reporte', function () {
        try {
            // Ejecuta el comando a la fuerza
            \Illuminate\Support\Facades\Artisan::call('reporte:busquedas');
            return '<h1>Comando ejecutado. ✅</h1> <p>Revisa tu casilla de correo (y la carpeta de Spam).</p>';
        } catch (\Exception $e) {
            return '<h1>Error ❌</h1> <p>' . $e->getMessage() . '</p>';
        }
    });

    /* ====== CACHE CLEARER (TEMPORAL) ====== */
    Route::get('/forzar-limpieza', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return '¡Cache del sistema eliminado con éxito!';
    });

    Route::get('/clear-cache', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            return '<h1>Caché borrada. ✅</h1> <p>Rutas, config y app caché eliminados.</p>';
        } catch (\Exception $e) {
            return '<h1>Error ❌</h1> <p>' . $e->getMessage() . '</p>';
        }
    });

    // Preview del Email "Listo para Retirar"
    Route::get('/debug-queue', function () {
        return response()->json([
            'queue_default' => config('queue.default'),
            'env_queue_connection' => env('QUEUE_CONNECTION'),
            'jobs_table_exists' => \Illuminate\Support\Facades\Schema::hasTable('jobs'),
            'failed_jobs_table_exists' => \Illuminate\Support\Facades\Schema::hasTable('failed_jobs'),
        ]);
    });

    Route::get('/preview-email-listo', function () {
        return view('emails.pedido_listo', [
            'nombre' => 'Mauro',
            'pedido' => ['id' => '3006'], // Simulating pedido object/array
            'cart' => [
                ['familia' => 'Malla', 'nombre' => 'Electrosoldada Galvanizada', 'medidas' => '50 x 50 mm.', 'ancho' => '100', 'espesor' => '(2,1 mm)', 'metros' => '5.00', 'cantidad' => 10],
                ['familia' => 'Alambre', 'nombre' => 'Recocido', 'medidas' => 'Rollo 1kg', 'ancho' => '', 'espesor' => '', 'metros' => '', 'cantidad' => 5],
            ]
        ]);
    });

    Route::get('/setup-queue-table', function () {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('jobs')) {
                \Illuminate\Support\Facades\Schema::create('jobs', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('queue')->index();
                    $table->longText('payload');
                    $table->unsignedTinyInteger('attempts');
                    $table->unsignedInteger('reserved_at')->nullable();
                    $table->unsignedInteger('available_at');
                    $table->unsignedInteger('created_at');
                });
                return "Tabla 'jobs' creada correctamente. AHORA: Cambia QUEUE_CONNECTION=database en tu archivo .env para activar la demora.";
            }
            return "La tabla 'jobs' ya existe. Verifica que QUEUE_CONNECTION=database en tu .env";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

    Route::get('/setup-order-history', function () {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('pedidos', 'historial_estado')) {
                \Illuminate\Support\Facades\Schema::table('pedidos', function (\Illuminate\Database\Schema\Blueprint $table) {
                    // Column TEXT is safer for JSON on MariaDB versions that don't support JSON native
                    // But Laravel handles JSON usually. Let's use text to be safe/simple
                    $table->text('historial_estado')->nullable()->after('estado_personalizado');
                });
                return "Columna 'historial_estado' agregada correctamente.";
            }
            return "La columna 'historial_estado' ya existe.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

    Route::get('/debug/prices', function () {
        $out = "<h1>Comparativa Precios Pilar</h1>";

        // LEGACY
        $out .= "<h2>1. Legacy (Destinos)</h2>";
        $destino = \App\Models\Destino::where('nombre', 'LIKE', '%Pilar%')->first();
        if ($destino) {
            $out .= "Destino: {$destino->nombre} (ID: {$destino->id})<br>";
            $dz = \App\Models\Destinozona::where('destino_id', $destino->id)->first();
            if ($dz) {
                $out .= "Zona ID: {$dz->zona_id}<br>";
                $precios = \App\Models\Pesozona::where('zona_id', $dz->zona_id)->get();
                foreach ($precios as $p)
                    $out .= "Peso {$p->peso}: $ {$p->costo}<br>";
            } else {
                $out .= "Destino sin Zona.<br>";
            }
        } else {
            $out .= "No encontrado en Legacy.<br>";
        }

        // FLEX
        $out .= "<h2>2. Flex (MapeoZonaFlex)</h2>";
        $flexs = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%pilar%')->get();
        foreach ($flexs as $f) {
            $t = $f->tarifa;
            $m = $t ? $t->monto : 0;
            $n = $t ? $t->nombre : 'Sin Tarifa';
            $out .= "Flex: '{$f->nombre_busqueda}' -> Tarifa: {$n} ($ {$m})<br>";
        }
        return $out;
    });

}); // End auth middleware group

// Ruta para el Chismoso (Guardar búsquedas del filtro)
Route::post('/guardar-busqueda', [App\Http\Controllers\WebController::class, 'guardarBusqueda'])->name('web.guardar_busqueda');

/* ====== REDIRECCIÓN DINÁMICA MERCADO LIBRE (MOBILE) ====== */
// Uso: /go/ml/{id_producto}
Route::get('/go/ml/{id}', function ($id) {
    // 1. Buscamos si hay un link ESPECÍFICO para este producto
    $specific = config("mercadolibre.links.{$id}");
    if ($specific) {
        return redirect()->away($specific);
    }

    // 2. Si no, usamos el link GENERAL (Fallback)
    $general = config("mercadolibre.general_link");
    if ($general) {
        return redirect()->away($general);
    }

    abort(404, 'Link de MercadoLibre no configurado.');
});

/* ====================================================== 
   CALCULADORA DE ENVÍOS (BETA / TEST)
   ====================================================== */
Route::get('/api/shipping/destinations', [\App\Http\Controllers\ShippingCalculatorController::class, 'getDestinations']);
Route::post('/api/shipping/calculate', [\App\Http\Controllers\ShippingCalculatorController::class, 'calculate']);
Route::post('/obtener-ciudad-gps', [\App\Http\Controllers\ShippingCalculatorController::class, 'obtenerCiudadGps'])->name('web.gps');
Route::post('/web/gps', [\App\Http\Controllers\ShippingCalculatorController::class, 'obtenerCiudadGps']); // Alias de emergencia
Route::get('/producto-prueba/{id}', [\App\Http\Controllers\ShippingCalculatorController::class, 'testView'])->name('web.producto.test');

