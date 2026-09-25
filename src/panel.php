<?php

declare(strict_types=1);

require __DIR__ . '/config_sesion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$_SESSION['visitas_panel'] = ($_SESSION['visitas_panel'] ?? 0) + 1;

$nombre  = $_SESSION['nombre'];
$carrera = $_SESSION['carrera'];
$visitas = $_SESSION['visitas_panel'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del estudiante</title>
</head>
<body>
    <h1>Bienvenido(a), <?= e($nombre) ?></h1>
    <p>Carrera: <?= e($carrera) ?></p>
    <p>Ha visitado este panel <?= (int) $visitas ?> veces durante esta sesión.</p>

    <form method="post" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
