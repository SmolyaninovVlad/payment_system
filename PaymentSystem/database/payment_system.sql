-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 12 2020 г., 15:11
-- Версия сервера: 5.7.16
-- Версия PHP: 7.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `payment_system`
--
CREATE DATABASE IF NOT EXISTS `payment_system` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `payment_system`;

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `appointment` varchar(100) NOT NULL,
  `card_Number` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  `sessionId` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `payments`
--

INSERT INTO `payments` (`id`, `appointment`, `card_Number`, `total`, `date`, `sessionId`) VALUES
(63, 'asdasdasd', '000000000000000000', '11.00', '2020-06-12 12:14:08', NULL),
(64, '123123', '0000000000000000000000', '123123.00', '2020-06-12 12:15:26', 'NULL'),
(65, '123123', '0000000000000', '123123.00', '2020-06-12 12:15:33', 'pay-fc490ca45c00b1249bbe3554a4fdf6fb'),
(66, '123123', '00000000000000000000', '123123.00', '2020-06-12 12:15:35', 'pay-3295c76acbf4caaed33c36b1b5fc2cb1'),
(67, '123123', '00000000000000000000', '123123.00', '2020-06-12 12:15:40', 'pay-735b90b4568125ed6c3f678819b6e058'),
(68, '123123 123', '00000000000000000000', '123123.00', '2020-06-12 12:15:43', 'pay-a3f390d88e4c41f2747bfa2f1b5f87db'),
(69, '123123 123', '00000000000000000000', '123123.00', '2020-06-12 12:16:54', 'pay-14bfa6bb14875e45bba028a21ed38046'),
(70, '123123 123', '00000000000000000000', '123123.00', '2020-06-12 12:17:01', 'NULL'),
(71, '123123123', '0000000000000000000000', '123123.00', '2020-06-12 12:43:23', 'pay-e2c420d928d4bf8ce0ff2ec19b371514'),
(72, '123123123', '0000000000000000000', '123123.00', '2020-06-12 12:44:33', 'NULL'),
(73, '123123', '000000000000000', '213123.00', '2020-06-12 13:36:31', 'pay-d2ddea18f00665ce8623e36bd4e3c7c5'),
(74, '123123', '000000000000000000000', '213123.00', '2020-06-12 13:37:06', 'pay-ad61ab143223efbc24c7d2583be69251'),
(75, '123', '000000000000000000000', '-213123.00', '2020-06-12 13:38:12', 'pay-d09bf41544a3365a46c9077ebb5e35c3'),
(76, '123', '000000000000000000000', '213123.00', '2020-06-12 13:39:33', 'pay-fbd7939d674997cdb4692d34de8633c4');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
