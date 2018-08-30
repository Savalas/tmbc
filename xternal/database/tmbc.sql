-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2018 at 05:28 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tmbc`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
`comment_id` bigint(20) NOT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `username` varchar(150) NOT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `tsCreated` int(11) NOT NULL,
  `tsUpdated` int(11) DEFAULT NULL,
  `tsDeleted` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COMMENT='the table to save the website comments';

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `comment`, `username`, `parent_id`, `tsCreated`, `tsUpdated`, `tsDeleted`) VALUES
(1, 'rgfsgsgsg', 'Johnny', 0, 1535637386, NULL, NULL),
(2, 'sfsdfs', 'sdfsdfsf', 0, 1535637453, NULL, NULL),
(3, 'dfsfs', 'dsfsdfsfd', 0, 1535637552, NULL, NULL),
(4, 'Ok johnny', 'SavCo', 0, 1535637774, NULL, NULL),
(5, 'ok johnnny 2', 'SavMan', 1, 1535637828, NULL, NULL),
(6, 'Anotyher 1', 'test', 2, 1535637886, NULL, NULL),
(7, 'Another 1 again', 'test', 2, 1535637924, NULL, NULL),
(8, 'so johnnny', 'SavMan2', 5, 1535638418, NULL, NULL),
(9, 'so so johnny', 'SavMan3', 8, 1535638460, NULL, NULL),
(10, 'so so so johnny', 'savman4', 9, 1535638523, NULL, NULL),
(11, 'Ok Now', 'Marcia', 0, 1535638551, NULL, NULL),
(12, 'ok ok Now', 'Marcia', 11, 1535638588, NULL, NULL),
(13, 'so so so johnny', 'savman5', 10, 1535638633, NULL, NULL),
(14, 'Another for SavMan2', 'savManAgain', 8, 1535638668, NULL, NULL),
(15, 'New Comment', 'LastMan', 0, 1535639086, NULL, NULL),
(16, 'Answe This lastman', 'lastman1', 15, 1535639110, NULL, NULL),
(17, '3rd level', 'Savco', 6, 1535639383, NULL, NULL),
(18, '3rd Level again', 'Someone', 6, 1535639407, NULL, NULL),
(19, '2nd Level Action', 'TestNow', 2, 1535639432, NULL, NULL),
(20, '1st Level Area', 'AreaMan', 0, 1535639469, NULL, NULL),
(21, 'eqaeffqa', 'Shelia', 0, 1535640192, NULL, NULL),
(22, 'Top Level Dad', 'Timothy', 0, 1535640491, NULL, NULL),
(23, 'timothy2', 'Dad2', 22, 1535640625, NULL, NULL),
(24, 'timothy3', 'Dad3', 23, 1535640679, NULL, NULL),
(25, 'Dad3 another one', 'Dad3B', 23, 1535640708, NULL, NULL),
(26, 'Next Level Mom', 'Mom2', 21, 1535640776, NULL, NULL),
(27, 'Mommmy', 'Mom3', 26, 1535640790, NULL, NULL),
(28, 'nana', 'mom3B', 26, 1535640805, NULL, NULL),
(29, 'Add Next Level', 'Dad2B', 22, 1535640994, NULL, NULL),
(30, 'new One', 'MeNow', 0, 1535641503, NULL, NULL),
(31, 'SavmanLevel3', 'SavMan3', 5, 1535641770, NULL, NULL),
(32, 'SavmanLevel3', 'SavMan4', 5, 1535641800, NULL, NULL),
(33, 'MeNow2', 'MeNowAgain', 30, 1535642007, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
 ADD PRIMARY KEY (`comment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
MODIFY `comment_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
