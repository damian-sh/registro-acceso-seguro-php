# Registro y Acceso Seguro de Estudiantes

Aplicación PHP con registro, autenticación y panel protegido. Incluye validación server-side, PDO con consultas preparadas, `password_hash()`, protección XSS/CSRF y sesiones seguras.

## Ejecutar con Docker (recomendado)

Requisitos: Docker y Docker Compose.

```bash
docker compose up --build -d
```

Abrir:

```text
http://localhost:8000/registro.php
```

En GitHub Codespaces, abre el puerto `8000` desde la pestaña **Ports** y selecciona **Open in Browser**.

La base de datos MariaDB se inicializa automáticamente con `sql/base_datos.sql`. Los datos persisten en el volumen `db_data`.

Comandos útiles:

```bash
# Ver logs de la aplicación
docker compose logs -f app

# Ver logs de la base de datos
docker compose logs -f db

# Ver estado
docker compose ps

# Detener contenedores sin borrar datos
docker compose down

# Detener y borrar también la base de datos
docker compose down -v
```

Credenciales internas de desarrollo:

```text
DB_HOST=db
DB_NAME=clase_web
DB_USER=app
DB_PASSWORD=app_secret
```

No uses estas credenciales en producción.

## Ejecutar sin Docker

```bash
php -S localhost:8000 -t src
```

En ese caso necesitas PHP 8.x, MySQL/MariaDB y la extensión `pdo_mysql`. Ajusta `src/conexion.php` si tus credenciales son distintas.

## Estructura

```text
.
├── Dockerfile
├── docker-compose.yml
├── docker/
│   └── php.ini
├── sql/
│   └── base_datos.sql
└── src/
    ├── config_sesion.php
    ├── conexion.php
    ├── Validador.php
    ├── registro.php
    ├── login.php
    ├── panel.php
    └── logout.php
```

## Flujo de seguridad

| Amenaza | Defensa |
|---|---|
| XSS | `htmlspecialchars()` en la salida HTML |
| Inyección SQL | Consultas preparadas con PDO |
| Contraseñas expuestas | `password_hash()` y `password_verify()` |
| CSRF | Token aleatorio validado con `hash_equals()` |
| Fijación de sesión | `session_regenerate_id(true)` al iniciar sesión |
| Secuestro de sesión | Cookies `HttpOnly`, `SameSite=Strict` y `Secure` bajo HTTPS |
| Sesión abandonada | Expiración por inactividad y logout por POST |

## Pruebas de la guía

1. Registro vacío y datos inválidos: deben aparecer errores por campo.
2. Login con `' OR 1=1 --`: debe devolver `Credenciales incorrectas.`.
3. Panel protegido: sin login debe redirigir a `login.php`.
4. Recargar el panel: el contador de visitas debe aumentar.
5. Revisar `PHPSESSID` en DevTools: debe tener `HttpOnly`.

La guía solicita tres capturas: errores de validación, inyección SQL rechazada y panel con contador.
