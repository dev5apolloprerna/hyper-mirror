-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 27, 2026 at 06:06 PM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hyper-mirror`
--

-- --------------------------------------------------------

--
-- Table structure for table `crm_roles`
--

DROP TABLE IF EXISTS `crm_roles`;
CREATE TABLE IF NOT EXISTS `crm_roles` (
  `iRoleId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `strRole` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iRoleId`),
  UNIQUE KEY `uk_crm_roles_role` (`strRole`),
  UNIQUE KEY `uk_crm_roles_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_roles`
--

INSERT INTO `crm_roles` (`iRoleId`, `strRole`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'StoreManager', 'storemanager', '2026-03-11 09:39:51', '2026-03-11 09:39:51'),
(2, 'Measurement', 'measurement', '2026-03-11 09:39:51', '2026-03-11 09:39:51'),
(3, 'Production', 'production', '2026-03-11 09:39:51', '2026-03-11 09:39:51'),
(4, 'Dispatch', 'dispatch', '2026-03-11 09:39:51', '2026-03-11 09:39:51'),
(5, 'Fitting', 'fitting', '2026-03-11 09:39:51', '2026-03-11 09:39:51'),
(6, 'Account', 'account', '2026-03-11 09:39:51', '2026-03-11 09:39:51');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `iCustomerId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `strCustomer` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strMobile` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strAddress` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iCustomerId`),
  UNIQUE KEY `uk_customers_mobile` (`strMobile`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`iCustomerId`, `strCustomer`, `strMobile`, `strAddress`, `created_at`, `updated_at`) VALUES
(3, 'Bansari Patel', '9987654321', 'Sola', '2026-03-18 09:45:44', '2026-03-21 07:10:17');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
CREATE TABLE IF NOT EXISTS `leads` (
  `iLeadId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `iCustomerId` bigint UNSIGNED NOT NULL,
  `iCurrentYearLeadId` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strLeadNo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IsMeasureMentRequired` tinyint NOT NULL DEFAULT '0',
  `MeasurementVisitDate` date DEFAULT NULL,
  `SiteAddress` text COLLATE utf8mb4_unicode_ci,
  `CreatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `iCurrentLeadStatus` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NetFollowupdate` date DEFAULT NULL,
  `isFittingLeadOnly` tinyint NOT NULL DEFAULT '0',
  `isFittingRequired` tinyint NOT NULL DEFAULT '0',
  `isFittingChargeIncluded` tinyint NOT NULL DEFAULT '0',
  `iFittingCharges` int NOT NULL DEFAULT '0',
  `iLeadAmount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `iQuotationId` bigint UNSIGNED DEFAULT NULL,
  `iCreatedBy` bigint UNSIGNED DEFAULT NULL,
  `iShowroomId` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iLeadId`),
  UNIQUE KEY `uk_leads_no` (`strLeadNo`),
  KEY `idx_leads_customer` (`iCustomerId`),
  KEY `idx_leads_created_by` (`iCreatedBy`),
  KEY `idx_leads_showroom` (`iShowroomId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`iLeadId`, `iCustomerId`, `iCurrentYearLeadId`, `strLeadNo`, `IsMeasureMentRequired`, `MeasurementVisitDate`, `SiteAddress`, `CreatedDate`, `iCurrentLeadStatus`, `NetFollowupdate`, `isFittingLeadOnly`, `isFittingRequired`, `isFittingChargeIncluded`, `iFittingCharges`, `iLeadAmount`, `iQuotationId`, `iCreatedBy`, `iShowroomId`, `created_at`, `updated_at`) VALUES
(2, 3, '2627', '2627-0001', 1, '2026-03-23', 'Science City', '2026-03-21 07:10:17', 'In Measurement', '2026-03-23', 0, 0, 0, 0, '0.00', NULL, 26, NULL, '2026-03-21 07:10:17', '2026-03-21 07:10:17'),
(3, 3, '2627', '2627-0002', 1, '2026-03-26', 'Science City', '2026-03-25 12:49:06', 'Quotation Sent', '2026-03-26', 0, 1, 0, 0, '13564200.00', 2, 24, NULL, '2026-03-25 12:49:06', '2026-03-27 07:11:18');

-- --------------------------------------------------------

--
-- Table structure for table `lead_designs`
--

