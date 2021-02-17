-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2021 at 10:03 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mcb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer`
--

CREATE TABLE `tbl_customer` (
  `c_id` int(11) NOT NULL,
  `customer_id` varchar(10) NOT NULL,
  `customer_type` varchar(20) NOT NULL,
  `Date_Of_Birth` date DEFAULT NULL,
  `Date_Incorp` date DEFAULT NULL,
  `REGISTRATION_NO` varchar(10) DEFAULT NULL,
  `Address_Line1` varchar(200) NOT NULL,
  `Address_Line2` varchar(200) NOT NULL,
  `Town_City` varchar(50) NOT NULL,
  `Country` varchar(50) NOT NULL,
  `Contact_Name` varchar(50) NOT NULL,
  `Contact_Number` int(20) NOT NULL,
  `Num_Shares` int(20) NOT NULL,
  `Share_Price` float NOT NULL,
  `f_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_customer`
--

INSERT INTO `tbl_customer` (`c_id`, `customer_id`, `customer_type`, `Date_Of_Birth`, `Date_Incorp`, `REGISTRATION_NO`, `Address_Line1`, `Address_Line2`, `Town_City`, `Country`, `Contact_Name`, `Contact_Number`, `Num_Shares`, `Share_Price`, `f_id`) VALUES
(1, 'C11233', 'Individual', '0000-00-00', '0000-00-00', '', '21', 'Downing Street', 'London', 'England', 'Mr John Doe', 7784051, 10200, 11.4, 1),
(2, 'D43764', 'Corporate', '0000-00-00', '0000-00-00', 'R14023581', '40', 'Morven Road', 'St Louis', 'United States', 'Sterling Holdings', 2147483647, 4000, 9.2, 1),
(3, 'H15676', 'Corporate', '0000-00-00', '0000-00-00', 'R10411524', '50 Paul', 'Detroit Road', 'Ohio', 'United States', 'Dellaware Ltd', 0, 63000, 12.2, 1),
(4, 'T90897', 'Individual', '0000-00-00', '0000-00-00', 'R156925840', '33 St James', 'Court', 'Port Louis', 'Mauritius', 'Thompson Deutsche Ltd', 579815662, 2000, 365.2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_error`
--

CREATE TABLE `tbl_error` (
  `e_id` int(11) NOT NULL,
  `e_customer_id` varchar(10) NOT NULL,
  `e_message` varchar(250) NOT NULL,
  `f_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_file`
--

CREATE TABLE `tbl_file` (
  `f_id` int(11) NOT NULL,
  `f_name` varchar(10) NOT NULL,
  `f_path` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_file`
--

INSERT INTO `tbl_file` (`f_id`, `f_name`, `f_path`) VALUES
(1, 'SH13101400', 'dir/SH1310140001.xml');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_customer`
--
ALTER TABLE `tbl_customer`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `f_id` (`f_id`);

--
-- Indexes for table `tbl_error`
--
ALTER TABLE `tbl_error`
  ADD PRIMARY KEY (`e_id`),
  ADD KEY `f_id` (`f_id`);

--
-- Indexes for table `tbl_file`
--
ALTER TABLE `tbl_file`
  ADD PRIMARY KEY (`f_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_customer`
--
ALTER TABLE `tbl_customer`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_error`
--
ALTER TABLE `tbl_error`
  MODIFY `e_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_file`
--
ALTER TABLE `tbl_file`
  MODIFY `f_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_customer`
--
ALTER TABLE `tbl_customer`
  ADD CONSTRAINT `tbl_customer_ibfk_1` FOREIGN KEY (`f_id`) REFERENCES `tbl_file` (`f_id`);

--
-- Constraints for table `tbl_error`
--
ALTER TABLE `tbl_error`
  ADD CONSTRAINT `tbl_error_ibfk_1` FOREIGN KEY (`f_id`) REFERENCES `tbl_file` (`f_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
