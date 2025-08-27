-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-04-2025 a las 11:51:51
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
-- Base de datos: `transportes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones_rutas`
--

CREATE TABLE `asignaciones_rutas` (
  `id_asignacion` int(11) NOT NULL,
  `id_camion` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `id_usuario_conductor` int(11) NOT NULL,
  `fecha_salida` datetime NOT NULL,
  `fecha_llegada_estimada` datetime NOT NULL,
  `fecha_llegada_real` datetime DEFAULT NULL,
  `id_estado_asignacion` int(11) NOT NULL DEFAULT 1,
  `carga_descripcion` text DEFAULT NULL,
  `peso_carga` decimal(10,2) DEFAULT NULL COMMENT 'en toneladas',
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignaciones_rutas`
--

INSERT INTO `asignaciones_rutas` (`id_asignacion`, `id_camion`, `id_ruta`, `id_usuario_conductor`, `fecha_salida`, `fecha_llegada_estimada`, `fecha_llegada_real`, `id_estado_asignacion`, `carga_descripcion`, `peso_carga`, `observaciones`, `fecha_registro`) VALUES
(1, 4, 1, 1, '2025-03-26 08:00:00', '2025-03-26 14:30:00', NULL, 1, 'Materiales de construcción', 30.00, NULL, '2025-03-26 20:44:39'),
(2, 8, 2, 1, '2025-03-26 09:00:00', '2025-03-26 14:45:00', NULL, 2, 'Productos alimenticios', 25.00, NULL, '2025-03-26 20:44:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `camiones`
--

CREATE TABLE `camiones` (
  `id_camion` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `capacidad` decimal(10,2) NOT NULL COMMENT 'en toneladas',
  `id_estado_camion` int(11) NOT NULL,
  `fecha_adquisicion` date NOT NULL,
  `ultima_revision` date DEFAULT NULL,
  `id_usuario_responsable` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `camiones`
--

INSERT INTO `camiones` (`id_camion`, `matricula`, `marca`, `modelo`, `capacidad`, `id_estado_camion`, `fecha_adquisicion`, `ultima_revision`, `id_usuario_responsable`, `fecha_registro`) VALUES
(1, 'ABC1234', 'Volvo', 'FH16', 40.00, 1, '2024-01-15', '2025-03-01', 1, '2025-03-26 20:44:39'),
(2, 'DEF5678', 'Scania', 'R500', 36.00, 1, '2023-11-20', '2025-02-15', 3, '2025-03-26 20:44:39'),
(3, 'GHI9012', 'Mercedes', 'Actros', 38.00, 2, '2024-03-10', '2025-01-20', NULL, '2025-03-26 20:44:39'),
(4, 'JKL3456', 'MAN', 'TGX', 42.00, 1, '2023-09-05', '2025-03-15', 4, '2025-03-26 20:44:39'),
(5, 'MNO7890', 'Iveco', 'S-Way', 36.50, 1, '2024-02-28', '2025-03-20', 4, '2025-03-26 20:44:39'),
(6, 'PQR1234', 'DAF', 'XF', 39.00, 4, '2022-12-10', '2024-12-01', NULL, '2025-03-26 20:44:39'),
(7, 'STU5678', 'Renault', 'T High', 37.50, 1, '2024-01-05', '2025-03-10', 3, '2025-03-26 20:44:39'),
(8, 'VWX9012', 'Volvo', 'FMX', 41.00, 3, '2023-10-15', '2025-02-28', 4, '2025-03-26 20:44:39'),
(9, 'YZA3456', 'Scania', 'S730', 43.00, 4, '2024-02-10', '2025-03-18', 3, '2025-03-26 20:44:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dificultades_ruta`
--

CREATE TABLE `dificultades_ruta` (
  `id_dificultad` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dificultades_ruta`
--

INSERT INTO `dificultades_ruta` (`id_dificultad`, `nombre`, `descripcion`) VALUES
(1, 'baja', 'Ruta con dificultad baja'),
(2, 'media', 'Ruta con dificultad media'),
(3, 'alta', 'Ruta con dificultad alta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_asignacion`
--

CREATE TABLE `estados_asignacion` (
  `id_estado_asignacion` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados_asignacion`
--

INSERT INTO `estados_asignacion` (`id_estado_asignacion`, `nombre`, `descripcion`) VALUES
(1, 'programada', 'La asignación está programada pero no ha comenzado'),
(2, 'en proceso', 'La asignación está en curso'),
(3, 'completada', 'La asignación se ha completado satisfactoriamente'),
(4, 'cancelada', 'La asignación ha sido cancelada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_camion`
--

CREATE TABLE `estados_camion` (
  `id_estado_camion` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados_camion`
--

INSERT INTO `estados_camion` (`id_estado_camion`, `nombre`, `descripcion`) VALUES
(1, 'disponibles', 'El camión está disponible para asignar'),
(2, 'en mantenimiento', 'El camión está en mantenimiento'),
(3, 'en ruta', 'El camión está actualmente en ruta'),
(4, 'inactivo', 'El camión no está activo en la flota');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_incidencia`
--

CREATE TABLE `estados_incidencia` (
  `id_estado_incidencia` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados_incidencia`
--

INSERT INTO `estados_incidencia` (`id_estado_incidencia`, `nombre`, `descripcion`) VALUES
(1, 'reportada', 'La incidencia ha sido reportada pero no atendida'),
(2, 'en proceso', 'La incidencia está siendo atendida'),
(3, 'resuelta', 'La incidencia ha sido resuelta'),
(4, 'cancelada', 'La incidencia ha sido cancelada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_ruta`
--

CREATE TABLE `estados_ruta` (
  `id_estado_ruta` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados_ruta`
--

INSERT INTO `estados_ruta` (`id_estado_ruta`, `nombre`, `descripcion`) VALUES
(1, 'activa', 'La ruta está activa y disponible'),
(2, 'inactiva', 'La ruta está inactiva temporalmente'),
(3, 'en mantenimiento', 'La ruta está en mantenimiento');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencias`
--

CREATE TABLE `incidencias` (
  `id_incidencia` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `id_tipo_incidencia` int(11) NOT NULL,
  `id_prioridad` int(11) NOT NULL DEFAULT 2,
  `id_estado_incidencia` int(11) NOT NULL DEFAULT 1,
  `id_camion` int(11) DEFAULT NULL,
  `id_ruta` int(11) DEFAULT NULL,
  `id_usuario_reporta` int(11) NOT NULL,
  `id_usuario_asignado` int(11) DEFAULT NULL,
  `fecha_reporte` datetime DEFAULT current_timestamp(),
  `fecha_resolucion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `incidencias`
--

INSERT INTO `incidencias` (`id_incidencia`, `titulo`, `descripcion`, `id_tipo_incidencia`, `id_prioridad`, `id_estado_incidencia`, `id_camion`, `id_ruta`, `id_usuario_reporta`, `id_usuario_asignado`, `fecha_reporte`, `fecha_resolucion`) VALUES
(1, 'Fallo de frenos', 'El camión presenta problemas con los frenos durante la ruta', 2, 2, 4, 1, 2, 4, 5, '2025-03-26 20:44:39', '2025-03-29 20:49:00'),
(2, 'Faros', 'Rotura de faros', 3, 3, 2, 1, 1, 1, 1, '2025-03-27 20:37:20', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles_prioridad`
--

CREATE TABLE `niveles_prioridad` (
  `id_prioridad` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `niveles_prioridad`
--

INSERT INTO `niveles_prioridad` (`id_prioridad`, `nombre`, `descripcion`) VALUES
(1, 'baja', 'Incidencia de baja prioridad'),
(2, 'media', 'Incidencia de prioridad media'),
(3, 'alta', 'Incidencia de alta prioridad'),
(4, 'critica', 'Incidencia crítica que requiere atención inmediata');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Usuario con acceso completo al sistema'),
(2, 'Supervisor', 'Usuario que supervisa las operaciones y asignaciones'),
(3, 'Conductor', 'Usuario que conduce los camiones'),
(4, 'Informatico', 'Usuario encargado de resolver incidencias técnicas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `id_ruta` int(11) NOT NULL,
  `origen` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `distancia` decimal(10,2) NOT NULL COMMENT 'en kilómetros',
  `tiempo_estimado` time NOT NULL,
  `id_dificultad` int(11) NOT NULL,
  `id_estado_ruta` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`id_ruta`, `origen`, `destino`, `distancia`, `tiempo_estimado`, `id_dificultad`, `id_estado_ruta`, `fecha_registro`) VALUES
(1, 'Murcia', 'Madrid', 630.00, '03:30:00', 3, 3, '2025-03-26 20:44:39'),
(2, 'Sevilla', 'Valencia', 540.00, '05:45:00', 1, 1, '2025-03-26 20:44:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_incidencia`
--

CREATE TABLE `tipos_incidencia` (
  `id_tipo_incidencia` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_incidencia`
--

INSERT INTO `tipos_incidencia` (`id_tipo_incidencia`, `nombre`, `descripcion`) VALUES
(1, 'mecanicas', 'Problemas mecánicos con el vehiculo'),
(2, 'logisticas', 'Problemas con la logística de la ruta'),
(3, 'climatica', 'Problemas causados por condiciones climáticas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password`, `id_rol`, `telefono`, `fecha_contratacion`, `fecha_registro`) VALUES
(1, 'Admin', 'admin@transportes.com', '$2y$10$925wnuIvYLBEsab8KdpauOCPpNdmJm9QfBEVkC7pxC7P/.Hgwc/qS', 1, '555123456', '2024-01-01', '2025-03-26 20:44:39'),
(2, 'Supervisor Juan', 'juan@transportes.com', '$2y$10$925wnuIvYLBEsab8KdpauOCPpNdmJm9QfBEVkC7pxC7P/.Hgwc/qS', 2, '555654321', '2024-01-15', '2025-03-26 20:44:39'),
(3, 'Conductor Pedro', 'pedro@transportes.com', '$2y$10$925wnuIvYLBEsab8KdpauOCPpNdmJm9QfBEVkC7pxC7P/.Hgwc/qS', 3, '555789123', '2024-02-01', '2025-03-26 20:44:39'),
(4, 'Conductor Maria', 'maria@transportes.com', '$2y$10$925wnuIvYLBEsab8KdpauOCPpNdmJm9QfBEVkC7pxC7P/.Hgwc/qS', 3, '555321654', '2024-02-10', '2025-03-26 20:44:39'),
(5, 'Informático Carlos', 'carlos@transportes.com', '$2y$10$925wnuIvYLBEsab8KdpauOCPpNdmJm9QfBEVkC7pxC7P/.Hgwc/qS', 4, '123456789', '2024-01-10', '2025-03-26 20:44:39'),
(6, 'Informático Ana', 'ana@transportes.com', '$2y$10$925wnuIvYLBEsab8KdpauOCPpNdmJm9QfBEVkC7pxC7P/.Hgwc/qS', 3, '987654321', '2024-02-15', '2025-03-26 20:44:39');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaciones_rutas`
--
ALTER TABLE `asignaciones_rutas`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_camion` (`id_camion`),
  ADD KEY `id_ruta` (`id_ruta`),
  ADD KEY `id_usuario_conductor` (`id_usuario_conductor`),
  ADD KEY `id_estado_asignacion` (`id_estado_asignacion`);

--
-- Indices de la tabla `camiones`
--
ALTER TABLE `camiones`
  ADD PRIMARY KEY (`id_camion`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD KEY `id_usuario_responsable` (`id_usuario_responsable`),
  ADD KEY `id_estado_camion` (`id_estado_camion`);

--
-- Indices de la tabla `dificultades_ruta`
--
ALTER TABLE `dificultades_ruta`
  ADD PRIMARY KEY (`id_dificultad`);

--
-- Indices de la tabla `estados_asignacion`
--
ALTER TABLE `estados_asignacion`
  ADD PRIMARY KEY (`id_estado_asignacion`);

--
-- Indices de la tabla `estados_camion`
--
ALTER TABLE `estados_camion`
  ADD PRIMARY KEY (`id_estado_camion`);

--
-- Indices de la tabla `estados_incidencia`
--
ALTER TABLE `estados_incidencia`
  ADD PRIMARY KEY (`id_estado_incidencia`);

--
-- Indices de la tabla `estados_ruta`
--
ALTER TABLE `estados_ruta`
  ADD PRIMARY KEY (`id_estado_ruta`);

--
-- Indices de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD PRIMARY KEY (`id_incidencia`),
  ADD KEY `id_camion` (`id_camion`),
  ADD KEY `id_ruta` (`id_ruta`),
  ADD KEY `id_usuario_reporta` (`id_usuario_reporta`),
  ADD KEY `id_usuario_asignado` (`id_usuario_asignado`),
  ADD KEY `id_estado_incidencia` (`id_estado_incidencia`),
  ADD KEY `id_tipo_incidencia` (`id_tipo_incidencia`),
  ADD KEY `id_prioridad` (`id_prioridad`);

--
-- Indices de la tabla `niveles_prioridad`
--
ALTER TABLE `niveles_prioridad`
  ADD PRIMARY KEY (`id_prioridad`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD PRIMARY KEY (`id_ruta`),
  ADD KEY `id_estado_ruta` (`id_estado_ruta`),
  ADD KEY `id_dificultad` (`id_dificultad`);

--
-- Indices de la tabla `tipos_incidencia`
--
ALTER TABLE `tipos_incidencia`
  ADD PRIMARY KEY (`id_tipo_incidencia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaciones_rutas`
--
ALTER TABLE `asignaciones_rutas`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `camiones`
--
ALTER TABLE `camiones`
  MODIFY `id_camion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `dificultades_ruta`
--
ALTER TABLE `dificultades_ruta`
  MODIFY `id_dificultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estados_asignacion`
--
ALTER TABLE `estados_asignacion`
  MODIFY `id_estado_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estados_camion`
--
ALTER TABLE `estados_camion`
  MODIFY `id_estado_camion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estados_incidencia`
--
ALTER TABLE `estados_incidencia`
  MODIFY `id_estado_incidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estados_ruta`
--
ALTER TABLE `estados_ruta`
  MODIFY `id_estado_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  MODIFY `id_incidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `niveles_prioridad`
--
ALTER TABLE `niveles_prioridad`
  MODIFY `id_prioridad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_incidencia`
--
ALTER TABLE `tipos_incidencia`
  MODIFY `id_tipo_incidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones_rutas`
--
ALTER TABLE `asignaciones_rutas`
  ADD CONSTRAINT `asignaciones_rutas_ibfk_1` FOREIGN KEY (`id_camion`) REFERENCES `camiones` (`id_camion`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asignaciones_rutas_ibfk_2` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asignaciones_rutas_ibfk_3` FOREIGN KEY (`id_usuario_conductor`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asignaciones_rutas_ibfk_4` FOREIGN KEY (`id_estado_asignacion`) REFERENCES `estados_asignacion` (`id_estado_asignacion`);

--
-- Filtros para la tabla `camiones`
--
ALTER TABLE `camiones`
  ADD CONSTRAINT `camiones_ibfk_1` FOREIGN KEY (`id_usuario_responsable`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `camiones_ibfk_2` FOREIGN KEY (`id_estado_camion`) REFERENCES `estados_camion` (`id_estado_camion`);

--
-- Filtros para la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD CONSTRAINT `incidencias_ibfk_1` FOREIGN KEY (`id_camion`) REFERENCES `camiones` (`id_camion`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `incidencias_ibfk_2` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `incidencias_ibfk_3` FOREIGN KEY (`id_usuario_reporta`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `incidencias_ibfk_4` FOREIGN KEY (`id_usuario_asignado`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `incidencias_ibfk_5` FOREIGN KEY (`id_estado_incidencia`) REFERENCES `estados_incidencia` (`id_estado_incidencia`),
  ADD CONSTRAINT `incidencias_ibfk_6` FOREIGN KEY (`id_tipo_incidencia`) REFERENCES `tipos_incidencia` (`id_tipo_incidencia`),
  ADD CONSTRAINT `incidencias_ibfk_7` FOREIGN KEY (`id_prioridad`) REFERENCES `niveles_prioridad` (`id_prioridad`);

--
-- Filtros para la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD CONSTRAINT `rutas_ibfk_1` FOREIGN KEY (`id_estado_ruta`) REFERENCES `estados_ruta` (`id_estado_ruta`),
  ADD CONSTRAINT `rutas_ibfk_2` FOREIGN KEY (`id_dificultad`) REFERENCES `dificultades_ruta` (`id_dificultad`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
