CREATE TABLE TRANSACCION (
    id_transaccion INT AUTO_INCREMENT PRIMARY KEY,
    id_cuenta_origen INT NOT NULL,
    id_cuenta_destino INT NULL, -- Solo se llena si es transferencia (Andrey)
    tipo_movimiento ENUM('deposito', 'retiro', 'transferencia') NOT NULL,
    monto DECIMAL(15, 2) NOT NULL,
    fecha_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
    descripcion VARCHAR(255),
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (id_cuenta_origen) REFERENCES CUENTA(id_cuenta),
    FOREIGN KEY (id_cuenta_destino) REFERENCES CUENTA(id_cuenta)
) ENGINE=InnoDB;
