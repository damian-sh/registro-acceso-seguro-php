-- Base de datos para el proyecto "Registro y Acceso Seguro de Estudiantes"
CREATE DATABASE IF NOT EXISTS clase_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clase_web;

CREATE TABLE IF NOT EXISTS estudiantes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(50)  NOT NULL,
    correo     VARCHAR(100) NOT NULL UNIQUE,
    carrera    VARCHAR(5)   NOT NULL,
    clave_hash VARCHAR(255) NOT NULL,
    creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
