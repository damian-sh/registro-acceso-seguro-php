<?php

declare(strict_types=1);

/**
 * Clase de validación reutilizable con interfaz fluida.
 * Cada método devuelve $this para poder encadenar llamadas.
 */
class Validador
{
    private array $errores = [];

    public function requerido(string $campo, string $valor, string $mensaje = ''): self
    {
        if (trim($valor) === '' && !$this->tieneError($campo)) {
            $this->errores[$campo] = $mensaje ?: "El campo {$campo} es obligatorio.";
        }
        return $this;
    }

    public function email(string $campo, string $valor): self
    {
        if (!$this->tieneError($campo) && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $this->errores[$campo] = 'Ingrese un correo válido.';
        }
        return $this;
    }

    public function longitud(string $campo, string $valor, int $min, int $max): self
    {
        $n = mb_strlen(trim($valor));
        if (!$this->tieneError($campo) && ($n < $min || $n > $max)) {
            $this->errores[$campo] = "El campo {$campo} debe tener entre {$min} y {$max} caracteres.";
        }
        return $this;
    }

    public function soloLetras(string $campo, string $valor): self
    {
        if (!$this->tieneError($campo) && !preg_match('/^[\\p{L} ]+$/u', $valor)) {
            $this->errores[$campo] = 'Solo se permiten letras y espacios.';
        }
        return $this;
    }

    public function listaBlanca(string $campo, string $valor, array $permitidos): self
    {
        if (!$this->tieneError($campo) && !in_array($valor, $permitidos, true)) {
            $this->errores[$campo] = 'Seleccione una opción válida.';
        }
        return $this;
    }

    public function iguales(string $campo, string $a, string $b, string $mensaje = ''): self
    {
        if (!$this->tieneError($campo) && $a !== $b) {
            $this->errores[$campo] = $mensaje ?: 'Los valores no coinciden.';
        }
        return $this;
    }

    private function tieneError(string $campo): bool
    {
        return isset($this->errores[$campo]);
    }

    public function esValido(): bool
    {
        return empty($this->errores);
    }

    public function getErrores(): array
    {
        return $this->errores;
    }
}
