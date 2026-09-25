# Registro y Acceso Seguro de Estudiantes

Proyecto en PHP puro que implementa un flujo completo de **registro, autenticación y panel protegido**, aplicando las tres unidades de la guía *Validación, Seguridad y Sesiones en PHP*:

- Validación completa en el servidor (presencia, tipo, formato, longitud, rango, lista blanca, consistencia).
- Contraseñas con `password_hash()` / `password_verify()`.
- Consultas preparadas con PDO (anti inyección SQL).
- Protección anti-XSS con `htmlspecialchars()` en toda salida.
- Token CSRF (`random_bytes()` + `hash_equals()`) en todos los formularios.
- Sesiones seguras: `session_regenerate_id(true)` al autenticar, cookies `HttpOnly`/`SameSite`, expiración por inactividad y cierre de sesión completo.

## Estructura

```
.
├── sql/
│   └── base_datos.sql        # Esquema de la base de datos (tabla estudiantes)
├── src/
│   ├── config_sesion.php     # Configuración segura de la sesión (incluir primero en cada página)
│   ├── conexion.php          # Conexión PDO reutilizable
│   ├── Validador.php         # Clase reutilizable de validación (interfaz fluida)
│   ├── registro.php          # Formulario de registro de estudiantes
│   ├── login.php             # Formulario de inicio de sesión
│   ├── panel.php             # Página protegida (requiere sesión activa)
│   └── logout.php            # Cierre de sesión (solo por POST)
└── README.md
```

## Requisitos

- PHP 8.x
- MySQL o MariaDB
- Extensión PDO MySQL habilitada

## Cómo ejecutarlo

1. Crear la base de datos importando `sql/base_datos.sql`:

   ```bash
   mysql -u root -p < sql/base_datos.sql
   ```

2. Ajustar las credenciales de conexión en `src/conexion.php` si es necesario (usuario, contraseña, host).

3. Levantar el servidor embebido de PHP desde la carpeta `src`:

   ```bash
   php -S localhost:8000 -t src
   ```

4. Abrir [http://localhost:8000/registro.php](http://localhost:8000/registro.php), crear una cuenta y luego iniciar sesión en `login.php`.

## Flujo de seguridad implementado

| Amenaza | Defensa aplicada |
|---|---|
| XSS | `htmlspecialchars()` al mostrar cualquier dato dinámico |
| Inyección SQL | Consultas preparadas con PDO (`prepare()` + `execute()`) |
| Contraseñas débiles | `password_hash()` con `PASSWORD_DEFAULT` (bcrypt) |
| CSRF | Token de 32 bytes aleatorios comparado con `hash_equals()` |
| Fijación de sesión | `session_regenerate_id(true)` inmediatamente tras autenticar |
| Secuestro de sesión | Cookies `HttpOnly`, `SameSite=Strict` y `secure` cuando hay HTTPS |
| Sesión abandonada | Expiración automática por inactividad (15 minutos) y logout por POST |

## Criterios cubiertos (práctica de la guía)

- `registro.php`: valida nombre, correo, carrera (lista blanca) y contraseña (8–64 caracteres, confirmación) con mensajes por campo.
- Guarda al estudiante con PDO + `password_hash()`, y rechaza correos duplicados.
- `login.php`: autentica con `password_verify()` y regenera el ID de sesión.
- `panel.php`: protegido por sesión, muestra nombre, carrera y contador de visitas en la sesión activa.
- `logout.php`: destruye la sesión completa vía POST con verificación CSRF.
- Todos los formularios incluyen token CSRF y toda salida usa `htmlspecialchars()`.
