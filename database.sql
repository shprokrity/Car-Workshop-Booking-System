-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2025 at 09:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `car_workshop`
--
CREATE DATABASE IF NOT EXISTS `car_workshop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `car_workshop`;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `mechanic_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `car_license` varchar(50) DEFAULT NULL,
  `car_engine` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `mechanic_id`, `appointment_date`, `car_license`, `car_engine`, `phone`, `status`) VALUES
(2, 3, 1, '2025-08-12', '12-34-68', '123456788', '017111111111', 'confirmed'),
(3, 4, 1, '2025-08-12', '12-34-687', '12345677777', '4r45666777', 'confirmed'),
(5, 6, 2, '2025-08-12', '12-34-68', '12345677777', '4r45666777', 'confirmed'),
(8, 5, 1, '2025-08-15', '12-34-687', '123456', '4r45666777', 'confirmed'),
(9, 8, 1, '2025-08-15', '12-34-687', '123456', '4r45666777', 'confirmed'),
(10, 2, 1, '2025-08-21', '12-34-68', '12345677777', '4r45666777', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `mechanics`
--

CREATE TABLE `mechanics` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `availability` int(11) DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanics`
--

INSERT INTO `mechanics` (`id`, `name`, `availability`) VALUES
(1, 'Tawkir Arifin', 4),
(2, 'Tawsif Islam', 4),
(3, 'Saiyara Iffat', 4),
(4, 'David Dowland', 4),
(5, 'Emily Khan', 4);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 6, 'Booking confirmed for 2025-08-12 with mechanic ID 2.', 0, '2025-08-12 09:37:07'),
(2, 5, 'Your booking has been cancelled.', 1, '2025-08-15 15:57:57'),
(3, 5, 'Your booking has been cancelled.', 1, '2025-08-15 15:58:03'),
(4, 5, 'Booking confirmed for 2025-08-15 with mechanic ID 1.', 1, '2025-08-15 15:58:22'),
(5, 5, 'Your booking has been cancelled.', 1, '2025-08-15 16:06:25'),
(6, 5, 'Your booking has been cancelled.', 1, '2025-08-15 16:06:35'),
(7, 5, 'Booking confirmed for 2025-08-15 with mechanic ID 1.', 1, '2025-08-15 16:06:49'),
(8, 5, 'Your booking has been cancelled.', 1, '2025-08-15 16:10:32'),
(9, 5, 'Your booking has been cancelled.', 1, '2025-08-15 16:10:35'),
(10, 5, 'Your booking has been cancelled.', 1, '2025-08-15 16:10:40'),
(11, 5, 'Booking confirmed for 2025-08-15 with mechanic Tawkir Arifin.', 1, '2025-08-15 16:42:00'),
(12, 8, 'Booking confirmed for 2025-08-15 with mechanic Tawkir Arifin.', 1, '2025-08-15 16:54:00'),
(13, 2, 'Your booking has been cancelled.', 1, '2025-08-15 16:57:00'),
(14, 2, 'Booking confirmed for 2025-08-21 with mechanic Tawkir Arifin.', 1, '2025-08-15 17:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `repairs`
--

CREATE TABLE `repairs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('Available','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repairs`
--

INSERT INTO `repairs` (`id`, `name`, `image_url`, `status`) VALUES
(1, 'Engine Repair', 'https://images.unsplash.com/photo-1605379399642-870262d3d051', 'Available'),
(2, 'Brake Service', 'https://images.unsplash.com/photo-1614270186804-4a1af0ed32db', 'Available'),
(3, 'Oil Change', 'https://images.unsplash.com/photo-1604147706283-2d7d54f9d2f1', 'Available'),
(4, 'Transmission Repair', 'https://images.unsplash.com/photo-1597764691556-fcc46a6f6bba', 'Available'),
(5, 'Wheel Alignment', 'https://images.unsplash.com/photo-1592853625600-e3f7d95e85f1', 'Unavailable');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(2, 'SUMAYA TANHA', 'sumayatanha@gmail.com', '$2y$10$DkjbEyLYNTwIvnbqXcSFOuEs2IFmK61g/2Mzk13YzJQQtdRbqBIRG', 'user'),
(3, 'Hamza Bin Mohsen', 'whatever@gmail.com', '$2y$10$8tvpbMWO3K.7luY.v1xq8u8DU.Soey/oawOtmu6wd2AFhjmkLmZ/.', 'user'),
(4, 'Tanzia Mehrin', 'tanzia@gmail.com', '$2y$10$Nalh9esFUNHxw/wPnpIWQe9wftufs6IicuoleWzYwmrbkp1C57CFi', 'user'),
(5, 'Tawkir Arifin', 'tawkir@gmail.com', '$2y$10$0sM7aq1Qgd7mfx945RKhauQPMZkdHBHP.RJVHWKTB0EBLauNv5LnK', 'user'),
(6, 'Nafis Rayan', 'nafisrayan@gmail.com', '$2y$10$0o4px1R0anx9sm6IEjPSH.zRFTkwHGGNoYonKlLjCZ3WME0iGdsiS', 'user'),
(7, 'Sumaya Hasan Prokrity', 'sumaya.hasan.prokrity@g.bracu.ac.bd', '$2y$10$WQmUlsNdr7h/mQw1V4msluSY8b9/rA7sgPyJCmGcRNTfMZKM1xtJ6', 'admin'),
(8, 'Mahi', 'mahi@gmail.com', '$2y$10$VgLmbNAYfFs8WVtmkral4e9/OnOSX.Aiy/GJZcsCT4DJIzb9bsiMK', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `mechanic_id` (`mechanic_id`);

--
-- Indexes for table `mechanics`
--
ALTER TABLE `mechanics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `repairs`
--
ALTER TABLE `repairs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
-- Add is_disabled column to users table
ALTER TABLE `users`
  ADD COLUMN `is_disabled` TINYINT(1) NOT NULL DEFAULT 0;

-- Make sure is_disabled column exists and has correct type
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `is_disabled` TINYINT(1) NOT NULL DEFAULT 0;

-- Fix any existing null values
UPDATE `users` SET `is_disabled` = 0 WHERE `is_disabled` IS NULL;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mechanics`
--
ALTER TABLE `mechanics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `repairs`
--
ALTER TABLE `repairs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"workshop_booking\",\"table\":\"bookings\"},{\"db\":\"workshop_booking\",\"table\":\"users\"},{\"db\":\"workshop_booking\",\"table\":\"mechanic_daily_availability\"},{\"db\":\"INFORMATION_SCHEMA\",\"table\":\"KEY_COLUMN_USAGE\"},{\"db\":\"workshop_booking\",\"table\":\"mechanics\"},{\"db\":\"workshop_booking\",\"table\":\"repair_services\"},{\"db\":\"workshop_db\",\"table\":\"mechanics\"},{\"db\":\"car_workshop\",\"table\":\"mechanics\"},{\"db\":\"car_workshop\",\"table\":\"users\"},{\"db\":\"car_workshop\",\"table\":\"appointments\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

--
-- Dumping data for table `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'workshop_booking', 'bookings', '{\"CREATE_TIME\":\"2025-08-26 00:44:09\",\"col_order\":[0,1,2,3,4,6,5,7,8,9,10,11,12,13,14,15],\"col_visib\":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]}', '2025-09-08 18:32:53');

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-09-14 19:51:38', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"en_GB\",\"NavigationWidth\":0}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
--
-- Database: `workshop_booking`
--
CREATE DATABASE IF NOT EXISTS `workshop_booking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `workshop_booking`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `car_license` varchar(50) NOT NULL,
  `car_engine` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mechanic_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `services` text NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `total_price` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `name`, `address`, `phone`, `car_license`, `car_engine`, `user_id`, `mechanic_id`, `service_id`, `booking_date`, `booking_time`, `status`, `total_price`, `notes`, `created_at`, `updated_at`) VALUES
(13, '', '', '', '', '', 2, 2, 5, '2025-08-21', '09:00:00', 'confirmed', 530.00, 'Services: Oil Change, Brake Repair, Engine Diagnostic, Transmission Service, Battery Replacement, Tire Rotation | Customer Notes: Kichu bolbo na :)', '2025-08-20 14:13:49', '2025-08-20 14:16:53'),
(14, '', '', '', '', '', 2, 6, 1, '2025-08-22', '14:00:00', 'confirmed', 45.00, 'Services: Oil Change', '2025-08-20 14:15:34', '2025-08-20 14:16:27'),
(15, '', '', '', '', '', 3, 1, 5, '2025-08-20', '15:18:00', 'completed', 95.00, 'Services: Battery Replacement', '2025-08-20 14:18:59', '2025-08-20 14:19:34'),
(16, '', '', '', '', '', 2, 3, 5, '2025-08-23', '12:30:00', 'confirmed', 140.00, 'Services: Oil Change, Battery Replacement', '2025-08-22 18:30:29', '2025-08-22 18:31:27'),
(17, '', '', '', '', '', 2, 2, 5, '2025-08-26', '13:03:00', 'confirmed', 180.00, 'Services: Engine Diagnostic, Battery Replacement', '2025-08-25 17:03:55', '2025-08-25 17:05:39'),
(18, '', '', '', '', '', 4, 2, 5, '2025-08-27', '10:19:00', 'pending', 95.00, 'Services: Battery Replacement', '2025-08-25 19:19:24', '2025-08-25 20:35:05'),
(19, 'Projukti', 'Uttara', '01946282670', '1245677778', '1234567899999999', 4, 2, 2, '2025-08-28', '11:37:00', 'confirmed', 120.00, 'Services: Brake Repair', '2025-08-25 20:38:01', '2025-08-25 20:51:01'),
(20, 'Tawkir Arifin', '465 North Shahjahanpur, Dhaka-1217', '01946282670', '12-34-687', '12345677777', 4, 6, 5, '2025-08-29', '10:13:00', 'pending', 245.00, 'Services: Transmission Service, Battery Replacement', '2025-08-25 21:13:31', '2025-08-25 21:13:31'),
(21, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 4, 6, 5, '2025-08-30', '10:13:00', 'confirmed', 180.00, 'Services: Engine Diagnostic, Battery Replacement', '2025-08-25 21:15:14', '2025-09-07 08:16:28'),
(22, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 2, 3, 5, '2025-09-07', '14:53:00', 'confirmed', 140.00, 'Services: Oil Change, Battery Replacement', '2025-09-07 08:03:32', '2025-09-07 08:16:37'),
(25, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 2, 7, 5, '2025-09-10', '15:07:00', 'confirmed', 215.00, 'Services: Brake Repair, Battery Replacement', '2025-09-08 18:08:10', '2025-09-08 18:26:00'),
(26, '', '', '', '', '', 2, 7, 5, '2025-09-12', '15:54:00', 'confirmed', 95.00, 'Services: Battery Replacement', '2025-09-08 18:55:01', '2025-09-08 19:03:33'),
(27, 'Sumaya Hasan Prokrity', 'House - 18, Road-05, Abdullahbag, Shatarkul Road, Uttar Badda, Dhaka-1212', '01639660001', '123456666666', '23456', 2, 3, 5, '2025-09-13', '10:04:00', 'confirmed', 180.00, 'Services: Engine Diagnostic, Battery Replacement', '2025-09-08 19:04:28', '2025-09-08 19:12:24'),
(28, 'Sumaya Hasan Prokrity', 'House - 18, Road-05, Abdullahbag, Shatarkul Road, Uttar Badda, Dhaka-1212', '01639660001', '123456666666', '23456', 2, 6, 1, '2025-09-23', '16:13:00', 'confirmed', 45.00, 'Services: Oil Change', '2025-09-08 19:13:09', '2025-09-08 19:13:32'),
(29, 'Sumaya Hasan Prokrity', 'House - 18, Road-05, Abdullahbag, Shatarkul Road, Uttar Badda, Dhaka-1212', '01639660001', '44444444', '5555555', 2, 1, 5, '2025-09-23', '11:30:00', 'confirmed', 375.00, 'Services: Oil Change, Engine Diagnostic, Transmission Service, Battery Replacement', '2025-09-08 19:31:02', '2025-09-08 19:32:45'),
(30, 'Prokrity Hasan', '465 North Shahjahanpur, Dhaka-1217', '01946282670', '123456', '123456', 2, 2, 5, '2025-09-15', '11:32:00', 'pending', 245.00, 'Services: Transmission Service, Battery Replacement | Customer Notes: okay', '2025-09-14 17:32:58', '2025-09-14 17:32:58'),
(31, 'Prokrity Hasan', 'House - 18, Road-05, Abdullahbag, Shatarkul Road, Uttar Badda, Dhaka-1212', '01946282670', '12-34-69', '12345677777', 2, 2, 2, '2025-09-15', '11:50:00', 'pending', 120.00, 'Services: Brake Repair | Customer Notes: dmdns', '2025-09-14 17:50:54', '2025-09-14 17:50:54'),
(32, 'Prokrity Hasan', 'House - 18, Road-05, Abdullahbag, Shatarkul Road, Uttar Badda, Dhaka-1212', '01946282670', '123456', '123456', 2, 6, 2, '2025-09-19', '11:54:00', 'confirmed', 120.00, 'Services: Brake Repair', '2025-09-14 17:54:24', '2025-09-14 18:02:36');

--
-- Triggers `bookings`
--
DELIMITER $$
CREATE TRIGGER `update_daily_availability_after_booking_delete` AFTER DELETE ON `bookings` FOR EACH ROW BEGIN
    IF OLD.status IN ('pending', 'confirmed') THEN
        UPDATE mechanic_daily_availability 
        SET current_bookings = GREATEST(current_bookings - 1, 0),
            status = CASE 
                WHEN current_bookings - 1 < max_bookings THEN 'available'
                ELSE status
            END
        WHERE mechanic_id = OLD.mechanic_id AND availability_date = OLD.booking_date;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_daily_availability_after_booking_insert` AFTER INSERT ON `bookings` FOR EACH ROW BEGIN
    INSERT INTO mechanic_daily_availability (mechanic_id, availability_date, current_bookings)
    VALUES (NEW.mechanic_id, NEW.booking_date, 1)
    ON DUPLICATE KEY UPDATE 
        current_bookings = current_bookings + 1,
        status = CASE 
            WHEN current_bookings + 1 >= max_bookings THEN 'full'
            ELSE 'available'
        END;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_daily_availability_after_booking_update` AFTER UPDATE ON `bookings` FOR EACH ROW BEGIN
    -- Handle status changes (cancelled, completed, etc.)
    IF OLD.status IN ('pending', 'confirmed') AND NEW.status NOT IN ('pending', 'confirmed') THEN
        -- Booking was cancelled or completed, decrease count
        UPDATE mechanic_daily_availability 
        SET current_bookings = GREATEST(current_bookings - 1, 0),
            status = CASE 
                WHEN current_bookings - 1 < max_bookings THEN 'available'
                ELSE status
            END
        WHERE mechanic_id = OLD.mechanic_id AND availability_date = OLD.booking_date;
    ELSEIF OLD.status NOT IN ('pending', 'confirmed') AND NEW.status IN ('pending', 'confirmed') THEN
        -- Booking was reactivated, increase count
        INSERT INTO mechanic_daily_availability (mechanic_id, availability_date, current_bookings)
        VALUES (NEW.mechanic_id, NEW.booking_date, 1)
        ON DUPLICATE KEY UPDATE 
            current_bookings = current_bookings + 1,
            status = CASE 
                WHEN current_bookings + 1 >= max_bookings THEN 'full'
                ELSE 'available'
            END;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mechanics`
--

CREATE TABLE `mechanics` (
  `mechanic_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `current_orders` int(11) DEFAULT 0,
  `order_date` date DEFAULT NULL,
  `max_orders` int(11) DEFAULT 4,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanics`
--

INSERT INTO `mechanics` (`mechanic_id`, `name`, `specialty`, `email`, `phone`, `status`, `current_orders`, `order_date`, `max_orders`, `created_at`) VALUES
(1, 'Tawkir Arifin\r\n', 'Engine Specialist', 'tawkirarifin@gmail.com', '01911111111', 'available', 0, NULL, 4, '2025-08-16 16:35:49'),
(2, 'Nafis Rayan', 'Brake Expert', 'nafis@gmail.com', '01911111111', 'available', 0, NULL, 4, '2025-08-16 16:35:49'),
(3, 'Tashin Rahman', 'Transmission Specialist', 'tashin@gmail.com', '01911111111', 'available', 0, NULL, 4, '2025-08-16 16:35:49'),
(4, 'Tawsif Islam\r\n', 'Electrical Systems', 'tawsif@gmail.com', '01911111111', 'available', 0, NULL, 4, '2025-08-16 16:35:49'),
(6, 'Saiyara Iffat', 'Electrical Systems', 'saiyaraiffat@gmail.com', '01911111111', 'available', 1, '2025-08-30', 4, '2025-08-18 18:38:20'),
(7, 'Amily Khan', 'Electrical Systems', 'amilykhan@gmail.com', '01911111111', 'unavailable', 0, NULL, 4, '2025-08-18 18:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `mechanic_daily_availability`
--

CREATE TABLE `mechanic_daily_availability` (
  `id` int(11) NOT NULL,
  `mechanic_id` int(11) NOT NULL,
  `availability_date` date NOT NULL,
  `current_bookings` int(11) DEFAULT 0,
  `max_bookings` int(11) DEFAULT 4,
  `status` enum('available','unavailable','full') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanic_daily_availability`
--

INSERT INTO `mechanic_daily_availability` (`id`, `mechanic_id`, `availability_date`, `current_bookings`, `max_bookings`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-08-21', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(2, 2, '2025-08-26', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(3, 2, '2025-08-27', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(4, 2, '2025-08-28', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(5, 3, '2025-08-23', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(6, 6, '2025-08-22', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(7, 6, '2025-08-29', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(8, 6, '2025-08-30', 1, 4, 'available', '2025-08-26 06:31:08', '2025-08-26 06:31:08'),
(9, 3, '2025-09-07', 1, 4, 'available', '2025-09-07 08:03:32', '2025-09-07 08:16:37'),
(11, 6, '2025-09-10', 1, 4, 'available', '2025-09-08 18:08:10', '2025-09-08 18:08:10'),
(12, 2, '2025-09-12', 1, 4, 'available', '2025-09-08 18:55:01', '2025-09-08 18:55:01'),
(13, 7, '2025-09-12', 1, 4, 'available', '2025-09-08 19:03:33', '2025-09-08 19:03:33'),
(14, 3, '2025-09-13', 1, 4, 'available', '2025-09-08 19:04:28', '2025-09-08 19:04:28'),
(15, 6, '2025-09-23', 1, 4, 'available', '2025-09-08 19:13:09', '2025-09-08 19:13:09'),
(16, 1, '2025-09-23', 1, 4, 'available', '2025-09-08 19:31:02', '2025-09-08 19:31:02'),
(17, 2, '2025-09-15', 2, 4, 'available', '2025-09-14 17:32:58', '2025-09-14 17:50:54'),
(19, 6, '2025-09-19', 1, 4, 'available', '2025-09-14 17:54:24', '2025-09-14 17:54:24');

-- --------------------------------------------------------

--
-- Table structure for table `repair_services`
--

CREATE TABLE `repair_services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_services`
--

INSERT INTO `repair_services` (`id`, `service_name`, `description`, `price`, `duration_hours`, `status`, `created_at`) VALUES
(1, 'Oil Change', 'Complete oil and filter change', 45.00, 1, 'available', '2025-08-16 16:35:49'),
(2, 'Brake Repair', 'Brake pad and rotor replacement', 120.00, 2, 'available', '2025-08-16 16:35:49'),
(3, 'Engine Diagnostic', 'Complete engine diagnostic check', 85.00, 1, 'available', '2025-08-16 16:35:49'),
(4, 'Transmission Service', 'Transmission fluid change and inspection', 150.00, 2, 'available', '2025-08-16 16:35:49'),
(5, 'Battery Replacement', 'Car battery replacement and testing', 95.00, 1, 'available', '2025-08-16 16:35:49'),
(6, 'Tire Rotation', 'Complete tire rotation and balancing', 35.00, 1, 'available', '2025-08-16 16:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `address`, `phone`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, '', '', '', 'admin', 'admin@workshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-08-16 16:35:49'),
(2, '', '', '', 'Prokrity', 'sumaya.hasan.prokrity@g.bracu.ac.bd', '$2y$10$MCsBybt3rI/LsKAVDZK3we/uzIvufUIZPWKaIOFAUsnWJwuGQEHfe', 'user', '2025-08-16 16:39:53'),
(3, '', '', '', 'Sumaya The Admin', 'sumayahasanprokrity@gmail.com', '$2y$10$7Sr4upck2vNvH1hBqv1tA.E2sZjswOMnBxBs7D6yA.gMRD4OubiK.', 'admin', '2025-08-16 16:44:09'),
(4, 'Projukti', 'Uttar Badda', '01639660001', 'projukti123', 'projukti@gmail.com', '$2y$10$uzpgxSdw3Ok.JYvIl0va8OldBuxFUHGWxhU3fY1emLri7ElQL7hwS', 'user', '2025-08-25 18:58:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `mechanic_id` (`mechanic_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `mechanics`
--
ALTER TABLE `mechanics`
  ADD PRIMARY KEY (`mechanic_id`);

--
-- Indexes for table `mechanic_daily_availability`
--
ALTER TABLE `mechanic_daily_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_mechanic_date` (`mechanic_id`,`availability_date`);

--
-- Indexes for table `repair_services`
--
ALTER TABLE `repair_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `mechanic_daily_availability`
--
ALTER TABLE `mechanic_daily_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `repair_services`
--
ALTER TABLE `repair_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics` (`mechanic_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `repair_services` (`id`);

--
-- Constraints for table `mechanic_daily_availability`
--
ALTER TABLE `mechanic_daily_availability`
  ADD CONSTRAINT `mechanic_daily_availability_ibfk_1` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics` (`mechanic_id`);
--
-- Database: `workshop_db`
--
CREATE DATABASE IF NOT EXISTS `workshop_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `workshop_db`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
