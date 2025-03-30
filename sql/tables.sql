CREATE DATABASE IF NOT EXISTS cuentacuentos_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE cuentacuentos_db;

-- Eliminar tablas existentes para recrearlas
DROP TABLE IF EXISTS preferencias_usuarios;
DROP TABLE IF EXISTS cuentos;
DROP TABLE IF EXISTS colaboraciones;
DROP TABLE IF EXISTS estadisticas;
DROP TABLE IF EXISTS usuarios;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de preferencias de usuarios
CREATE TABLE preferencias_usuarios (
    usuario_id INT PRIMARY KEY,
    color_favorito VARCHAR(20),
    edad INT,
    altura FLOAT,
    peso FLOAT,
    genero ENUM('M', 'F'),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabla de cuentos
CREATE TABLE cuentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) UNIQUE NOT NULL,
    tema VARCHAR(100),
    pasos INT NOT NULL,
    pasos_restantes INT NOT NULL, -- Nuevo campo para los pasos restantes
    palabra_guia VARCHAR(50),
    creador_id INT NOT NULL,
    texto_completo TEXT DEFAULT NULL,
    estado ENUM('abierto', 'cerrado') DEFAULT 'abierto', -- Estado del cuento
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_fin TIMESTAMP NULL,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabla de colaboraciones
CREATE TABLE colaboraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fragmento TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cuento_id) REFERENCES cuentos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabla de estadísticas
CREATE TABLE estadisticas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuento_id INT NOT NULL,
    colaboraciones INT DEFAULT 0,
    media_edad FLOAT DEFAULT 0,
    media_peso FLOAT DEFAULT 0,
    media_altura FLOAT DEFAULT 0,
    color_comun VARCHAR(50),
    distribucion_genero JSON DEFAULT ('{}'),
    FOREIGN KEY (cuento_id) REFERENCES cuentos(id) ON DELETE CASCADE ON UPDATE CASCADE
);



