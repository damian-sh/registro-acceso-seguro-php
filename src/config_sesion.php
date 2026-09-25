<?php

declare(strict_types=1);

/**
 * Configuración segura de la sesión.
 * Incluir SIEMPRE de primero, antes de cualquier salida HTML.
 */

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

// Expiración por inactividad: 15 minutos.
const LIMITE_INACTIVIDAD_SEGUNDOS = 15 * 60;

if (
    isset($_SESSION['usuario_id'], $_SESSION['ultimo_acceso'])
    && (time() - $_SESSION['ultimo_acceso']) > LIMITE_INACTIVIDAD_SEGUNDOS
) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php?expirada=1');
    exit;
}

$_SESSION['ultimo_acceso'] = time();

// Token CSRF único por sesión, disponible para todas las páginas.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/** Escapa texto para mostrarlo de forma segura en HTML (anti-XSS). */
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/** Verifica el token CSRF recibido por POST contra el de la sesión. */
function verificarCsrf(): void
{
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }
}
