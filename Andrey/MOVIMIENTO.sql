CREATE DATABASE IF NOT EXISTS novabank_db 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE novabank_db;

CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    curp VARCHAR(18) UNIQUE NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    direccion VARCHAR(255),
    codigo_cic VARCHAR(20) UNIQUE,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contrasena_hash VARCHAR(255) NOT NULL, 
    rol ENUM('cliente', 'admin') DEFAULT 'cliente',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
) ENGINE=InnoDB;



CREATE TABLE CUENTA (
    id_cuenta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    numero_cuenta VARCHAR(20) UNIQUE NOT NULL,
    tipo_cuenta ENUM('ahorro', 'cheques') NOT NULL,
    saldo DECIMAL(15, 2) DEFAULT 0.00,
    estado ENUM('activa', 'bloqueada') NOT NULL,
    fecha_apertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE TARJETA_DEBITO (
    id_tarjeta_debito INT AUTO_INCREMENT PRIMARY KEY,
    id_cuenta INT NOT NULL,
    numero_tarjeta_debito VARCHAR(16) UNIQUE NOT NULL,
    fecha_expiracion DATE NOT NULL,
    cvv_hash VARCHAR(255) NOT NULL,
    pin_hash VARCHAR(255) NOT NULL,
    estado ENUM('activa', 'bloqueada', 'vencida') DEFAULT 'activa',
    tipo_red ENUM('visa', 'mastercard') NOT NULL,
    FOREIGN KEY (id_cuenta) REFERENCES CUENTA(id_cuenta) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE TARJETA_CREDITO (
    id_tarjeta_credito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    numero_tarjeta_credito VARCHAR(16) UNIQUE NOT NULL,
    limite_credito DECIMAL(15, 2) NOT NULL,
    saldo_utilizado DECIMAL(15, 2) DEFAULT 0.00,
    dia_corte INT NOT NULL CHECK (dia_corte BETWEEN 1 AND 31),
    tasa_interes DECIMAL(5, 2) NOT NULL,
    fecha_expiracion DATE NOT NULL,
    cvv_hash VARCHAR(255) NOT NULL,
    estado ENUM('activa', 'bloqueada', 'vencida','inactiva') DEFAULT 'inactiva',
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE MOVIMIENTO (

    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_cuenta INT NOT NULL,
    tipo_movimiento ENUM(
        'deposito',
        'transferencia',
        'pago'
    ) NOT NULL,
    monto DECIMAL(15,2) NOT NULL,
    descripcion VARCHAR(255),
    fecha_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cuenta)
    REFERENCES CUENTA(id_cuenta)
    ON DELETE CASCADE
) ENGINE=InnoDB;