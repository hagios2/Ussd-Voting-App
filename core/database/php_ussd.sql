-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2020 at 07:15 PM
-- Server version: 5.7.30-0ubuntu0.18.04.1
-- PHP Version: 7.2.31-1+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_ussd`
--

-- --------------------------------------------------------

--
-- Table structure for table `pensioners_biodata_table`
--

CREATE TABLE `pensioners_biodata_table` (
  `id` int(11) NOT NULL,
  `msidn` varchar(100) NOT NULL,
  `member_id` varchar(100) NOT NULL,
  `surname` varchar(1000) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `other_names` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pensioners_biodata_table`
--

INSERT INTO `pensioners_biodata_table` (`id`, `msidn`, `member_id`, `surname`, `firstname`, `other_names`, `dob`, `gender`, `nationality`, `verified_at`) VALUES
(14, '0203665358', 'eea1b', 'Wilson', 'Emmanel', '', '2006-03-02', 'Male', 'Ghanain', '2020-06-22 18:11:10');

-- --------------------------------------------------------

--
-- Table structure for table `pensioners_session_table`
--

CREATE TABLE `pensioners_session_table` (
  `msidn` varchar(100) NOT NULL,
  `member_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session_manager_table`
--

CREATE TABLE `session_manager_table` (
  `msidn` varchar(100) NOT NULL,
  `transaction_type` varchar(100) DEFAULT NULL,
  `T1` varchar(100) DEFAULT NULL,
  `T2` varchar(100) DEFAULT NULL,
  `T3` varchar(100) DEFAULT NULL,
  `T4` varchar(100) DEFAULT NULL,
  `T5` varchar(100) DEFAULT NULL,
  `T6` varchar(100) DEFAULT NULL,
  `T7` varchar(100) DEFAULT NULL,
  `T8` varchar(100) DEFAULT NULL,
  `T9` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pensioners_biodata_table`
--
ALTER TABLE `pensioners_biodata_table`
  ADD PRIMARY KEY (`id`,`member_id`),
  ADD UNIQUE KEY `msidn` (`msidn`);

--
-- Indexes for table `pensioners_session_table`
--
ALTER TABLE `pensioners_session_table`
  ADD PRIMARY KEY (`msidn`,`member_id`);

--
-- Indexes for table `session_manager_table`
--
ALTER TABLE `session_manager_table`
  ADD PRIMARY KEY (`msidn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pensioners_biodata_table`
--
ALTER TABLE `pensioners_biodata_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
