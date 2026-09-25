<?php

declare(strict_types=1);

require __DIR__ . '/config_sesion.php';
require __DIR__ . '/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: panel.php');
    exit;
}

$error  = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();

    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $clave === '') {
        $error = 'Ingrese un correo válido y su contraseña.';
    } else {
        $stmt = conectar()->prepare(
            'SELECT id, nombre, carrera, clave_hash FROM estudiantes WHERE correo = ?'
        );
        $stmt->execute([$correo]);
        $estudiante = $stmt->fetch();

        if ($estudiante && password_verify($clave, $estudiante['clave_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id']     = $estudiante['id'];
            $_SESSION['nombre']         = $estudiante['nombre'];
            $_SESSION['carrera']        = $estudiante['carrera'];
            $_SESSION['visitas_panel']  = 0;
            header('Location: panel.php');
            exit;
        }

        $error = 'Credenciales incorrectas.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>
<body>
    <h1>Iniciar sesión</h1>

    <?php if (isset($_GET['expirada'])): ?>
        <p>Su sesión expiró por inactividad.</p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color:red"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <p>Correo: <input type="email" name="correo" required value="<?= e($correo) ?>"></p>
        <p>Contraseña: <input type="password" name="clave" required></p>
        <button type="submit">Entrar</button>
    </form>

    <p>¿No tiene cuenta? <a href="registro.php">Registrarse</a></p>
</body>
</html>
