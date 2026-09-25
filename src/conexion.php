<?php

declare(strict_types=1);

/**
 * Crea y retorna una conexión PDO a la base de datos del proyecto.
 * Falla de forma segura: registra el detalle en el log y muestra
 * un mensaje genérico al usuario.
 */
function conectar(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=clase_web;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );

        return $pdo;
    } catch (PDOException $e) {
        error_log('Error de conexión a BD: ' . $e->getMessage());
        exit('Ocurrió un problema. Intente más tarde.');
    }
}
