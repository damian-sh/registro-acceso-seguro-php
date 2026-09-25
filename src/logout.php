<?php

declare(strict_types=1);

require __DIR__ . '/config_sesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();

    $_SESSION = [];

    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );

    session_destroy();
}

header('Location: login.php');
exit;
