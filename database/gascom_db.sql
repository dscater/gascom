-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 05-10-2026 a las 16:04:08
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gascom_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracions`
--

CREATE TABLE `configuracions` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre_sistema` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actividad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracions`
--

INSERT INTO `configuracions` (`id`, `nombre_sistema`, `alias`, `razon_social`, `nit`, `dir`, `fono`, `actividad`, `correo`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'LUDESA', 'LD', 'LUDESA S.A.', '1111111111', 'LOS OLIVOS #111', '67676767', 'ACTIVIDAD', 'correo@gmail.com', '11787767543.png', '2026-02-16 22:21:27', '2026-08-26 18:08:27'),
(2, 'LUDESA', 'LD', 'LUDESA S.A.', '1111111111', 'LOS OLIVOS #111', '67676767', 'ACTIVIDAD', 'correo@gmail.com', '11787767543.png', '2026-02-16 22:21:27', '2026-08-26 18:08:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'LUZ', 'PAGO DE FACTURA DE LUZ', '2026-10-05 14:43:44', '2026-10-05 14:43:44'),
(2, 'AGUA', '', '2026-10-05 14:43:54', '2026-10-05 14:43:54'),
(3, 'INTERNET ENTEL', '', '2026-10-05 14:44:03', '2026-10-05 14:44:03'),
(4, 'GAS 1', '', '2026-10-05 14:44:20', '2026-10-05 14:44:20'),
(5, 'GAS 2', '', '2026-10-05 14:44:29', '2026-10-05 14:44:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_accions`
--

CREATE TABLE `historial_accions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `accion` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_original` json DEFAULT NULL,
  `datos_nuevo` json DEFAULT NULL,
  `modulo` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_accions`
--

INSERT INTO `historial_accions` (`id`, `user_id`, `accion`, `descripcion`, `datos_original`, `datos_nuevo`, `modulo`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 1, \"nombre\": \"LUZ\", \"created_at\": \"2026-10-05T14:43:44.000000Z\", \"updated_at\": \"2026-10-05T14:43:44.000000Z\", \"descripcion\": \"PAGO DE FACTURA DE LUZ\"}', NULL, 'GASTOS', '2026-10-05', '10:43:45', '2026-10-05 14:43:45', '2026-10-05 14:43:45'),
(2, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 2, \"nombre\": \"AGUA\", \"created_at\": \"2026-10-05T14:43:54.000000Z\", \"updated_at\": \"2026-10-05T14:43:54.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-05', '10:43:54', '2026-10-05 14:43:54', '2026-10-05 14:43:54'),
(3, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 3, \"nombre\": \"INTERNET ENTEL\", \"created_at\": \"2026-10-05T14:44:03.000000Z\", \"updated_at\": \"2026-10-05T14:44:03.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-05', '10:44:03', '2026-10-05 14:44:03', '2026-10-05 14:44:03'),
(4, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 4, \"nombre\": \"GAS 1\", \"created_at\": \"2026-10-05T14:44:20.000000Z\", \"updated_at\": \"2026-10-05T14:44:20.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-05', '10:44:20', '2026-10-05 14:44:20', '2026-10-05 14:44:20'),
(5, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 5, \"nombre\": \"GAS 2\", \"created_at\": \"2026-10-05T14:44:29.000000Z\", \"updated_at\": \"2026-10-05T14:44:29.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-05', '10:44:29', '2026-10-05 14:44:29', '2026-10-05 14:44:29'),
(6, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE', '{\"id\": 1, \"nombre\": \"JUAN PABLO\", \"created_at\": \"2026-10-05T14:46:05.000000Z\", \"updated_at\": \"2026-10-05T14:46:05.000000Z\", \"descripcion\": \"\"}', NULL, 'PARTICIPANTES', '2026-10-05', '10:46:05', '2026-10-05 14:46:05', '2026-10-05 14:46:05'),
(7, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE', '{\"id\": 2, \"nombre\": \"EDWIN\", \"created_at\": \"2026-10-05T14:46:11.000000Z\", \"updated_at\": \"2026-10-05T14:46:11.000000Z\", \"descripcion\": \"\"}', NULL, 'PARTICIPANTES', '2026-10-05', '10:46:11', '2026-10-05 14:46:11', '2026-10-05 14:46:11'),
(8, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE', '{\"id\": 3, \"nombre\": \"GONZALO\", \"created_at\": \"2026-10-05T14:46:17.000000Z\", \"updated_at\": \"2026-10-05T14:46:17.000000Z\", \"descripcion\": \"\"}', NULL, 'PARTICIPANTES', '2026-10-05', '10:46:17', '2026-10-05 14:46:17', '2026-10-05 14:46:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_31_165641_create_configuracions_table', 1),
(2, '2024_11_02_153317_create_users_table', 1),
(3, '2024_11_02_153318_create_historial_accions_table', 1),
(4, '2026_10_05_101457_create_gastos_table', 1),
(5, '2026_10_05_101501_create_participantes_table', 1),
(6, '2026_10_05_101612_create_pagos_table', 1),
(7, '2026_10_05_101613_create_pago_detalles_table', 1),
(8, '2026_10_05_101615_create_pago_participantes_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint UNSIGNED NOT NULL,
  `mes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio` int NOT NULL,
  `total` decimal(24,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_detalles`
