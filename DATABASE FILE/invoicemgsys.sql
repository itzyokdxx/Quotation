-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2025 at 09:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invoicemgsys`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `store_customer` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `county` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `name_ship` varchar(255) NOT NULL,
  `address_1_ship` varchar(255) NOT NULL,
  `address_2_ship` varchar(255) NOT NULL,
  `town_ship` varchar(255) NOT NULL,
  `county_ship` varchar(255) NOT NULL,
  `postcode_ship` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `invoice_date` varchar(255) NOT NULL,
  `invoice_due_date` varchar(255) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `notes` text NOT NULL,
  `invoice_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `product` text NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` text NOT NULL,
  `product_desc` text NOT NULL,
  `product_price` decimal(10,2) DEFAULT NULL,
  `product_image` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_desc`, `product_price`, `product_image`) VALUES
(996, 'Foam Boards', '3mm (20x30 in)', 275.00, NULL),
(993, 'PVC Boards', '10mm (4x8 ft)', 2800.00, NULL),
(989, 'Acrylic Sheets', '1.5mm (4x6 in)', 110.00, NULL),
(990, 'Acrylic Sheets', '3mm (4x8 ft)', 3850.00, NULL),
(991, 'Acrylic Sheets', '5mm (4x8 ft)', 5500.00, NULL),
(992, 'PVC Boards', '5mm (4x8 ft)', 1600.00, NULL),
(994, 'Vinyl Stickers', 'Per roll (24 in x 5 m)', 1400.00, NULL),
(995, 'Laminated Films', 'Per roll (12 in x 100 m)', 1100.00, NULL),
(988, 'PVC Boards', '3mm (4x8 ft)', 1100.00, NULL),
(997, 'Foam Boards', '5mm (4x8 ft)', 2100.00, NULL),
(998, 'Pull-Up Stands', 'Standard size (33x80 in)', 3850.00, NULL),
(999, 'Pull-Up Stands', 'Wide size (36x92 in)', 5500.00, NULL),
(1000, 'Dye Sublimation Prints', 'Per square foot', 330.00, NULL),
(1001, 'Reflective Stickers', 'Per square foot', 250.00, NULL),
(1002, 'Opal Acrylic Sheets', '1.5mm (4x6 ft)', 990.00, NULL),
(1003, 'PP Sticker', 'Per roll ', 1162.50, NULL),
(1004, 'Laminated Film Gloss', 'Per roll', 2185.00, NULL),
(1005, 'Cyno #2', '10 botts', 1400.00, NULL),
(1006, 'Dye PP Sticker Matte', 'Per roll', 1750.00, NULL),
(1007, 'asd', 'asdas', 100.00, NULL),
(1008, 'test', 'test', 100.00, NULL),
(1009, 'test', 'test', 100.00, NULL),
(1010, 'asd', 'asd', 100.00, NULL),
(1011, 'asd', 'asd', 100.00, NULL),
(1012, 're', 're', 1.10, ''),
(1013, 'asd', 'asd', 1.10, 'product_20241205191711.jpg'),
(1014, 're', 're', 1.10, 'product_20241205191752.jpg'),
(1016, 'test', 'test', 1.50, 'product_20241205194323.jpg'),
(1017, 'asd', 'asd', 10.00, 'product_20241207073732.jpg'),
(1018, 'aaaaaaaaaaaa', 'aaaaaaaaaaa', 1000.00, 'product_20241207073808.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `store_customers`
--

CREATE TABLE `store_customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `county` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `name_ship` varchar(255) NOT NULL,
  `address_1_ship` varchar(255) NOT NULL,
  `address_2_ship` varchar(255) NOT NULL,
  `town_ship` varchar(255) NOT NULL,
  `county_ship` varchar(255) NOT NULL,
  `postcode_ship` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `store_customers`
--

INSERT INTO `store_customers` (`id`, `name`, `email`, `address_1`, `address_2`, `town`, `county`, `postcode`, `phone`, `name_ship`, `address_1_ship`, `address_2_ship`, `town_ship`, `county_ship`, `postcode_ship`) VALUES
(53, 'Wendy Reilly', 'wendy@mail.com', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488', '3214444444', 'Wendy Reilly', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488'),
(54, 'Albert M Dunford', 'albd@mail.com', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092', '8520000010', 'Albert M Dunford', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092'),
(55, 'Anne B Ruch', 'anner@mail.com', '4039 Overlook Drive', '6939 Dt Drive', 'Indianapolis', 'US', '46225', '1478500000', 'Anne B Ruch', '4039 Overlook Drive', '6939 Dt Drive', 'Indianapolis', 'US', '46225'),
(56, 'Celeste Prather', 'celeste@mail.com', '421 Fincham Road', '421 Fincham Road', 'San Diego', 'US', '90000', '8021111111', 'Celeste Prather', '421 Fincham Road', '421 Fincham Road', 'San Diego', 'US', '90000'),
(57, 'Katharine Mayer', 'kathmay@mail.com', '508 Bernardo Street', '508 Bernardo Street', 'Tampa', 'US', '90000', '9014555500', 'Katharine Mayer', '508 Bernardo Street', '508 Bernardo Street', 'Tampa', 'US', '90000'),
(58, 'Rose Thompson', 'thompsonr@mail.com', '2374 Berkley Street', '2374 Berkley Street', 'Northampton', 'US', '01010', '7410000020', 'Rose Thompson', '2374 Berkley Street', '2374 Berkley Street', 'Northampton', 'US', '01010'),
(59, 'Ira Turner', 'iratur@mail.com', '1387 Pine Street', '1387 Pine Street', 'Pittsburgh', 'US', '10005', '7890002222', 'Ira Turner', '1387 Pine Street', '1387 Pine Street', 'Pittsburgh', 'US', '10005'),
(60, 'Richards', 'richards@mail.com', '311 Bchwood Drive', '311 Bchwood Drive', 'Bridgeville', 'US', '50005', '7410000014', 'Richards', '311 Bchwood Drive', '311 Bchwood Drive', 'Bridgeville', 'US', '50005'),
(61, 'Allan Deer', 'allande@mail.com', '1702 Modoc Alley', '1702 Modoc Alley', 'White Bird', 'US', '55550', '8520001450', 'Allan Deer', '1702 Modoc Alley', '1702 Modoc Alley', 'White Bird', 'US', '55550'),
(62, 'Demo User', 'demouser@mail.com', '115 Demo Address', '116 Demo Address', 'DemoTown', 'DemoCn', '00020', '7777777777', 'Demo User', '115 Demo Address', '116 Demo Address', 'DemoTown', 'DemoCn', '00020'),
(63, 'Albert M Dunford', 'albd@mail.com', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092', '8520000010', 'Albert M Dunford', '1070 Crisostomo St., Sampaloc, Manila', '1070 Crisostomo St., Sampaloc, Manila', 'Norcross', 'US', '30092'),
(64, 'Albert M Dunford', 'albd@mail.com', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092', '8520000010', 'Albert M Dunford', '1070 Crisostomo St., Sampaloc, Manila', '1070 Crisostomo St., Sampaloc, Manila', 'Norcross', 'US', '30092'),
(65, 'Krysstin Dyan Manaog Nunag', 'krysstindyan.nunag@tup.edu.ph', '1070 Crisostomo St., Sampaloc, Manila', '1143 Kuhl Avenue', 'Metro Manila', 'US', '1008', '8520000010', 'Albert M Dunford', '1070 Crisostomo St., Sampaloc, Manila', '1070 Crisostomo St., Sampaloc, Manila', 'Norcross', 'US', '30092'),
(66, 'Krysstin Dyan Manaog Nunag', 'krysstindyan.nunag@tup.edu.ph', '1070 Crisostomo St., Sampaloc, Manila', '1143 Kuhl Avenue', 'Metro Manila', 'US', '1008', '8520000010', 'Albert M Dunford', '1070 Crisostomo St., Sampaloc, Manila', '1070 Crisostomo St., Sampaloc, Manila', 'Norcross', 'US', '30092'),
(67, 'asd', 'asd@ads.com', 'asd', '', 'ads', 'asd', '999', '999999', 'asd', 'asd', '', 'ads', 'asd', '999');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `password`) VALUES
(1, 'kd', 'admin', 'kd.keydi22@gmail.com', '09078049176', '7488e331b8b64e5794da3fa4eb10ad5d'),
(2, 'Krysstin Dyan Manaog Nunag', 'admin1', 'krysstindyan.nunag@tup.edu.ph', '09078049176', '7488e331b8b64e5794da3fa4eb10ad5d');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `store_customers`
--
ALTER TABLE `store_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1019;

--
-- AUTO_INCREMENT for table `store_customers`
--
ALTER TABLE `store_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
