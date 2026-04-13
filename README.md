[README.md](https://github.com/user-attachments/files/26668659/README.md)
ELAMITECH

Web para consultoría tecnológica enfocada en optimización, minimalismo y seguridad.

## Tecnologías y Características
- **Backend:** PHP nativo
- **Base de Datos:** MySQL
- **Frontend:** HTML5 / CSS3 / Bootstrap 5 (Estilo mate y minimalista basado en Catppuccin)
- **Infraestructura:** Docker & Docker Compose
- **Roles:** Administrador, Empleado, Usuario.

## Despliegue con Docker
1. Asegúrate de tener **Docker** y **Docker Compose** instalados.
2. Clona este repositorio y navega a su carpeta.
3. Ejecuta `docker-compose up -d --build`.
4. La aplicación estará corriendo de manera local en `http://localhost:8080`.

## Creación del Administrador Inicial
En la instalación inicial, existe un usuario administrador por defecto cargado en la base de datos:
- **Correo:** admin@belamitech.com
- **Contraseña:** password
 *(Asegúrate de cambiarla tan pronto inicies sesión en producción).*

## Escalabilidad e Implementación con Cloudflare Tunnels
Para escalar horizontalmente (montando el contenedor en múltiples servidores y balanceando la carga) y protegerlo directamente desde el CDN sin exponer puertos en tu máquina, usamos Cloudflare Tunnels:

1. Instala el demonio `cloudflared` en la máquina host donde corre Docker.
2. Autentica tu cuenta: `cloudflared tunnel login`.
3. Crea un túnel: `cloudflared tunnel create belamitech-tunnel`.
4. Enruta el tráfico mapeando `http://localhost:8080` a tu subdominio (ej: `app.belamitech.com`) en el Dashboard de Zero Trust de Cloudflare.
5. Ejecuta el túnel localmente de forma segura: `cloudflared tunnel run belamitech-tunnel`.
