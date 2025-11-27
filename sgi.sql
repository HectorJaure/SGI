-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 15:10:00
-- Versión del servidor: 8.0.42
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sgi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `legal_requirements`
--

CREATE TABLE `legal_requirements` (
  `id` bigint UNSIGNED NOT NULL,
  `norma` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria_norma` enum('seguridad','salud','organizacion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'seguridad',
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_requisito` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_requisito` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cumplimiento` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evidencia` text COLLATE utf8mb4_unicode_ci,
  `acciones_no` text COLLATE utf8mb4_unicode_ci,
  `peligro_asociado` text COLLATE utf8mb4_unicode_ci,
  `fecha_cumplimiento` date DEFAULT NULL,
  `responsables` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia_control` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsable_control` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_29_182822_create_password_resets_table', 1),
(5, '2025_10_29_182851_create_risks_table', 1),
(6, '2025_10_29_182919_create_legal_requirements_table', 1),
(7, '2025_10_29_182950_create_notifications_table', 1),
(8, '2025_11_01_195837_add_telefono_to_users_table', 1),
(9, '2025_11_07_222626_add_otros_factores_to_risks_table', 1),
(10, '2025_11_07_223853_add_otros_factores_column_to_risks_table', 1),
(11, '2025_11_10_143353_add_usuario_accion_to_notifications_table', 1),
(12, '2025_11_18_183057_add_categoria_norma_to_requisitos_legales_table', 1),
(13, '2025_11_18_195830_increase_peligro_asociado_length_in_legal_requirements_table', 1),
(14, '2025_11_18_200109_make_columns_nullable_in_legal_requirements_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('info','warning','success','urgent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('leida','no_leida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no_leida',
  `remitente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sistema',
  `usuario_accion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `risks`
--

CREATE TABLE `risks` (
  `id` bigint UNSIGNED NOT NULL,
  `lugar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actividad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peligro` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_riesgo` enum('Interno','Externo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `otros_factores` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'No aplica',
  `clasificacion` enum('Seguridad','Salud') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tiempo_exposicion` decimal(3,1) NOT NULL,
  `personas_expuestas` decimal(3,1) NOT NULL,
  `probabilidad_ocurrencia` decimal(3,1) NOT NULL,
  `consecuencia_personas` decimal(3,1) NOT NULL,
  `consecuencia_infraestructura` decimal(3,1) NOT NULL,
  `significancia` decimal(8,2) NOT NULL,
  `nivel_riesgo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `risks`
--

INSERT INTO `risks` (`id`, `lugar`, `actividad`, `peligro`, `tipo_riesgo`, `otros_factores`, `clasificacion`, `tiempo_exposicion`, `personas_expuestas`, `probabilidad_ocurrencia`, `consecuencia_personas`, `consecuencia_infraestructura`, `significancia`, `nivel_riesgo`, `created_at`, `updated_at`) VALUES
(1, 'Edificio A, C y D', 'Actividades administrativas', 'Cortaduras por bordes filosos en vidrios de credenza en Servicios Escolares; vidrio incompleto en puestas de oficina (Centro de maestros ADM).', 'Interno', 'No aplica', 'Seguridad', 1.0, 1.0, 1.0, 1.0, 1.0, 6.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(2, 'Edificio A, C y D', 'Actividades administrativas', 'Caída por cables sueltos sobre nivel de piso (Servicios escolares) o por silla con respaldo suelto o en mal estado Sala de maestros ADM', 'Interno', 'No aplica', 'Seguridad', 1.0, 1.0, 1.0, 1.0, 1.0, 6.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(3, 'Todas la área', 'Mantenimiento (limpieza)', 'Resbalón y caídas por pisos mojados', 'Interno', 'No aplica', 'Seguridad', 5.0, 1.0, 1.0, 1.0, 0.0, 7.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(4, 'Todas la área', 'Mantenimiento (limpieza)', 'Daño a la piel por exposición al cloro', 'Interno', 'No aplica', 'Salud', 5.0, 1.0, 1.0, 1.0, 0.0, 7.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(5, 'Edificio A (planta alta) y Cafetería', 'Circulación por los pasillos', 'Golpes en la cabeza por soporte de extintor botado (el extintor ya no está)', 'Interno', 'No aplica', 'Seguridad', 1.0, 1.0, 1.0, 1.0, 1.0, 6.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(6, 'Edificio A (planta alta) y Cafetería', 'Necesidades básicas', 'Caídas por goteras en sanitario de los hombre planta alta y caferería', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 1.0, 1.0, 1.0, 18.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(7, 'Laboratorio de Química (Edificio A)', 'Prácticas de laboratorio', 'Caída por banco sin tornillo', 'Interno', 'No aplica', 'Seguridad', 1.0, 2.0, 1.0, 1.0, 0.0, 4.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(8, 'Laboratorio de Química (Edificio A)', 'Prácticas de laboratorio', 'Posible electrocución por contacto suelto en escritorio del profesor', 'Interno', 'No aplica', 'Seguridad', 5.0, 2.0, 1.0, 1.0, 1.0, 16.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(9, 'Edificios A, B, C Y D (aulas)', 'Impartición de clases', 'Caída por hueco en piso al final del salón (A9) y junto al escritorio (A10), por bultos de arena y ladrillos (Lab. Arq)', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 1.0, 1.0, 1.0, 18.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(10, 'Edificios A, B, C Y D (aulas)', 'Impartición de clases', 'Caída por: butacas sin remache (A1, A3, A11), butaca inestable por pata (A3), silla con respaldo/asiento suelto (A2, A4, C4, C6, C7 y C9)', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 1.0, 1.0, 1.0, 18.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(11, 'Edificios A, B, C Y D (aulas)', 'Impartición de clases', 'Posibles contacto eléctrico por apagador con cables expuestos (A2)', 'Interno', 'No aplica', 'Seguridad', 5.0, 2.0, 1.0, 1.0, 1.0, 16.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(12, 'Edificios A, B, C Y D (aulas)', 'Impartición de clases', 'Cortaduras por bordes filosos en tubo de la silla de escritorio (A1), corte por bordes filosos en chapas de las puertas (aulas C2 y C3); por rebabas en marco de puerta (aula C8).', 'Interno', 'No aplica', 'Seguridad', 1.0, 2.0, 1.0, 1.0, 1.0, 8.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(13, 'Edificios A, B, C Y D (aulas)', 'Impartición de clases', 'Toques o electrocución por contactos flojos y expuesto al interperie (Aulas C4, C6, C9, estacionamiento de vehiculos oficiales y almacen de IIAS', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 1.0, 1.0, 1.0, 18.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(14, 'Edificios A, B, C Y D (aulas)', 'Impartición de clases', 'Golpes por plafones sueltos en las aula, oficinas administrativas y sanitarios.', 'Interno', 'No aplica', 'Seguridad', 1.0, 2.0, 1.0, 1.0, 1.0, 8.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(15, 'Edificio C', 'Circulación por áreas comunes', 'Caida por Falta de Protección en escaleras para subir y bajar a 2do. Y 3er. Piso del edificio C', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 3.0, 1.0, 1.0, 22.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(16, 'Bodegas de Edificio B', 'Mantenimiento y almacen', 'Poco control de acceso a Áreas Restringidas en: bodega de mantenimiento, almacen de semillas (IIAS) y ADIE', 'Interno', 'No aplica', 'Seguridad', 1.0, 2.0, 1.0, 1.0, 0.0, 4.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(17, 'Bodegas de Edificio B', 'Mantenimiento y almacen', 'Caídas por desorden de cajas, carpetas, cables.', 'Interno', 'No aplica', 'Seguridad', 1.0, 2.0, 1.0, 1.0, 0.0, 4.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(18, 'Bodegas de Edificio B', 'Mantenimiento y almacen', 'Productos y liquidos sin señalizar o identificación', 'Interno', 'No aplica', 'Seguridad', 1.0, 1.0, 1.0, 1.0, 1.0, 6.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(19, 'Estacionamiento', 'Circulación de vehiculos y peatones', 'Caida, raspones, golpes, por tapa de registro de drenaje levantada. Tapa en mal estado, expuesta a caída.', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 1.0, 1.0, 1.0, 18.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(20, 'Estacionamiento', 'Circulación de vehiculos y peatones', 'Tropiezos o golpes por motocicletas estacionadas en área de acceso entre los edificios C y D y en el estacionamiento de vehiculos oficiales.', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 3.0, 1.0, 0.0, 11.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(21, 'Áreas exteriores/pasillos.', 'Circulación por áreas comunes', 'Posible picadura de araña. Nido de araña capulina (viuda negra) en bancas de pasillos y mobiliario de las aulas', 'Interno', 'No aplica', 'Seguridad', 1.0, 4.0, 1.0, 1.0, 1.0, 12.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(22, 'Áreas exteriores/pasillos.', 'Circulación por áreas comunes', 'Panales de avispas', 'Interno', 'No aplica', 'Seguridad', 5.0, 3.0, 3.0, 1.0, 1.0, 22.00, 'baja', '2025-11-26 18:59:08', '2025-11-26 18:59:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol` enum('Administrador','Usuario') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Usuario',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `nombre`, `departamento`, `telefono`, `rol`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@itsn.edu.mx', '$2y$12$jdViMeWxbhKOdkTQcBx3GunwdL8gsPk96Q6d9tZbRMTkCHHq9Qka.', 'Administrador del Sistema', 'Sistemas', NULL, 'Administrador', NULL, '2025-11-26 18:59:07', '2025-11-26 18:59:07'),
(2, 'usuario', 'usuario@itsn.edu.mx', '$2y$12$7YaJwV6gYKsGUwyK/YWgCuCCRuRsVOVVG1afj0POxYDY6IbIQNoOi', 'Usuario de Prueba', 'Recursos Humanos', NULL, 'Usuario', NULL, '2025-11-26 18:59:08', '2025-11-26 18:59:08'),
(3, '22050029', '22050029@itsn.edu.mx', '$2y$12$BP1W43buGzq09tekjAvo8umdQTRgz5hoyAQ/pgvZf39EiH6VuU3bi', 'Estudiante ITSN', 'Estudiantes', NULL, 'Usuario', NULL, '2025-11-26 18:59:08', '2025-11-26 19:59:54'),
(4, '22050086', '22050086@itsn.edu.mx', '$2y$12$g/9USeKej.f.eZgS/4YNvO9GYxe34MQqYKr1ICZqbWHDJxcV5XMHW', 'Estudiante ITSN', 'Estudiantes', NULL, 'Usuario', NULL, '2025-11-26 18:59:08', '2025-11-26 18:59:08');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `legal_requirements`
--
ALTER TABLE `legal_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `risks`
--
ALTER TABLE `risks`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `legal_requirements`
--
ALTER TABLE `legal_requirements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `risks`
--
ALTER TABLE `risks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
