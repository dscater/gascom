-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 07-10-2026 a las 15:52:51
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
  `qr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracions`
--

INSERT INTO `configuracions` (`id`, `nombre_sistema`, `alias`, `razon_social`, `nit`, `dir`, `fono`, `actividad`, `correo`, `logo`, `qr`, `created_at`, `updated_at`) VALUES
(1, 'Gascom', 'GC', 'GASCOM', '1111111111', 'LOS OLIVOS #111', '67676767', 'ACTIVIDAD', 'correo@gmail.com', '11787767543.png', '11791388078.jpeg', '2026-02-16 22:21:27', '2026-10-07 15:47:58');

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
(1, 'LUZ', '', '2026-10-06 15:02:54', '2026-10-06 15:02:54'),
(2, 'AGUA', '', '2026-10-06 15:02:59', '2026-10-06 15:02:59'),
(3, 'INTERNET ENTEL', '', '2026-10-06 15:03:05', '2026-10-06 15:03:05'),
(4, 'GAS 1', '', '2026-10-06 15:03:12', '2026-10-06 15:03:12'),
(5, 'GAS 2', '', '2026-10-06 15:03:16', '2026-10-06 15:03:16');

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
(1, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE', '{\"id\": 1, \"correo\": \"juanpablo@gmail.com\", \"nombre\": \"JUAN PABLO\", \"created_at\": \"2026-10-06T15:02:20.000000Z\", \"updated_at\": \"2026-10-06T15:02:20.000000Z\", \"descripcion\": \"\"}', NULL, 'PARTICIPANTES', '2026-10-06', '11:02:20', '2026-10-06 15:02:20', '2026-10-06 15:02:20'),
(2, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE', '{\"id\": 2, \"correo\": \"edwin@gmail.com\", \"nombre\": \"EDWIN\", \"created_at\": \"2026-10-06T15:02:30.000000Z\", \"updated_at\": \"2026-10-06T15:02:30.000000Z\", \"descripcion\": \"\"}', NULL, 'PARTICIPANTES', '2026-10-06', '11:02:30', '2026-10-06 15:02:30', '2026-10-06 15:02:30'),
(3, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE', '{\"id\": 3, \"correo\": \"victorgonzalo.as@gmail.com\", \"nombre\": \"GONZALO\", \"created_at\": \"2026-10-06T15:02:43.000000Z\", \"updated_at\": \"2026-10-06T15:02:43.000000Z\", \"descripcion\": \"\"}', NULL, 'PARTICIPANTES', '2026-10-06', '11:02:43', '2026-10-06 15:02:43', '2026-10-06 15:02:43'),
(4, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 1, \"nombre\": \"LUZ\", \"created_at\": \"2026-10-06T15:02:54.000000Z\", \"updated_at\": \"2026-10-06T15:02:54.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-06', '11:02:54', '2026-10-06 15:02:54', '2026-10-06 15:02:54'),
(5, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 2, \"nombre\": \"AGUA\", \"created_at\": \"2026-10-06T15:02:59.000000Z\", \"updated_at\": \"2026-10-06T15:02:59.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-06', '11:02:59', '2026-10-06 15:02:59', '2026-10-06 15:02:59'),
(6, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 3, \"nombre\": \"INTERNET ENTEL\", \"created_at\": \"2026-10-06T15:03:05.000000Z\", \"updated_at\": \"2026-10-06T15:03:05.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-06', '11:03:05', '2026-10-06 15:03:05', '2026-10-06 15:03:05'),
(7, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 4, \"nombre\": \"GAS 1\", \"created_at\": \"2026-10-06T15:03:12.000000Z\", \"updated_at\": \"2026-10-06T15:03:12.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-06', '11:03:12', '2026-10-06 15:03:12', '2026-10-06 15:03:12'),
(8, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN GASTO', '{\"id\": 5, \"nombre\": \"GAS 2\", \"created_at\": \"2026-10-06T15:03:16.000000Z\", \"updated_at\": \"2026-10-06T15:03:16.000000Z\", \"descripcion\": \"\"}', NULL, 'GASTOS', '2026-10-06', '11:03:16', '2026-10-06 15:03:16', '2026-10-06 15:03:16'),
(9, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PAGO', '{\"id\": 1, \"mes\": \"08\", \"anio\": \"2026\", \"total\": \"354.18\", \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\", \"pago_detalles\": [{\"id\": 1, \"fecha\": null, \"monto\": \"145.30\", \"fecha_t\": \"\", \"pago_id\": 1, \"gasto_id\": 1, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\"}, {\"id\": 2, \"fecha\": null, \"monto\": \"28.00\", \"fecha_t\": \"\", \"pago_id\": 1, \"gasto_id\": 2, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\"}, {\"id\": 3, \"fecha\": null, \"monto\": \"149.00\", \"fecha_t\": \"\", \"pago_id\": 1, \"gasto_id\": 3, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\"}, {\"id\": 4, \"fecha\": null, \"monto\": \"22.90\", \"fecha_t\": \"\", \"pago_id\": 1, \"gasto_id\": 4, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\"}, {\"id\": 5, \"fecha\": null, \"monto\": \"8.98\", \"fecha_t\": \"\", \"pago_id\": 1, \"gasto_id\": 5, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\"}], \"pago_participantes\": [{\"id\": 1, \"total\": \"0.00\", \"estado\": \"PENDIENTE\", \"pago_id\": 1, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\", \"correo_enviado\": 0, \"participante_id\": 1}, {\"id\": 2, \"total\": \"0.00\", \"estado\": \"PENDIENTE\", \"pago_id\": 1, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\", \"correo_enviado\": 0, \"participante_id\": 2}, {\"id\": 3, \"total\": \"0.00\", \"estado\": \"PENDIENTE\", \"pago_id\": 1, \"created_at\": \"2026-10-06T15:30:39.000000Z\", \"updated_at\": \"2026-10-06T15:30:39.000000Z\", \"correo_enviado\": 0, \"participante_id\": 3}]}', NULL, 'PAGOS', '2026-10-06', '11:30:39', '2026-10-06 15:30:39', '2026-10-06 15:30:39'),
(10, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 1, \"gasto_id\": \"1\", \"created_at\": \"2026-10-07T14:35:34.000000Z\", \"porcentaje\": \"45\", \"updated_at\": \"2026-10-07T14:35:34.000000Z\", \"participante_id\": \"1\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:35:34', '2026-10-07 14:35:34', '2026-10-07 14:35:34'),
(11, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 2, \"gasto_id\": \"2\", \"created_at\": \"2026-10-07T14:37:01.000000Z\", \"porcentaje\": \"45\", \"updated_at\": \"2026-10-07T14:37:01.000000Z\", \"participante_id\": \"1\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:37:01', '2026-10-07 14:37:01', '2026-10-07 14:37:01'),
(12, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 3, \"gasto_id\": \"4\", \"created_at\": \"2026-10-07T14:37:17.000000Z\", \"porcentaje\": \"45\", \"updated_at\": \"2026-10-07T14:37:17.000000Z\", \"participante_id\": \"1\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:37:18', '2026-10-07 14:37:18', '2026-10-07 14:37:18'),
(13, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 4, \"gasto_id\": \"3\", \"created_at\": \"2026-10-07T14:37:53.000000Z\", \"porcentaje\": \"50\", \"updated_at\": \"2026-10-07T14:37:53.000000Z\", \"participante_id\": \"1\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:37:53', '2026-10-07 14:37:53', '2026-10-07 14:37:53'),
(14, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 5, \"gasto_id\": \"1\", \"created_at\": \"2026-10-07T14:38:00.000000Z\", \"porcentaje\": \"25\", \"updated_at\": \"2026-10-07T14:38:00.000000Z\", \"participante_id\": \"2\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:38:00', '2026-10-07 14:38:00', '2026-10-07 14:38:00'),
(15, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 6, \"gasto_id\": \"2\", \"created_at\": \"2026-10-07T14:38:09.000000Z\", \"porcentaje\": \"25\", \"updated_at\": \"2026-10-07T14:38:09.000000Z\", \"participante_id\": \"2\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:38:09', '2026-10-07 14:38:09', '2026-10-07 14:38:09'),
(16, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 7, \"gasto_id\": \"4\", \"created_at\": \"2026-10-07T14:38:24.000000Z\", \"porcentaje\": \"25\", \"updated_at\": \"2026-10-07T14:38:24.000000Z\", \"participante_id\": \"2\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:38:24', '2026-10-07 14:38:24', '2026-10-07 14:38:24'),
(17, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 8, \"gasto_id\": \"5\", \"created_at\": \"2026-10-07T14:38:38.000000Z\", \"porcentaje\": \"100\", \"updated_at\": \"2026-10-07T14:38:38.000000Z\", \"participante_id\": \"2\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:38:38', '2026-10-07 14:38:38', '2026-10-07 14:38:38'),
(18, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 9, \"gasto_id\": \"5\", \"created_at\": \"2026-10-07T14:38:45.000000Z\", \"porcentaje\": \"0\", \"updated_at\": \"2026-10-07T14:38:45.000000Z\", \"participante_id\": \"1\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:38:45', '2026-10-07 14:38:45', '2026-10-07 14:38:45'),
(19, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 10, \"gasto_id\": \"1\", \"created_at\": \"2026-10-07T14:38:55.000000Z\", \"porcentaje\": \"30\", \"updated_at\": \"2026-10-07T14:38:55.000000Z\", \"participante_id\": \"3\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:38:55', '2026-10-07 14:38:55', '2026-10-07 14:38:55'),
(20, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 11, \"gasto_id\": \"2\", \"created_at\": \"2026-10-07T14:39:03.000000Z\", \"porcentaje\": \"30\", \"updated_at\": \"2026-10-07T14:39:03.000000Z\", \"participante_id\": \"3\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:39:03', '2026-10-07 14:39:03', '2026-10-07 14:39:03'),
(21, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 12, \"gasto_id\": \"4\", \"created_at\": \"2026-10-07T14:39:13.000000Z\", \"porcentaje\": \"30\", \"updated_at\": \"2026-10-07T14:39:13.000000Z\", \"participante_id\": \"3\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:39:13', '2026-10-07 14:39:13', '2026-10-07 14:39:13'),
(22, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 13, \"gasto_id\": \"5\", \"created_at\": \"2026-10-07T14:39:39.000000Z\", \"porcentaje\": \"0\", \"updated_at\": \"2026-10-07T14:39:39.000000Z\", \"participante_id\": \"3\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:39:39', '2026-10-07 14:39:39', '2026-10-07 14:39:39'),
(23, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 14, \"gasto_id\": \"3\", \"created_at\": \"2026-10-07T14:39:48.000000Z\", \"porcentaje\": \"50\", \"updated_at\": \"2026-10-07T14:39:48.000000Z\", \"participante_id\": \"3\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:39:48', '2026-10-07 14:39:48', '2026-10-07 14:39:48'),
(24, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PARTICIPANTE GASTO', '{\"id\": 15, \"gasto_id\": \"3\", \"created_at\": \"2026-10-07T14:43:06.000000Z\", \"porcentaje\": \"0\", \"updated_at\": \"2026-10-07T14:43:06.000000Z\", \"participante_id\": \"2\"}', NULL, 'PARTICIPANTE GASTOS', '2026-10-07', '10:43:06', '2026-10-07 14:43:06', '2026-10-07 14:43:06');

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
(8, '2026_10_05_101615_create_pago_participantes_table', 1),
(9, '2026_10_06_105225_create_pago_gastos_table', 2),
(10, '2026_10_07_101728_create_participante_gastos_table', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint UNSIGNED NOT NULL,
  `mes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio` int NOT NULL,
  `total` decimal(24,2) DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `fecha_registro` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `mes`, `anio`, `total`, `estado`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, '08', 2026, 354.18, 'PENDIENTE', '2026-10-06', '2026-10-06 15:30:39', '2026-10-06 15:30:39');

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

--
-- Volcado de datos para la tabla `pago_detalles`
--

INSERT INTO `pago_detalles` (`id`, `pago_id`, `gasto_id`, `monto`, `fecha`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 145.30, NULL, '2026-10-06 15:30:39', '2026-10-06 15:30:39'),
(2, 1, 2, 28.00, NULL, '2026-10-06 15:30:39', '2026-10-06 15:30:39'),
(3, 1, 3, 149.00, NULL, '2026-10-06 15:30:39', '2026-10-06 15:30:39'),
(4, 1, 4, 22.90, NULL, '2026-10-06 15:30:39', '2026-10-06 15:30:39'),
(5, 1, 5, 8.98, NULL, '2026-10-06 15:30:39', '2026-10-06 15:30:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_gastos`
--

CREATE TABLE `pago_gastos` (
  `id` bigint UNSIGNED NOT NULL,
  `pago_id` bigint UNSIGNED NOT NULL,
  `pago_detalle_id` bigint UNSIGNED NOT NULL,
  `participante_id` bigint UNSIGNED NOT NULL,
  `pago_participante_id` bigint UNSIGNED NOT NULL,
  `gasto_id` bigint UNSIGNED NOT NULL,
  `porcentaje_pago` double(11,8) NOT NULL DEFAULT '0.00000000',
  `monto_pagado` decimal(24,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pago_gastos`
--

INSERT INTO `pago_gastos` (`id`, `pago_id`, `pago_detalle_id`, `participante_id`, `pago_participante_id`, `gasto_id`, `porcentaje_pago`, `monto_pagado`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 45.00000000, 65.39, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(2, 1, 2, 1, 1, 2, 45.00000000, 12.60, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(3, 1, 3, 1, 1, 3, 50.00000000, 74.50, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(4, 1, 4, 1, 1, 4, 45.00000000, 10.31, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(5, 1, 5, 1, 1, 5, 0.00000000, 0.00, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(6, 1, 1, 2, 2, 1, 25.00000000, 36.33, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(7, 1, 2, 2, 2, 2, 25.00000000, 7.00, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(8, 1, 3, 2, 2, 3, 0.00000000, 0.00, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(9, 1, 4, 2, 2, 4, 25.00000000, 5.73, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(10, 1, 5, 2, 2, 5, 100.00000000, 8.98, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(11, 1, 1, 3, 3, 1, 30.00000000, 43.59, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(12, 1, 2, 3, 3, 2, 30.00000000, 8.40, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(13, 1, 3, 3, 3, 3, 50.00000000, 74.50, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(14, 1, 4, 3, 3, 4, 30.00000000, 6.87, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17'),
(15, 1, 5, 3, 3, 5, 0.00000000, 0.00, 'PENDIENTE', '2026-10-07 14:43:17', '2026-10-07 14:43:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_participantes`
--

CREATE TABLE `pago_participantes` (
  `id` bigint UNSIGNED NOT NULL,
  `pago_id` bigint UNSIGNED NOT NULL,
  `participante_id` bigint UNSIGNED NOT NULL,
  `correo_enviado` tinyint(1) NOT NULL DEFAULT '0',
  `total` decimal(24,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pago_participantes`
--

INSERT INTO `pago_participantes` (`id`, `pago_id`, `participante_id`, `correo_enviado`, `total`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 162.80, 'PENDIENTE', '2026-10-06 15:30:39', '2026-10-07 15:51:27'),
(2, 1, 2, 0, 58.04, 'PENDIENTE', '2026-10-06 15:30:39', '2026-10-07 15:51:27'),
(3, 1, 3, 0, 133.36, 'PENDIENTE', '2026-10-06 15:30:39', '2026-10-07 15:51:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `participantes`
--

INSERT INTO `participantes` (`id`, `nombre`, `correo`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'JUAN PABLO', 'juanpablo@gmail.com', '', '2026-10-06 15:02:20', '2026-10-06 15:02:20'),
(2, 'EDWIN', 'edwin@gmail.com', '', '2026-10-06 15:02:30', '2026-10-06 15:02:30'),
(3, 'GONZALO', 'victorgonzalo.as@gmail.com', '', '2026-10-06 15:02:43', '2026-10-06 15:02:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participante_gastos`
--

CREATE TABLE `participante_gastos` (
  `id` bigint UNSIGNED NOT NULL,
  `participante_id` bigint UNSIGNED NOT NULL,
  `gasto_id` bigint UNSIGNED NOT NULL,
  `porcentaje` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `participante_gastos`
--

INSERT INTO `participante_gastos` (`id`, `participante_id`, `gasto_id`, `porcentaje`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 45, '2026-10-07 14:35:34', '2026-10-07 14:35:34'),
(2, 1, 2, 45, '2026-10-07 14:37:01', '2026-10-07 14:37:01'),
(3, 1, 4, 45, '2026-10-07 14:37:17', '2026-10-07 14:37:17'),
(4, 1, 3, 50, '2026-10-07 14:37:53', '2026-10-07 14:37:53'),
(5, 2, 1, 25, '2026-10-07 14:38:00', '2026-10-07 14:38:00'),
(6, 2, 2, 25, '2026-10-07 14:38:09', '2026-10-07 14:38:09'),
(7, 2, 4, 25, '2026-10-07 14:38:24', '2026-10-07 14:38:24'),
(8, 2, 5, 100, '2026-10-07 14:38:38', '2026-10-07 14:38:38'),
(9, 1, 5, 0, '2026-10-07 14:38:45', '2026-10-07 14:38:45'),
(10, 3, 1, 30, '2026-10-07 14:38:55', '2026-10-07 14:38:55'),
(11, 3, 2, 30, '2026-10-07 14:39:03', '2026-10-07 14:39:03'),
(12, 3, 4, 30, '2026-10-07 14:39:13', '2026-10-07 14:39:13'),
(13, 3, 5, 0, '2026-10-07 14:39:39', '2026-10-07 14:39:39'),
(14, 3, 3, 50, '2026-10-07 14:39:48', '2026-10-07 14:39:48'),
(15, 2, 3, 0, '2026-10-07 14:43:06', '2026-10-07 14:43:06');

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
-- Indices de la tabla `pago_gastos`
--
ALTER TABLE `pago_gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_gastos_pago_id_foreign` (`pago_id`),
  ADD KEY `pago_gastos_pago_detalle_id_foreign` (`pago_detalle_id`),
  ADD KEY `pago_gastos_participante_id_foreign` (`participante_id`),
  ADD KEY `pago_gastos_pago_participante_id_foreign` (`pago_participante_id`),
  ADD KEY `pago_gastos_gasto_id_foreign` (`gasto_id`);

--
-- Indices de la tabla `pago_participantes`
--
ALTER TABLE `pago_participantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_participantes_pago_id_foreign` (`pago_id`),
  ADD KEY `pago_participantes_participante_id_foreign` (`participante_id`);

--
-- Indices de la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participantes_nombre_unique` (`nombre`),
  ADD UNIQUE KEY `participantes_correo_unique` (`correo`);

--
-- Indices de la tabla `participante_gastos`
--
ALTER TABLE `participante_gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participante_gastos_participante_id_foreign` (`participante_id`),
  ADD KEY `participante_gastos_gasto_id_foreign` (`gasto_id`);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pago_detalles`
--
ALTER TABLE `pago_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pago_gastos`
--
ALTER TABLE `pago_gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `pago_participantes`
--
ALTER TABLE `pago_participantes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `participante_gastos`
--
ALTER TABLE `participante_gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
-- Filtros para la tabla `pago_gastos`
--
ALTER TABLE `pago_gastos`
  ADD CONSTRAINT `pago_gastos_gasto_id_foreign` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`),
  ADD CONSTRAINT `pago_gastos_pago_detalle_id_foreign` FOREIGN KEY (`pago_detalle_id`) REFERENCES `pago_detalles` (`id`),
  ADD CONSTRAINT `pago_gastos_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`),
  ADD CONSTRAINT `pago_gastos_pago_participante_id_foreign` FOREIGN KEY (`pago_participante_id`) REFERENCES `pago_participantes` (`id`),
  ADD CONSTRAINT `pago_gastos_participante_id_foreign` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`);

--
-- Filtros para la tabla `pago_participantes`
--
ALTER TABLE `pago_participantes`
  ADD CONSTRAINT `pago_participantes_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`),
  ADD CONSTRAINT `pago_participantes_participante_id_foreign` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`);

--
-- Filtros para la tabla `participante_gastos`
--
ALTER TABLE `participante_gastos`
  ADD CONSTRAINT `participante_gastos_gasto_id_foreign` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`),
  ADD CONSTRAINT `participante_gastos_participante_id_foreign` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
