<?php

declare(strict_types=1);

require __DIR__ . '/config_sesion.php';
require __DIR__ . '/conexion.php';
require __DIR__ . '/Validador.php';

$carreras = [
    'ISI' => 'Ing. en Sistemas Informáticos',
    'IDS' => 'Ing. en Desarrollo de Software',
    'ARQ' => 'Arquitectura',
];

$errores = [];
$exito   = '';
$nombre  = $correo = $carrera = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();

    $nombre  = trim($_POST['nombre'] ?? '');
    $correo  = trim($_POST['correo'] ?? '');
    $carrera = $_POST['carrera'] ?? '';
    $clave   = $_POST['clave'] ?? '';
    $clave2  = $_POST['clave2'] ?? '';

    $v = new Validador();
    $v->requerido('nombre', $nombre)
      ->longitud('nombre', $nombre, 3, 50)
      ->soloLetras('nombre', $nombre)
      ->requerido('correo', $correo)
      ->email('correo', $correo)
      ->requerido('carrera', $carrera)
      ->listaBlanca('carrera', $carrera, array_keys($carreras))
      ->requerido('clave', $clave)
      ->longitud('clave', $clave, 8, 64)
      ->iguales('clave2', $clave, $clave2, 'Las contraseñas no coinciden.');

    if ($v->esValido()) {
        $stmt = conectar()->prepare('SELECT id FROM estudiantes WHERE correo = ?');
        $stmt->execute([$correo]);

        if ($stmt->fetch()) {
            $errores['correo'] = 'Ese correo ya está registrado.';
        } else {
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $ins  = conectar()->prepare(
                'INSERT INTO estudiantes (nombre, correo, carrera, clave_hash) VALUES (?, ?, ?, ?)'
            );
            $ins->execute([$nombre, $correo, $carrera, $hash]);

            $exito  = 'Registro exitoso. Ya puede iniciar sesión.';
            $nombre = $correo = $carrera = '';
        }
    } else {
        $errores = $v->getErrores();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de estudiantes</title>
</head>
<body>
    <h1>Registro de estudiantes</h1>

    <?php if ($exito): ?>
        <p style="color:green"><?= e($exito) ?> <a href="login.php">Iniciar sesión</a></p>
    <?php endif; ?>

    <form method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

        <p>
            Nombre: <input type="text" name="nombre" value="<?= e($nombre) ?>">
            <small style="color:red"><?= e($errores['nombre'] ?? '') ?></small>
        </p>
        <p>
            Correo: <input type="email" name="correo" value="<?= e($correo) ?>">
            <small style="color:red"><?= e($errores['correo'] ?? '') ?></small>
        </p>
        <p>
            Carrera:
            <select name="carrera">
                <option value="">-- Seleccione --</option>
                <?php foreach ($carreras as $cod => $txt): ?>
                    <option value="<?= e($cod) ?>" <?= $carrera === $cod ? 'selected' : '' ?>>
                        <?= e($txt) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small style="color:red"><?= e($errores['carrera'] ?? '') ?></small>
        </p>
        <p>
            Contraseña: <input type="password" name="clave">
            <small style="color:red"><?= e($errores['clave'] ?? '') ?></small>
        </p>
        <p>
            Confirmar contraseña: <input type="password" name="clave2">
            <small style="color:red"><?= e($errores['clave2'] ?? '') ?></small>
        </p>
        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tiene cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>