--

CREATE TABLE `pago_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `pago_id` bigint UNSIGNED NOT NULL,
  `gasto_id` bigint UNSIGNED NOT NULL,
  `monto` decimal(24,2) NOT NULL DEFAULT '0.00',
  `fecha` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_participantes`
--

CREATE TABLE `pago_participantes` (
  `id` bigint UNSIGNED NOT NULL,
  `pago_id` bigint UNSIGNED NOT NULL,
  `gasto_id` bigint UNSIGNED NOT NULL,
  `pago_detalle_id` bigint UNSIGNED NOT NULL,
  `participante_id` bigint UNSIGNED NOT NULL,
  `monto` decimal(24,2) NOT NULL DEFAULT '0.00',
  `porcentaje` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `participantes`
--

INSERT INTO `participantes` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'JUAN PABLO', '', '2026-10-05 14:46:05', '2026-10-05 14:46:05'),
(2, 'EDWIN', '', '2026-10-05 14:46:11', '2026-10-05 14:46:11'),
(3, 'GONZALO', '', '2026-10-05 14:46:17', '2026-10-05 14:46:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `usuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paterno` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materno` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ci` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_exp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acceso` int NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `usuario`, `nombre`, `paterno`, `materno`, `ci`, `ci_exp`, `dir`, `correo`, `fono`, `password`, `acceso`, `tipo`, `foto`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin', '', '0', '', '', '', '', '$2y$12$65d4fgZsvBV5Lc/AxNKh4eoUdbGyaczQ4sSco20feSQANshNLuxSC', 1, 'ADMINISTRADOR', NULL, '2026-08-26', 1, '2026-02-17 22:21:27', '2026-02-17 22:21:27');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gastos_nombre_unique` (`nombre`);

--
-- Indices de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historial_accions_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pago_detalles`
--
ALTER TABLE `pago_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_detalles_pago_id_foreign` (`pago_id`),
  ADD KEY `pago_detalles_gasto_id_foreign` (`gasto_id`);

--
-- Indices de la tabla `pago_participantes`
--
ALTER TABLE `pago_participantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_participantes_pago_id_foreign` (`pago_id`),
  ADD KEY `pago_participantes_gasto_id_foreign` (`gasto_id`),
  ADD KEY `pago_participantes_pago_detalle_id_foreign` (`pago_detalle_id`),
  ADD KEY `pago_participantes_participante_id_foreign` (`participante_id`);

--
-- Indices de la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participantes_nombre_unique` (`nombre`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago_detalles`
--
ALTER TABLE `pago_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago_participantes`
--
ALTER TABLE `pago_participantes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD CONSTRAINT `historial_accions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pago_detalles`
--
ALTER TABLE `pago_detalles`
  ADD CONSTRAINT `pago_detalles_gasto_id_foreign` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`),
  ADD CONSTRAINT `pago_detalles_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`);

--
-- Filtros para la tabla `pago_participantes`
--
ALTER TABLE `pago_participantes`
  ADD CONSTRAINT `pago_participantes_gasto_id_foreign` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`),
  ADD CONSTRAINT `pago_participantes_pago_detalle_id_foreign` FOREIGN KEY (`pago_detalle_id`) REFERENCES `pago_detalles` (`id`),
  ADD CONSTRAINT `pago_participantes_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`),
  ADD CONSTRAINT `pago_participantes_participante_id_foreign` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
