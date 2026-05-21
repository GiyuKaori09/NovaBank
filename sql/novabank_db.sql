-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-05-2026 a las 05:49:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `novabank_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE `cuenta` (
  `id_cuenta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `numero_cuenta` varchar(20) NOT NULL,
  `tipo_cuenta` enum('debito','credito','ahorro') DEFAULT 'debito',
  `saldo` decimal(12,2) DEFAULT 0.00,
  `estado` enum('activa','bloqueada','cerrada') DEFAULT 'activa',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuenta`
--

INSERT INTO `cuenta` (`id_cuenta`, `id_usuario`, `numero_cuenta`, `tipo_cuenta`, `saldo`, `estado`, `fecha_creacion`) VALUES
(1, 2, '9317461918', 'debito', 2424.00, 'activa', '2026-05-09 22:56:22'),
(2, 3, '9573620148', 'debito', 2000.00, 'activa', '2026-05-09 23:12:22'),
(3, 2, '8114278178', 'credito', 5000.00, 'activa', '2026-05-10 01:21:48'),
(4, 4, '3470224072', 'debito', 0.00, 'activa', '2026-05-10 03:12:53'),
(5, 4, '3504805414', 'credito', 5000.00, 'activa', '2026-05-10 03:13:52'),
(6, 5, '3816949813', 'debito', 576.00, 'activa', '2026-05-12 15:03:25'),
(7, 5, '6026427664', 'credito', 5000.00, 'activa', '2026-05-12 15:05:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `id_movimiento` int(11) NOT NULL,
  `id_cuenta_origen` int(11) DEFAULT NULL,
  `id_cuenta_destino` int(11) DEFAULT NULL,
  `tipo_movimiento` enum('deposito','retiro','transferencia') NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimiento`
--

INSERT INTO `movimiento` (`id_movimiento`, `id_cuenta_origen`, `id_cuenta_destino`, `tipo_movimiento`, `monto`, `descripcion`, `fecha_movimiento`) VALUES
(5, 1, 2, 'transferencia', 1000.00, 'Transferencia bancaria', '2026-05-10 02:24:24'),
(6, 2, 1, 'transferencia', 500.00, 'Transferencia bancaria', '2026-05-10 02:25:01'),
(7, 4, 1, 'transferencia', 100.00, 'Transferencia bancaria', '2026-05-10 03:14:42'),
(8, 6, 1, 'transferencia', 324.00, 'Transferencia bancaria', '2026-05-12 15:06:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion`
--

CREATE TABLE `transaccion` (
  `id_transaccion` int(11) NOT NULL,
  `id_cuenta_origen` int(11) NOT NULL,
  `id_cuenta_destino` int(11) DEFAULT NULL,
  `tipo_movimiento` enum('deposito','retiro','transferencia') NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp(),
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT 0.00,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activo','bloqueado') DEFAULT 'activo',
  `curp` varchar(18) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre_completo`, `correo`, `contrasena_hash`, `saldo`, `fecha_registro`, `estado`, `curp`, `fecha_nacimiento`, `direccion`) VALUES
(2, 'Andrey Rmz', 'magagiarados@gmail.com', '$2y$10$oLOKzgVxT3dJS7iuZ2qrsuDYsdEymC6Gg6XB5jHeX4KeGHBtIUo42', 0.00, '2026-05-09 22:56:22', 'activo', 'PERA050412HOCRMNA9', '2005-04-12', 'Quiches'),
(3, 'Fulano 2', 'alguien@gmail.com', '$2y$10$cvy.dcUlP2arzRU6TSfA3ORiRxUr9qkUHaOZC/svQVaRpMEOmuhM6', 0.00, '2026-05-09 23:12:22', 'activo', 'WOVU960322MSRNKT31', '2026-05-22', 'Lomas'),
(4, 'Paulina Rmz', 'cool3250@hotmal.com', '$2y$10$Xj8g3ABSKlJgy0jwEw4InO3J9Tv3BfkDOBdybFArJEIPo4BgpGPfa', 0.00, '2026-05-10 03:12:53', 'activo', 'RAMV891029MOCMRL01', '1989-10-29', 'Quiches'),
(5, 'Danae Ramírez Camacho', 'danae@gmail.com', '$2y$10$Kf8Rn4T2BVY3c040SKOL3.yOezAQd.va5v6agaQnWHuCeu9UfC.QK', 0.00, '2026-05-12 15:03:25', 'activo', 'RACD050623MDFMMNA7', '2005-06-23', 'LOMAS DE TEPEMEPECATL');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD UNIQUE KEY `numero_cuenta` (`numero_cuenta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_cuenta_origen` (`id_cuenta_origen`),
  ADD KEY `id_cuenta_destino` (`id_cuenta_destino`);

--
-- Indices de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`id_transaccion`),
  ADD KEY `id_cuenta_origen` (`id_cuenta_origen`),
  ADD KEY `id_cuenta_destino` (`id_cuenta_destino`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  MODIFY `id_transaccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD CONSTRAINT `cuenta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `movimiento_ibfk_1` FOREIGN KEY (`id_cuenta_origen`) REFERENCES `cuenta` (`id_cuenta`),
  ADD CONSTRAINT `movimiento_ibfk_2` FOREIGN KEY (`id_cuenta_destino`) REFERENCES `cuenta` (`id_cuenta`);

--
-- Filtros para la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `transaccion_ibfk_1` FOREIGN KEY (`id_cuenta_origen`) REFERENCES `cuenta` (`id_cuenta`),
  ADD CONSTRAINT `transaccion_ibfk_2` FOREIGN KEY (`id_cuenta_destino`) REFERENCES `cuenta` (`id_cuenta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
