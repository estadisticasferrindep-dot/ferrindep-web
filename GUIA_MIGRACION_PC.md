# 🌍 GUÍA DE MIGRACIÓN: Antigravity + MercadoLibre Scripts

**¡HOLA FUTURO AG O USUARIO!** 👋
Si estás leyendo esto, es porque estás instalando este entorno en una **Nueva Computadora**.
Este archivo viaja junto con el código en Google Drive, así que aquí tienes todo lo necesario para arrancar.

---

## 🛠️ PASO 1: Instalación de Software (Para el Usuario)
En la computadora nueva, instalá esto (solo una vez):

1.  **Node.js (Entorno de Ejecución)**
    *   Descargar: [https://nodejs.org/](https://nodejs.org/) (Bajá la versión "LTS").
    *   Instalar: Todo "Siguiente", "Siguiente".

2.  **VS Code (Editor de Código)**
    *   Descargar: [https://code.visualstudio.com/](https://code.visualstudio.com/)

---

## ⚡ PASO 2: Activación del Proyecto (Para Antigravity o Usuario)
Las "librerías" (el código que hace la magia de conectar a Google y ML) no siempre se copian bien entre compus. Hay que "reinstalarlas" (es automático).

**Instrucción para Antigravity:**
> *"Agent, por favor ejecutá `npm install` en la carpeta `google-sheets-worker` para restaurar las dependencias."*

**Instrucción Manual (si lo hacés vos):**
1.  Abrí la carpeta `google-sheets-worker` en la terminal.
2.  Escribí: `npm install`
3.  Dale Enter y esperá que termine.

---

## 🔑 PASO 3: Credenciales (Automático)
Si sincronizaste esta carpeta con Google Drive, **YA ESTÁS LISTO**.
Los siguientes archivos deberían estar aquí y contienen tus llaves de acceso:

*   `google-sheets-worker/ml_token.txt` (Token ML)
*   `google-sheets-worker/ml_refresh_token.txt` (Renovación ML)
*   `google-sheets-worker/credentials.json` (Acceso a Google Sheets)
*   `google-sheets-worker/.env` (Configuraciones)

✅ **No hace falta loguearse de nuevo.** Antigravity leerá estos archivos y conectará de inmediato.

---

## 🚀 ¿Cómo probar que anda?
Pedile a Antigravity:
*"Chequeá si tenés conexión con MercadoLibre y la Hoja de Cálculo."*

(Si responde OK, la migración fue un éxito).
