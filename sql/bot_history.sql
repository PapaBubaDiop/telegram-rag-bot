-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 19, 2026 at 10:53 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u839204355_bill`
--

-- --------------------------------------------------------

--
-- Table structure for table `bot_history`
--

CREATE TABLE `bot_history` (
  `uid` int(11) NOT NULL,
  `chat_id` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Dumping data for table `bot_history`
--

INSERT INTO `bot_history` (`uid`, `chat_id`, `text`, `content`) VALUES
(5, '499219519', 'Context: SAT 09:40 Sundial — Enhancing University Education with AI: Telegram Bot with RAG & External APIs — Vadim Bashurov; Paul Safonov\n\nUser Query: tell about prof Safonov', ''),
(6, '499219519', 'tell about Bashurov', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bot_history`
--
ALTER TABLE `bot_history`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bot_history`
--
ALTER TABLE `bot_history`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
