-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2024 a las 01:41:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `practicas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario_etapas`
--

CREATE TABLE `formulario_etapas` (
  `id` int(11) NOT NULL,
  `Fecha_hora` datetime DEFAULT current_timestamp(),
  `carrera` varchar(255) NOT NULL,
  `apellido_nombre` varchar(255) NOT NULL,
  `DNI` int(10) NOT NULL,
  `Fecha_egreso` date DEFAULT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `ciudad` varchar(255) NOT NULL,
  `situacion_laboral` varchar(255) NOT NULL,
  `empresa` varchar(255) DEFAULT NULL,
  `localidadempresa` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `relaciontrabajo` varchar(255) DEFAULT NULL,
  `vinculacion` varchar(255) DEFAULT NULL,
  `Actividad` varchar(255) DEFAULT NULL,
  `Docente` varchar(255) DEFAULT NULL,
  `cargo_docente` varchar(255) DEFAULT NULL,
  `Departamento_docente` varchar(255) DEFAULT NULL,
  `becario` varchar(255) DEFAULT NULL,
  `no_docente` varchar(255) DEFAULT NULL,
  `desocupado` text DEFAULT NULL,
  `capacitarse` text DEFAULT NULL,
  `acompanar` varchar(255) NOT NULL,
  `nombre_normalizado` varchar(255) DEFAULT NULL,
  `estado` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