DROP TABLE IF EXISTS `lead_designs`;
CREATE TABLE IF NOT EXISTS `lead_designs` (
  `iLeadDesignId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `iLeadId` bigint UNSIGNED NOT NULL,
  `strFilename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strTitle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iLeadDesignId`),
  KEY `idx_lead_designs_lead` (`iLeadId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_histories`
--

DROP TABLE IF EXISTS `lead_histories`;
CREATE TABLE IF NOT EXISTS `lead_histories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `iLeadId` bigint UNSIGNED NOT NULL,
  `strComments` text COLLATE utf8mb4_unicode_ci,
  `NetFolloupwdate` date DEFAULT NULL,
  `iStatus` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iEnterBy` bigint UNSIGNED NOT NULL,
  `EntryDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_histories_lead` (`iLeadId`),
  KEY `idx_lead_histories_user` (`iEnterBy`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_histories`
--

INSERT INTO `lead_histories` (`id`, `iLeadId`, `strComments`, `NetFolloupwdate`, `iStatus`, `iEnterBy`, `EntryDate`, `created_at`, `updated_at`) VALUES
(2, 2, 'Lead created.', '2026-03-23', 'In Measurement', 26, '2026-03-21 07:10:17', '2026-03-21 07:10:17', '2026-03-21 07:10:17'),
(3, 3, 'Lead created.', '2026-03-26', 'In Measurement', 24, '2026-03-25 12:49:06', '2026-03-25 12:49:06', '2026-03-25 12:49:06'),
(4, 3, 'Quotation generated.', '2026-03-26', 'Quotation Sent', 24, '2026-03-27 07:11:18', '2026-03-27 07:11:18', '2026-03-27 07:11:18');

-- --------------------------------------------------------

--
-- Table structure for table `lead_payments`
--

DROP TABLE IF EXISTS `lead_payments`;
CREATE TABLE IF NOT EXISTS `lead_payments` (
  `iLeadPaymentId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `iLeadId` bigint UNSIGNED NOT NULL,
  `iPaidAmount` decimal(16,2) NOT NULL,
  `PaymentDate` date NOT NULL,
  `PaymentMode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iUserID` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iLeadPaymentId`),
  KEY `idx_lead_payments_lead` (`iLeadId`),
  KEY `idx_lead_payments_user` (`iUserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotations`
--

DROP TABLE IF EXISTS `lead_quotations`;
CREATE TABLE IF NOT EXISTS `lead_quotations` (
  `iQuotationId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `iLeadId` bigint UNSIGNED NOT NULL,
  `iProductCategoryId` bigint UNSIGNED NOT NULL,
  `iProductId` bigint UNSIGNED NOT NULL,
  `unit_of_measurement` enum('inch','MM','Feet') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape_id` bigint UNSIGNED DEFAULT NULL,
  `feature_id` bigint UNSIGNED DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `decHeight` decimal(16,2) NOT NULL,
  `decWidth` decimal(16,2) NOT NULL,
  `decTotalSqft` decimal(16,2) NOT NULL,
  `decRatePerSqft` decimal(16,2) NOT NULL,
  `iAmount` decimal(16,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iQuotationId`),
  KEY `idx_lead_quotations_lead` (`iLeadId`),
  KEY `idx_lead_quotations_category` (`iProductCategoryId`),
  KEY `idx_lead_quotations_product` (`iProductId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_quotations`
--

INSERT INTO `lead_quotations` (`iQuotationId`, `iLeadId`, `iProductCategoryId`, `iProductId`, `unit_of_measurement`, `shape_id`, `feature_id`, `remarks`, `quantity`, `decHeight`, `decWidth`, `decTotalSqft`, `decRatePerSqft`, `iAmount`, `created_at`, `updated_at`) VALUES
(2, 3, 1, 1, 'inch', 4, 5, NULL, 1, '1222.00', '111.00', '135642.00', '100.00', '13564200.00', '2026-03-27 07:11:18', '2026-03-27 07:11:18');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `iProductId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `iCategoryId` bigint UNSIGNED NOT NULL,
  `strProductName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MRP` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iProductId`),
  KEY `idx_products_category` (`iCategoryId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`iProductId`, `iCategoryId`, `strProductName`, `MRP`, `created_at`, `updated_at`) VALUES
(1, 1, 'product 2', 100, '2026-03-12 07:17:53', '2026-03-12 07:17:53'),
(2, 1, 'product 3', 100, '2026-03-12 07:17:53', '2026-03-12 07:17:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE IF NOT EXISTS `product_categories` (
  `iCategoryId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `strCategoryName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iCategoryId`),
  UNIQUE KEY `uk_product_categories_name` (`strCategoryName`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`iCategoryId`, `strCategoryName`, `created_at`, `updated_at`) VALUES
(1, 'Plain Mirror', '2026-03-11 10:57:03', '2026-03-11 10:57:03'),
(2, 'NC Colour Mirror', NULL, NULL),
(3, 'V-Groove Mirror', NULL, NULL),
(4, 'LED Mirror\r\n', NULL, NULL),
(5, 'WPC / WPVC Frame Mirror\r\n', NULL, NULL),
(6, 'Aluminium Frame Mirror\r\n', NULL, NULL),
(7, 'MS Frame Mirror\r\n', NULL, NULL),
(8, 'SS Frame Mirror\r\n', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_feature`
--

DROP TABLE IF EXISTS `product_feature`;
CREATE TABLE IF NOT EXISTS `product_feature` (
  `feature_id` int NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(100) NOT NULL,
  `iStatus` int NOT NULL DEFAULT '1',
  `isDelete` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`feature_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_feature`
--

INSERT INTO `product_feature` (`feature_id`, `feature_name`, `iStatus`, `isDelete`) VALUES
(1, 'STS (Single Touch Sensor)', 1, 0),
(2, '3TS (Three Touch Sensor)', 1, 0),
(3, 'Motion Sensor', 1, 0),
(4, 'Hand Wave', 1, 0),
(5, 'Anti Fog', 1, 0),
(6, 'Custom Feature', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_shape`
--

DROP TABLE IF EXISTS `product_shape`;
CREATE TABLE IF NOT EXISTS `product_shape` (
  `shape_id` int NOT NULL AUTO_INCREMENT,
  `shape_title` varchar(100) NOT NULL,
  `iStatus` int NOT NULL DEFAULT '1',
  `isDelete` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`shape_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_shape`
--

INSERT INTO `product_shape` (`shape_id`, `shape_title`, `iStatus`, `isDelete`) VALUES
(1, 'Rectangle', 1, 0),
(2, 'Square', 1, 0),
(3, 'Oval', 1, 0),
(4, 'Capsule', 1, 0),
(5, 'Corner Round Rectangle', 1, 0),
(6, 'Triangle', 1, 0),
(7, 'Hexagon', 1, 0),
(8, 'Octagon', 1, 0),
(9, 'Arch', 1, 0),
(10, 'Semi Circle', 1, 0),
(11, 'Diamond', 1, 0),
(12, 'Trapezium', 1, 0),
(13, 'Custom Shape', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2022-09-12 04:33:06', '2022-09-12 04:33:06'),
(2, 'Employee', 'web', '2022-09-12 04:33:06', '2022-09-12 04:33:06'),
(3, 'Vendor', 'web', '2022-09-12 04:33:06', '2022-09-12 04:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sendemaildetails`
--

DROP TABLE IF EXISTS `sendemaildetails`;
CREATE TABLE IF NOT EXISTS `sendemaildetails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `strSubject` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `strTitle` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `strFromMail` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ToMail` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `strCC` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `strBCC` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `sendemaildetails`
--

INSERT INTO `sendemaildetails` (`id`, `strSubject`, `strTitle`, `strFromMail`, `ToMail`, `strCC`, `strBCC`) VALUES
(4, 'Contact Inquiry', 'Sukti', 'support@sukti.in', NULL, '', ''),
(8, 'Forget Password', 'Sukti', 'support@sukti.in', NULL, NULL, NULL),
(9, 'sign_up', 'Sukti', 'support@sukti.in', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
CREATE TABLE IF NOT EXISTS `setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sitename` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iStatus` int NOT NULL DEFAULT '1',
  `isDelete` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `strIP` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `sitename`, `logo`, `email`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `strIP`) VALUES
(1, 'Jewellery crm', '1746446528.png', 'dev5.apolloinfotech@gmail.com', 1, 0, '2025-05-05 12:02:08', NULL, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `showrooms`
--

DROP TABLE IF EXISTS `showrooms`;
CREATE TABLE IF NOT EXISTS `showrooms` (
  `iShowroomId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `strShowRoomName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iShowroomId`),
  UNIQUE KEY `uk_showrooms_name` (`strShowRoomName`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `showrooms`
--

INSERT INTO `showrooms` (`iShowroomId`, `strShowRoomName`, `created_at`, `updated_at`) VALUES
(1, 'Main Showroom', '2026-03-11 09:40:03', '2026-03-11 09:40:03'),
(2, 'Branch Showroom', '2026-03-11 09:40:03', '2026-03-11 09:40:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `strUserName` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strUserMobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strUserAddress` text COLLATE utf8mb4_unicode_ci,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` int NOT NULL DEFAULT '2' COMMENT '1=Admin, 2=TA/TP',
  `iRoalId` bigint UNSIGNED DEFAULT NULL,
  `otp` int DEFAULT NULL,
  `otpTimeOut` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `uk_users_strUserMobile` (`strUserMobile`),
  KEY `fk_users_crm_role` (`iRoalId`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `strUserName`, `first_name`, `last_name`, `email`, `mobile_number`, `strUserMobile`, `strUserAddress`, `email_verified_at`, `password`, `role_id`, `iRoalId`, `otp`, `otpTimeOut`, `status`, `remember_token`, `device_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Super', 'admin', 'admin@admin.com', '9876543210', NULL, NULL, NULL, '$2y$10$sPrSb4x/ajMNN4OAnT6pLe4jQXOovPn.05aQ9HlpTA5faYqRTUilO', 1, NULL, NULL, NULL, 1, NULL, NULL, '2022-09-12 04:33:06', '2025-07-14 09:48:46'),
(24, 'prerna', 'prerna', NULL, 'dev5.apolloinfotech@gmail.com', '9723391747', '9723391747', '3/2, Barnaby Road, Kilpauk,', NULL, '$2y$10$GxapfxRQWRN5OsOM6OHKSeKKHkGSx50n1wSeRJNI5/br1tYq08Fze', 2, 1, NULL, NULL, 1, NULL, NULL, '2026-03-18 10:02:13', '2026-03-25 07:21:00'),
(25, 'jlkjlkj', 'jlkjlkj', NULL, 'dev1.apolloinfotech@gmail.com', '09987654321', '09987654321', 'Sola\r\nScience City', NULL, '$2y$10$bVJOibUYPQhcr1acQXeRCOj1Ucw7Nygt1wAe26LC9jKHZSDs7iJLW', 2, 3, NULL, NULL, 1, NULL, NULL, '2026-03-18 10:16:13', '2026-03-26 09:46:25'),
(26, 'test user', 'test user', NULL, 'dev4.apolloinfotech@gmail.com', '09790954014', '09790954014', '3/2, Barnaby Road, Kilpauk,', NULL, '$2y$10$V82Vp9qq5W995KZ15avlZONRqPDeQYeATt/NrIMy3EdDes37VdikC', 2, 2, NULL, NULL, 1, NULL, NULL, '2026-03-20 12:02:05', '2026-03-26 09:46:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_showrooms`
--

DROP TABLE IF EXISTS `user_showrooms`;
CREATE TABLE IF NOT EXISTS `user_showrooms` (
  `UserShowRoomId` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserId` bigint UNSIGNED NOT NULL,
  `ShowRoomId` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`UserShowRoomId`),
  UNIQUE KEY `uk_user_showroom` (`UserId`,`ShowRoomId`),
  KEY `idx_user_showrooms_showroom` (`ShowRoomId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_showrooms`
--

INSERT INTO `user_showrooms` (`UserShowRoomId`, `UserId`, `ShowRoomId`, `created_at`, `updated_at`) VALUES
(2, 24, 2, NULL, NULL),
(3, 24, 1, NULL, NULL),
(4, 25, 2, NULL, NULL),
(5, 25, 1, NULL, NULL),
(6, 26, 2, NULL, NULL),
(7, 26, 1, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `fk_leads_customer` FOREIGN KEY (`iCustomerId`) REFERENCES `customers` (`iCustomerId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_leads_showroom` FOREIGN KEY (`iShowroomId`) REFERENCES `showrooms` (`iShowroomId`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_leads_user` FOREIGN KEY (`iCreatedBy`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_designs`
--
ALTER TABLE `lead_designs`
  ADD CONSTRAINT `fk_lead_designs_lead` FOREIGN KEY (`iLeadId`) REFERENCES `leads` (`iLeadId`) ON DELETE CASCADE;

--
-- Constraints for table `lead_histories`
--
ALTER TABLE `lead_histories`
  ADD CONSTRAINT `fk_lead_histories_lead` FOREIGN KEY (`iLeadId`) REFERENCES `leads` (`iLeadId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lead_histories_user` FOREIGN KEY (`iEnterBy`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_payments`
--
ALTER TABLE `lead_payments`
  ADD CONSTRAINT `fk_lead_payments_lead` FOREIGN KEY (`iLeadId`) REFERENCES `leads` (`iLeadId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lead_payments_user` FOREIGN KEY (`iUserID`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_quotations`
--
ALTER TABLE `lead_quotations`
  ADD CONSTRAINT `fk_lead_quotations_category` FOREIGN KEY (`iProductCategoryId`) REFERENCES `product_categories` (`iCategoryId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lead_quotations_lead` FOREIGN KEY (`iLeadId`) REFERENCES `leads` (`iLeadId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lead_quotations_product` FOREIGN KEY (`iProductId`) REFERENCES `products` (`iProductId`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`iCategoryId`) REFERENCES `product_categories` (`iCategoryId`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_crm_role` FOREIGN KEY (`iRoalId`) REFERENCES `crm_roles` (`iRoleId`) ON DELETE SET NULL;

--
-- Constraints for table `user_showrooms`
--
ALTER TABLE `user_showrooms`
  ADD CONSTRAINT `fk_user_showrooms_showroom` FOREIGN KEY (`ShowRoomId`) REFERENCES `showrooms` (`iShowroomId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_showrooms_user` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
