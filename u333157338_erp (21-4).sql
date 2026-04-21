-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2026 at 11:46 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u333157338_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_payments_collection`
--

CREATE TABLE `account_payments_collection` (
  `account_payment_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `payment_mode` int(11) DEFAULT 0,
  `payment_date` datetime DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `is_Delete_recode` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_payments_collection`
--

INSERT INTO `account_payments_collection` (`account_payment_id`, `emp_id`, `amount`, `payment_mode`, `payment_date`, `comment`, `created_at`, `updated_at`, `user_id`, `is_Delete_recode`) VALUES
(1, 30, 2500, 0, '2026-04-18 17:50:02', 'payment rec', '2026-04-18 17:50:02', NULL, 32, NULL),
(2, 30, 12900, 0, '2026-04-18 17:51:19', 'rec', '2026-04-18 17:51:19', NULL, 32, NULL),
(3, 30, 25000, 0, '2026-04-18 17:54:42', NULL, '2026-04-18 17:54:42', NULL, 32, NULL),
(4, 30, 5000, 0, '2026-04-18 17:54:58', NULL, '2026-04-18 17:54:58', NULL, 32, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_payments_collection`
--

CREATE TABLE `admin_payments_collection` (
  `admin_payment_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `payment_mode` int(11) NOT NULL DEFAULT 0,
  `payment_date` timestamp NULL DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `is_Delete_recode` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_payments_collection`
--

INSERT INTO `admin_payments_collection` (`admin_payment_id`, `emp_id`, `amount`, `payment_mode`, `payment_date`, `comment`, `created_at`, `updated_at`, `user_id`, `is_Delete_recode`) VALUES
(1, 32, 10000, 0, '2026-04-18 16:32:02', '10k recd', '2026-04-18 16:32:02', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cash_payment_ledger`
--

CREATE TABLE `cash_payment_ledger` (
  `cash_payment_ledger_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL DEFAULT 0,
  `invoices_Id` int(11) NOT NULL DEFAULT 0,
  `open` int(11) NOT NULL DEFAULT 0,
  `credit` int(11) NOT NULL DEFAULT 0,
  `debit` int(11) NOT NULL DEFAULT 0,
  `close` int(11) NOT NULL DEFAULT 0,
  `credit_emp_id` int(11) NOT NULL DEFAULT 0,
  `debit_emp_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `UserType` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_payment_ledger`
--

INSERT INTO `cash_payment_ledger` (`cash_payment_ledger_id`, `emp_id`, `invoices_Id`, `open`, `credit`, `debit`, `close`, `credit_emp_id`, `debit_emp_id`, `created_at`, `updated_at`, `UserType`) VALUES
(1, 30, 1, 0, 5000, 0, 5000, 30, 0, '2026-04-11 18:25:33', '2026-04-11 18:25:33', 1),
(2, 32, 2, 0, 24, 0, 24, 32, 0, '2026-04-11 18:37:38', '2026-04-11 18:37:38', 2),
(3, 30, 2, 5000, 500, 0, 5500, 30, 0, '2026-04-12 15:14:45', '2026-04-12 15:14:45', 1),
(4, 32, 7, 24, 20000000, 0, 20000024, 32, 0, '2026-04-12 16:20:33', '2026-04-12 16:20:33', 2),
(5, 32, 3, 20000024, 5001, 0, 20005025, 32, 0, '2026-04-13 18:46:25', '2026-04-13 18:46:25', 2),
(6, 32, 3, 20005025, 0, 5001, 20000024, 0, 32, '2026-04-13 19:05:15', '2026-04-13 19:05:15', 2),
(7, 36, 3, 0, 300, 0, 300, 36, 0, '2026-04-14 12:12:35', '2026-04-14 12:12:35', 1),
(8, 30, 4, 5500, 2900, 0, 8400, 30, 0, '2026-04-14 12:58:32', '2026-04-14 12:58:32', 1),
(9, 32, 14, 20000024, 1700, 0, 20001724, 32, 0, '2026-04-15 23:19:16', '2026-04-15 23:19:16', 2),
(10, 30, 5, 8400, 4500, 0, 12900, 30, 0, '2026-04-15 23:41:41', '2026-04-15 23:41:41', 1),
(11, 32, 17, 20001724, 5000, 0, 20006724, 32, 0, '2026-04-18 16:05:40', '2026-04-18 16:05:40', 2),
(12, 32, 17, 20006724, 7000, 0, 20013724, 32, 0, '2026-04-18 16:24:28', '2026-04-18 16:24:28', 2),
(13, 32, 1, 20013724, 0, 10000, 20003724, 1, 32, '2026-04-18 16:32:02', '2026-04-18 16:32:02', 2),
(14, 1, 1, 0, 10000, 0, 10000, 1, 32, '2026-04-18 16:32:02', '2026-04-18 16:32:02', 3),
(15, 30, 6, 12900, 2500, 0, 15400, 30, 0, '2026-04-18 17:47:12', '2026-04-18 17:47:12', 1),
(16, 30, 1, 15400, 0, 2500, 12900, 32, 30, '2026-04-18 17:50:02', '2026-04-18 17:50:02', 1),
(17, 32, 1, 20003724, 2500, 0, 20006224, 32, 30, '2026-04-18 17:50:02', '2026-04-18 17:50:02', 2),
(18, 30, 2, 12900, 0, 12900, 0, 32, 30, '2026-04-18 17:51:19', '2026-04-18 17:51:19', 1),
(19, 32, 2, 20006224, 12900, 0, 20019124, 32, 30, '2026-04-18 17:51:19', '2026-04-18 17:51:19', 2),
(20, 30, 7, 0, 50000, 0, 50000, 30, 0, '2026-04-18 17:52:53', '2026-04-18 17:52:53', 1),
(21, 30, 3, 50000, 0, 25000, 25000, 32, 30, '2026-04-18 17:54:42', '2026-04-18 17:54:42', 1),
(22, 32, 3, 20019124, 25000, 0, 20044124, 32, 30, '2026-04-18 17:54:42', '2026-04-18 17:54:42', 2),
(23, 30, 4, 25000, 0, 5000, 20000, 32, 30, '2026-04-18 17:54:58', '2026-04-18 17:54:58', 1),
(24, 32, 4, 20044124, 5000, 0, 20049124, 32, 30, '2026-04-18 17:54:58', '2026-04-18 17:54:58', 2);

-- --------------------------------------------------------

--
-- Table structure for table `complain_master`
--

CREATE TABLE `complain_master` (
  `complain_id` int(11) NOT NULL,
  `irole_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `comment` text NOT NULL,
  `status` enum('pending','resolved') NOT NULL DEFAULT 'pending',
  `resolve_user_id` int(11) NOT NULL DEFAULT 0,
  `phone` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `resolve_comment` text DEFAULT NULL,
  `resolve_date` date DEFAULT NULL,
  `payment_type` varchar(100) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `iStatus` tinyint(4) NOT NULL DEFAULT 1,
  `isDelete` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `complain_master`
--

INSERT INTO `complain_master` (`complain_id`, `irole_id`, `user_id`, `name`, `email`, `comment`, `status`, `resolve_user_id`, `phone`, `address`, `resolve_comment`, `resolve_date`, `payment_type`, `amount`, `iStatus`, `isDelete`, `created_at`, `updated_at`) VALUES
(1, 1, 30, 'Monty', 'monty@hypermirror.in', 'test', 'pending', 0, '04564654564', 'test address line 1\r\ntest address line2', NULL, NULL, NULL, NULL, 1, 0, '2026-04-13 18:01:23', '2026-04-13 18:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `crm_roles`
--

CREATE TABLE `crm_roles` (
  `iRoleId` bigint(20) UNSIGNED NOT NULL,
  `strRole` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `customers` (
  `iCustomerId` bigint(20) UNSIGNED NOT NULL,
  `strCustomer` varchar(100) NOT NULL,
  `strMobile` varchar(10) NOT NULL,
  `strAddress` text DEFAULT NULL,
  `customer_type` varchar(20) NOT NULL DEFAULT 'Retail',
  `company_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`iCustomerId`, `strCustomer`, `strMobile`, `strAddress`, `customer_type`, `company_name`, `created_at`, `updated_at`) VALUES
(1, 'prerna', '9898012345', '1 anurag flat,bhairawnath, maninagar', 'Retail', 'tetst', '2026-04-07 22:37:34', '2026-04-11 16:26:22'),
(2, 'Apollo Infotech', '9824773136', '1 anurag flat,bhairawnath, maninagar', 'Retail', 'Apollo Infotech NEW', '2026-04-11 16:23:19', '2026-04-12 15:55:24'),
(3, 'Mehul Brahmkshatriya', '9725317761', 'B/49, Prerna Society, Part -2, B/H Namrta Tenemens, Near Parasnager\r\nIsanpur, Ahmedabad', 'B2B', 'The sunshine Group', '2026-04-11 18:03:53', '2026-04-11 18:03:53'),
(4, 'Shital Brahmkshatriya', '9824052159', 'SMB', 'Retail', NULL, '2026-04-11 18:15:13', '2026-04-11 18:15:13'),
(5, 'Manoj Brahmkshatriya', '9904004252', 'ABC Tower', 'Retail', NULL, '2026-04-12 15:37:41', '2026-04-12 15:37:41'),
(6, 'Shailesh Bhavsar', '9375551910', 'ABC Test', 'Retail', NULL, '2026-04-12 16:01:26', '2026-04-12 16:05:21'),
(7, 'Ram Rahim', '9852525252', 'ABC', 'Retail', NULL, '2026-04-12 16:43:30', '2026-04-12 16:43:30'),
(8, 'Prerna Rathod', '9723391747', 'dayanivas street', 'Retail', 'apollo', '2026-04-13 15:08:19', '2026-04-13 19:06:11'),
(9, 'Ankit Gandhi', '8866277000', 'B/49, Narol society\r\nIsanpur', 'Retail', 'Glass Grip', '2026-04-15 22:07:46', '2026-04-15 22:07:46'),
(10, 'Bansari Patel', '9987654321', 'Sola', 'Retail', NULL, '2026-04-17 17:34:52', '2026-04-17 17:34:52'),
(11, 'ravipatel', '7984305927', 'Sola', 'Retail', 'Apollo', '2026-04-17 17:42:57', '2026-04-17 17:42:57'),
(12, 'shivkumar mohta', '9228257933', NULL, 'Retail', NULL, '2026-04-18 14:24:39', '2026-04-18 16:38:16'),
(13, 'dhruvil desai', '9979535421', NULL, 'Retail', NULL, '2026-04-18 17:16:16', '2026-04-18 17:16:16'),
(14, 'JALPA BEN', '9979353421', 'D-703 BINORI AARNA AMBLI', 'Retail', NULL, '2026-04-20 12:49:30', '2026-04-20 12:49:30'),
(15, 'VINOD JAGANI', '9426009948', 'C-7 PUNNESHWER TENAMENT  GULBAI TEKRA', 'Retail', NULL, '2026-04-20 12:54:39', '2026-04-20 12:54:39');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `iInvoiceId` bigint(20) UNSIGNED NOT NULL,
  `strInvoiceNo` varchar(30) NOT NULL,
  `iShowroomId` bigint(20) UNSIGNED DEFAULT NULL,
  `iCreatedBy` bigint(20) UNSIGNED NOT NULL,
  `InvoiceDate` date NOT NULL,
  `customer_name` varchar(120) DEFAULT NULL,
  `customer_mobile` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `strNotes` text DEFAULT NULL,
  `status` enum('draft','confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  `payment_mode` enum('cash','bank') NOT NULL DEFAULT 'cash',
  `payment_received` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`iInvoiceId`, `strInvoiceNo`, `iShowroomId`, `iCreatedBy`, `InvoiceDate`, `customer_name`, `customer_mobile`, `customer_address`, `strNotes`, `status`, `payment_mode`, `payment_received`, `created_at`, `updated_at`) VALUES
(1, 'INV-202604-0001', 3, 30, '2026-04-11', NULL, NULL, NULL, NULL, 'confirmed', 'cash', 1, '2026-04-11 18:25:33', '2026-04-11 18:25:33'),
(2, 'INV-202604-0002', 3, 30, '2026-04-12', NULL, NULL, NULL, 'As discussed with Ankit Gandhi its pending', 'confirmed', 'cash', 1, '2026-04-12 15:14:45', '2026-04-12 15:30:44'),
(3, 'INV-202604-0003', 2, 36, '2026-04-14', 'Prerna Parekh', '9723391747', '3/176, Samathuva Nagar Main Road, 14, Kazhipathur, Padur Post', 'test', 'confirmed', 'cash', 1, '2026-04-14 12:12:35', '2026-04-14 12:12:35'),
(4, 'INV-202604-0004', 3, 30, '2026-04-14', 'prerna', '09824773136', '1, anurag flat,', 'ok', 'confirmed', 'cash', 1, '2026-04-14 12:58:32', '2026-04-14 12:58:32'),
(5, 'INV-202604-0005', 3, 30, '2026-04-15', 'Krunal Shah', '9824773136', 'Maninagar', 'ok', 'confirmed', 'cash', 1, '2026-04-15 23:41:41', '2026-04-15 23:41:41'),
(6, 'INV-202604-0006', 3, 30, '2026-04-18', 'nikhil', '7575002108', 'ishanpur', NULL, 'confirmed', 'cash', 1, '2026-04-18 17:47:12', '2026-04-18 17:47:12'),
(7, 'INV-202604-0007', 3, 30, '2026-04-18', 'nikhil', '7575002108', NULL, NULL, 'confirmed', 'cash', 1, '2026-04-18 17:52:53', '2026-04-18 17:52:53');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `iItemId` bigint(20) UNSIGNED NOT NULL,
  `iInvoiceId` bigint(20) UNSIGNED NOT NULL,
  `iCategoryId` bigint(20) UNSIGNED NOT NULL,
  `iProductId` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(16,2) NOT NULL DEFAULT 0.00,
  `iAmount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `item_remark` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`iItemId`, `iInvoiceId`, `iCategoryId`, `iProductId`, `quantity`, `unit_price`, `iAmount`, `item_remark`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 4, 1, 5000.00, 5000.00, NULL, '2026-04-11 18:25:33', '2026-04-11 18:25:33'),
(2, 2, 6, 5, 1, 500.00, 500.00, NULL, '2026-04-12 15:14:45', '2026-04-12 15:14:45'),
(3, 3, 1, 10, 1, 100.00, 100.00, 'test2', '2026-04-14 12:12:35', '2026-04-14 12:12:35'),
(4, 3, 3, 12, 1, 200.00, 200.00, 'test 3', '2026-04-14 12:12:35', '2026-04-14 12:12:35'),
(5, 4, 4, 3, 1, 2500.00, 2500.00, 'ok', '2026-04-14 12:58:32', '2026-04-14 12:58:32'),
(6, 4, 2, 9, 2, 200.00, 400.00, 'ok', '2026-04-14 12:58:32', '2026-04-14 12:58:32'),
(7, 5, 4, 3, 3, 1500.00, 4500.00, 'all ok', '2026-04-15 23:41:41', '2026-04-15 23:41:41'),
(8, 6, 4, 3, 1, 2500.00, 2500.00, NULL, '2026-04-18 17:47:12', '2026-04-18 17:47:12'),
(9, 7, 1, 10, 10, 5000.00, 50000.00, NULL, '2026-04-18 17:52:53', '2026-04-18 17:52:53');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_pdf_settings`
--

CREATE TABLE `invoice_pdf_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `terms_and_conditions` longtext DEFAULT NULL,
  `bank_details` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_pdf_settings`
--

INSERT INTO `invoice_pdf_settings` (`id`, `terms_and_conditions`, `bank_details`, `created_at`, `updated_at`) VALUES
(1, '<p>1. Fixed Rate - No Bargaining. 2. Quotation is valid for only 07 days. 3. Warranty Period is ONE Year from the date of Installation. 4. Excluding due to physical damage, power supply surge or burn, or natural calamity of water. 5. Payment 100% advance along with PO. 6. Work will start within a week on receipt of PO and advance payment. 7. The entire dispute will be subject to the courts of Ahmedabad. 8. Goods once sold will not be taken back or exchanged.</p>', '<p>Name: AXIS BANK, ISANPUR&nbsp;</p><p>Account No.: 920020057214879&nbsp;</p><p>IFSC code: UTIB0001338&nbsp;</p><p>Account Holder\'s Name: HYPER MIRROR</p>', '2026-04-14 21:49:44', '2026-04-16 18:24:22');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `iLeadId` bigint(20) UNSIGNED NOT NULL,
  `iCustomerId` bigint(20) UNSIGNED NOT NULL,
  `iCurrentYearLeadId` varchar(4) NOT NULL,
  `strLeadNo` varchar(255) NOT NULL,
  `IsMeasureMentRequired` tinyint(4) NOT NULL DEFAULT 0,
  `MeasurementVisitDate` date DEFAULT NULL,
  `SiteAddress` text DEFAULT NULL,
  `CreatedDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `iCurrentLeadStatus` varchar(50) NOT NULL,
  `NetFollowupdate` date DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `isFittingLeadOnly` tinyint(4) NOT NULL DEFAULT 0,
  `isFittingRequired` tinyint(4) NOT NULL DEFAULT 0,
  `isFittingChargeIncluded` tinyint(4) NOT NULL DEFAULT 0,
  `iFittingCharges` int(11) NOT NULL DEFAULT 0,
  `isDiscountApplicable` tinyint(4) NOT NULL DEFAULT 0,
  `decDiscountAmount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `isGstApplicable` tinyint(4) NOT NULL DEFAULT 0,
  `decGstAmount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `iLeadAmount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `iQuotationId` bigint(20) UNSIGNED DEFAULT NULL,
  `iCreatedBy` bigint(20) UNSIGNED DEFAULT NULL,
  `iShowroomId` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_charges` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`iLeadId`, `iCustomerId`, `iCurrentYearLeadId`, `strLeadNo`, `IsMeasureMentRequired`, `MeasurementVisitDate`, `SiteAddress`, `CreatedDate`, `iCurrentLeadStatus`, `NetFollowupdate`, `expected_delivery_date`, `isFittingLeadOnly`, `isFittingRequired`, `isFittingChargeIncluded`, `iFittingCharges`, `isDiscountApplicable`, `decDiscountAmount`, `isGstApplicable`, `decGstAmount`, `iLeadAmount`, `iQuotationId`, `iCreatedBy`, `iShowroomId`, `created_at`, `updated_at`, `delivery_charges`) VALUES
(1, 3, '2627', '2627-0001', 0, NULL, 'B/49, Prerna Society, Part -2, B/H Namrta Tenemens, Near Parasnager\r\nIsanpur, Ahmedabad', '2026-04-11 18:03:53', 'Quotation Approved', '2026-04-11', '2026-04-11', 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0.00, NULL, 30, NULL, '2026-04-11 18:03:53', '2026-04-11 18:54:36', 0),
(2, 4, '2627', '2627-0002', 1, '2026-04-11', 'SMB', '2026-04-11 18:15:13', 'Dispatched', '2026-04-11', '2026-04-11', 0, 0, 0, 0, 0, 0.00, 1, 3.60, 23.60, 1, 30, NULL, '2026-04-11 18:15:13', '2026-04-11 18:49:30', 0),
(3, 5, '2627', '2627-0003', 1, '2026-04-12', 'Abc Tower', '2026-04-12 15:37:41', 'Quotation Sent', '2026-04-12', NULL, 0, 0, 0, 0, 1, 2000.00, 0, 0.00, 10000.00, 3, 30, NULL, '2026-04-12 15:37:41', '2026-04-12 15:53:41', 0),
(4, 2, '2627', '2627-0004', 0, NULL, '1 anurag flat,bhairawnath, maninagar', '2026-04-12 15:55:24', 'Lead Rejected', NULL, NULL, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 200000.00, 4, 30, NULL, '2026-04-12 15:55:24', '2026-04-12 16:00:05', 0),
(5, 6, '2627', '2627-0005', 1, '2026-04-12', 'ABC Test', '2026-04-12 16:01:26', 'In Measurement', '2026-04-12', NULL, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0.00, NULL, 30, NULL, '2026-04-12 16:01:26', '2026-04-12 16:01:26', 0),
(6, 6, '2627', '2627-0006', 0, NULL, NULL, '2026-04-12 16:05:21', 'In Design', '2026-04-12', NULL, 0, 0, 0, 0, 0, 0.00, 0, 0.00, 0.00, NULL, 30, NULL, '2026-04-12 16:05:21', '2026-04-12 16:05:21', 0),
(7, 6, '2627', '2627-0007', 0, NULL, NULL, '2026-04-12 16:09:18', 'Quotation Approved', '2026-04-12', '2026-04-13', 0, 0, 0, 0, 0, 0.00, 0, 0.00, 40000.00, 5, 30, NULL, '2026-04-12 16:09:18', '2026-04-12 16:17:32', 0),
(8, 7, '2627', '2627-0008', 1, '2026-04-12', NULL, '2026-04-12 16:43:30', 'Dispatched', '2026-04-12', '2026-04-12', 0, 0, 0, 0, 0, 0.00, 0, 0.00, 500.00, 6, 30, NULL, '2026-04-12 16:43:30', '2026-04-12 16:50:12', 0),
(12, 8, '2627', '2627-0009', 0, NULL, 'test address', '2026-04-13 15:36:11', 'Fitting Pending', '2026-04-17', '2026-04-21', 0, 1, 0, 1000, 0, 0.00, 1, 6930.00, 45430.00, 7, 30, NULL, '2026-04-13 15:36:11', '2026-04-13 17:04:42', 0),
(13, 8, '2627', '2627-0010', 0, NULL, 'mission road', '2026-04-13 19:06:11', 'Dispatched', '2026-04-17', '2026-04-22', 0, 0, 0, 1000, 0, 0.00, 0, 0.00, 2537.50, 13, 30, NULL, '2026-04-13 19:06:11', '2026-04-17 17:13:38', 1500),
(14, 9, '2627', '2627-0011', 1, '2026-04-15', 'B/49, Narol society\r\nIsanpur', '2026-04-15 22:07:46', 'Received @ Narol', NULL, '2026-04-15', 0, 1, 1, 0, 1, 100.00, 0, 0.00, 3500.00, 9, 30, NULL, '2026-04-15 22:07:46', '2026-04-16 18:42:31', 0),
(16, 11, '2627', '2627-0012', 1, '2026-04-17', 'Sola', '2026-04-17 17:42:57', 'In Measurement', '2026-04-17', NULL, 0, 1, 0, 100, 0, 0.00, 1, 399.38, 2618.13, 28, 30, NULL, '2026-04-17 17:42:57', '2026-04-20 14:21:02', 100),
(17, 12, '2627', '2627-0013', 1, '2026-04-20', '7 alfa cycle store ,narol ,ahmedabad', '2026-04-18 14:24:39', 'Deal Done', NULL, '2026-04-22', 0, 1, 1, 0, 1, 12.50, 0, 0.00, 12000.00, 20, 30, NULL, '2026-04-18 14:24:39', '2026-04-18 16:26:18', 200),
(18, 12, '2627', '2627-0014', 1, '2026-04-19', NULL, '2026-04-18 16:38:16', 'In Measurement', '2026-04-19', NULL, 0, 1, 0, 0, 0, 0.00, 0, 0.00, 0.00, NULL, 30, NULL, '2026-04-18 16:38:16', '2026-04-18 16:38:16', 0),
(19, 13, '2627', '2627-0015', 1, '2026-04-19', 'ambali', '2026-04-18 17:16:16', 'Measurement Done', '2026-04-20', NULL, 0, 1, 1, 500, 0, 0.00, 0, 0.00, 40500.00, 27, 30, NULL, '2026-04-18 17:16:16', '2026-04-20 11:15:41', 1000),
(20, 3, '2627', '2627-0016', 0, NULL, 'B/49, Prerna Society, Part -2, B/H Namrta Tenemens, Near Parasnager\r\nIsanpur, Ahmedabad', '2026-04-19 12:36:17', 'Advance Received', '2026-04-19', '2026-04-19', 0, 1, 0, 0, 0, 0.00, 0, 0.00, 3375.00, 29, 30, NULL, '2026-04-19 12:36:17', '2026-04-20 15:28:55', 0),
(21, 14, '2627', '2627-0017', 0, NULL, 'D-703 BINORI AARNA AMBLI', '2026-04-20 12:49:30', 'In Design', '2026-04-30', NULL, 0, 1, 1, 0, 0, 0.00, 0, 0.00, 0.00, NULL, 30, NULL, '2026-04-20 12:49:30', '2026-04-20 12:49:30', 0),
(22, 15, '2627', '2627-0018', 0, NULL, 'C-7 PUNNESHWER TENAMENT  GULBAI TEKRA', '2026-04-20 12:54:39', 'In Design', '2026-04-21', NULL, 0, 1, 0, 0, 0, 0.00, 0, 0.00, 0.00, NULL, 30, NULL, '2026-04-20 12:54:39', '2026-04-20 12:54:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lead_designs`
--

CREATE TABLE `lead_designs` (
  `iLeadDesignId` bigint(20) UNSIGNED NOT NULL,
  `iLeadId` bigint(20) UNSIGNED NOT NULL,
  `strFilename` varchar(255) NOT NULL,
  `strTitle` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_designs`
--

INSERT INTO `lead_designs` (`iLeadDesignId`, `iLeadId`, `strFilename`, `strTitle`, `created_at`, `updated_at`) VALUES
(1, 2, '1775911769_Hina_Dye_Chem_Audit_Report.pdf', 'Ca', '2026-04-11 18:19:29', '2026-04-11 18:19:29'),
(2, 1, '1775911850_Sale_30112035_24-03-2026.pdf', 'Pharma Sector Social Profiling', '2026-04-11 18:20:50', '2026-04-11 18:20:50'),
(3, 3, '1775988943_aloe.jpg', '1111', '2026-04-12 15:45:43', '2026-04-12 15:45:43'),
(4, 4, '1775989547_aloe.jpg', '1111', '2026-04-12 15:55:47', '2026-04-12 15:55:47'),
(5, 5, '1775989924_error.png', NULL, '2026-04-12 16:02:04', '2026-04-12 16:02:04'),
(6, 8, '1775992499_aloe.jpg', NULL, '2026-04-12 16:44:59', '2026-04-12 16:44:59'),
(15, 14, '20260416190848_1760002506_AIaPSA.jpg', 'test', '2026-04-16 19:08:48', '2026-04-16 19:08:48'),
(17, 14, '20260416191415_1600w-kgrclsa6l6m_fok0uo.webp', 'test', '2026-04-16 19:14:15', '2026-04-16 19:14:15'),
(18, 17, '20260418151000_1039666081_XBFgmC.pdf', 'shivkuma mohta', '2026-04-18 15:10:00', '2026-04-18 15:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `lead_histories`
--

CREATE TABLE `lead_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iLeadId` bigint(20) UNSIGNED NOT NULL,
  `strComments` text DEFAULT NULL,
  `NetFolloupwdate` date DEFAULT NULL,
  `iStatus` varchar(50) NOT NULL,
  `iEnterBy` bigint(20) UNSIGNED NOT NULL,
  `EntryDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_histories`
--

INSERT INTO `lead_histories` (`id`, `iLeadId`, `strComments`, `NetFolloupwdate`, `iStatus`, `iEnterBy`, `EntryDate`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lead created.', '2026-04-11', 'In Design', 30, '2026-04-11 18:03:53', '2026-04-11 18:03:53', '2026-04-11 18:03:53'),
(2, 2, 'Lead created.', '2026-04-11', 'In Measurement', 30, '2026-04-11 18:15:13', '2026-04-11 18:15:13', '2026-04-11 18:15:13'),
(3, 2, 'Capsual Mirror', NULL, 'Measurement Done', 31, '2026-04-11 18:16:26', '2026-04-11 18:16:26', '2026-04-11 18:16:26'),
(4, 2, 'Will have to followup with designer', '2026-04-11', 'In Design', 30, '2026-04-11 18:20:13', '2026-04-11 18:20:13', '2026-04-11 18:20:13'),
(5, 2, 'Quotation generated. Version #1', '2026-04-11', 'Quotation Sent', 30, '2026-04-11 18:30:19', '2026-04-11 18:30:19', '2026-04-11 18:30:19'),
(6, 2, 'Quation Approved\nExpected Delivery Date: 2026-04-11', '2026-04-11', 'Quotation Approved', 30, '2026-04-11 18:36:12', '2026-04-11 18:36:12', '2026-04-11 18:36:12'),
(7, 2, 'Ad Received', '2026-04-11', 'Advance Received', 30, '2026-04-11 18:39:39', '2026-04-11 18:39:39', '2026-04-11 18:39:39'),
(8, 2, 'Accepted', '2026-04-11', 'Production Accepted', 33, '2026-04-11 18:46:21', '2026-04-11 18:46:21', '2026-04-11 18:46:21'),
(9, 2, 'Ready to dispatch', NULL, 'Ready to Dispatched', 33, '2026-04-11 18:47:09', '2026-04-11 18:47:09', '2026-04-11 18:47:09'),
(10, 2, 'isampur', '2026-04-11', 'Dispatched', 34, '2026-04-11 18:49:30', '2026-04-11 18:49:30', '2026-04-11 18:49:30'),
(11, 1, '1234', '2026-04-11', 'Quotation Sent', 30, '2026-04-11 18:54:06', '2026-04-11 18:54:06', '2026-04-11 18:54:06'),
(12, 1, '1\nExpected Delivery Date: 2026-04-11', '2026-04-11', 'Quotation Approved', 30, '2026-04-11 18:54:36', '2026-04-11 18:54:36', '2026-04-11 18:54:36'),
(13, 3, 'Lead created.', '2026-04-12', 'In Measurement', 30, '2026-04-12 15:37:41', '2026-04-12 15:37:41', '2026-04-12 15:37:41'),
(14, 3, '120 X 120 Capsual', NULL, 'Measurement Done', 30, '2026-04-12 15:42:23', '2026-04-12 15:42:23', '2026-04-12 15:42:23'),
(15, 3, 'Quotation generated. Version #1', '2026-04-12', 'Quotation Sent', 30, '2026-04-12 15:52:10', '2026-04-12 15:52:10', '2026-04-12 15:52:10'),
(16, 3, 'Quotation generated. Version #2', '2026-04-12', 'Quotation Sent', 30, '2026-04-12 15:53:41', '2026-04-12 15:53:41', '2026-04-12 15:53:41'),
(17, 4, 'Lead created.', '2026-04-12', 'In Design', 30, '2026-04-12 15:55:24', '2026-04-12 15:55:24', '2026-04-12 15:55:24'),
(18, 4, 'Quotation generated. Version #1', '2026-04-12', 'Quotation Sent', 30, '2026-04-12 15:57:14', '2026-04-12 15:57:14', '2026-04-12 15:57:14'),
(19, 4, 'Rejection Reason: Lead Rejected due to high Amount\nLead Rejected due to high Amount', NULL, 'Lead Rejected', 30, '2026-04-12 16:00:05', '2026-04-12 16:00:05', '2026-04-12 16:00:05'),
(20, 5, 'Lead created.', '2026-04-12', 'In Measurement', 30, '2026-04-12 16:01:26', '2026-04-12 16:01:26', '2026-04-12 16:01:26'),
(21, 6, 'Lead created.', '2026-04-12', 'In Design', 30, '2026-04-12 16:05:21', '2026-04-12 16:05:21', '2026-04-12 16:05:21'),
(22, 7, 'Lead created.', '2026-04-12', 'In Design', 30, '2026-04-12 16:09:18', '2026-04-12 16:09:18', '2026-04-12 16:09:18'),
(23, 7, 'Quotation generated. Version #1', '2026-04-12', 'Quotation Sent', 30, '2026-04-12 16:17:11', '2026-04-12 16:17:11', '2026-04-12 16:17:11'),
(24, 7, 'Hi\nExpected Delivery Date: 2026-04-13', '2026-04-12', 'Quotation Approved', 30, '2026-04-12 16:17:32', '2026-04-12 16:17:32', '2026-04-12 16:17:32'),
(25, 8, 'Lead created.', '2026-04-12', 'In Measurement', 30, '2026-04-12 16:43:30', '2026-04-12 16:43:30', '2026-04-12 16:43:30'),
(26, 8, 'So', NULL, 'Measurement Done', 31, '2026-04-12 16:44:16', '2026-04-12 16:44:16', '2026-04-12 16:44:16'),
(27, 8, 'Quotation generated. Version #1', '2026-04-12', 'Quotation Sent', 30, '2026-04-12 16:45:48', '2026-04-12 16:45:48', '2026-04-12 16:45:48'),
(28, 8, '1\nExpected Delivery Date: 2026-04-12', '2026-04-12', 'Quotation Approved', 30, '2026-04-12 16:46:23', '2026-04-12 16:46:23', '2026-04-12 16:46:23'),
(29, 8, '250 Received', '2026-04-12', 'Advance Received', 30, '2026-04-12 16:47:09', '2026-04-12 16:47:09', '2026-04-12 16:47:09'),
(30, 8, '1233', '2026-04-12', 'Production Accepted', 33, '2026-04-12 16:49:09', '2026-04-12 16:49:09', '2026-04-12 16:49:09'),
(31, 8, 'aa', NULL, 'Ready to Dispatched', 33, '2026-04-12 16:49:18', '2026-04-12 16:49:18', '2026-04-12 16:49:18'),
(32, 8, '12', '2026-04-12', 'Dispatched', 34, '2026-04-12 16:50:12', '2026-04-12 16:50:12', '2026-04-12 16:50:12'),
(36, 12, 'Lead created.', '2026-04-14', 'In Design', 30, '2026-04-13 15:36:11', '2026-04-13 15:36:11', '2026-04-13 15:36:11'),
(37, 12, 'Quotation generated. Version #1', '2026-04-14', 'Quotation Sent', 30, '2026-04-13 15:38:56', '2026-04-13 15:38:56', '2026-04-13 15:38:56'),
(38, 12, 'fitting extra\nExpected Delivery Date: 2026-04-21', '2026-04-16', 'Quotation Approved', 30, '2026-04-13 15:39:36', '2026-04-13 15:39:36', '2026-04-13 15:39:36'),
(39, 12, 'done', NULL, 'Advance Received', 30, '2026-04-13 17:00:07', '2026-04-13 17:00:07', '2026-04-13 17:00:07'),
(40, 12, 'production done', '2026-04-16', 'Production Accepted', 33, '2026-04-13 17:01:02', '2026-04-13 17:01:02', '2026-04-13 17:01:02'),
(41, 12, 'ready', NULL, 'Ready to Dispatched', 33, '2026-04-13 17:01:58', '2026-04-13 17:01:58', '2026-04-13 17:01:58'),
(42, 12, 'dispatchd', '2026-04-18', 'Dispatched', 30, '2026-04-13 17:02:30', '2026-04-13 17:02:30', '2026-04-13 17:02:30'),
(43, 12, 'done', NULL, 'Dispatched Done', 30, '2026-04-13 17:03:48', '2026-04-13 17:03:48', '2026-04-13 17:03:48'),
(44, 12, 'pending', '2026-04-17', 'Fitting Pending', 35, '2026-04-13 17:04:42', '2026-04-13 17:04:42', '2026-04-13 17:04:42'),
(45, 13, 'Lead created.', '2026-04-15', 'In Design', 30, '2026-04-13 19:06:11', '2026-04-13 19:06:11', '2026-04-13 19:06:11'),
(46, 13, 'Quotation generated. Version #1', '2026-04-15', 'Quotation Sent', 30, '2026-04-13 19:07:28', '2026-04-13 19:07:28', '2026-04-13 19:07:28'),
(47, 13, 'ok\nExpected Delivery Date: 2026-04-22', '2026-04-16', 'Quotation Approved', 30, '2026-04-13 19:08:02', '2026-04-13 19:08:02', '2026-04-13 19:08:02'),
(48, 13, 'advance 1000 received', NULL, 'Advance Received', 30, '2026-04-13 19:08:31', '2026-04-13 19:08:31', '2026-04-13 19:08:31'),
(49, 14, 'Require to do Visit', '2026-04-15', 'In Measurement', 30, '2026-04-15 22:07:46', '2026-04-15 22:07:46', '2026-04-15 22:07:46'),
(50, 14, 'Measurement Completed', NULL, 'Measurement Done', 31, '2026-04-15 22:15:14', '2026-04-15 22:15:14', '2026-04-15 22:15:14'),
(51, 14, 'Design require, Design document given to Fazu', '2026-04-15', 'In Design', 30, '2026-04-15 22:19:27', '2026-04-15 22:19:27', '2026-04-15 22:19:27'),
(52, 14, 'OK', '2026-04-15', 'In Design', 30, '2026-04-15 22:45:06', '2026-04-15 22:45:06', '2026-04-15 22:45:06'),
(53, 14, 'Need to call AG for Payment', '2026-04-15', 'Quotation Sent', 30, '2026-04-15 23:05:10', '2026-04-15 23:05:10', '2026-04-15 23:05:10'),
(54, 14, 'Payment\nExpected Delivery Date: 2026-04-15', '2026-04-15', 'Quotation Approved', 30, '2026-04-15 23:08:18', '2026-04-15 23:08:18', '2026-04-15 23:08:18'),
(55, 14, '1700 RS Received', '2026-04-16', 'Advance Received', 30, '2026-04-15 23:13:03', '2026-04-15 23:13:03', '2026-04-15 23:13:03'),
(56, 14, 'Production Accepted', NULL, 'Production Accepted', 33, '2026-04-15 23:22:24', '2026-04-15 23:22:24', '2026-04-15 23:22:24'),
(57, 14, 'Ready to dispatch', NULL, 'Ready to Dispatched', 33, '2026-04-15 23:22:51', '2026-04-15 23:22:51', '2026-04-15 23:22:51'),
(58, 14, 'Dipatch to Narol', '2026-04-16', 'Dispatched', 34, '2026-04-15 23:28:07', '2026-04-15 23:28:07', '2026-04-15 23:28:07'),
(59, 13, 'ok', '2026-04-21', 'Production Accepted', 30, '2026-04-16 14:36:44', '2026-04-16 14:36:44', '2026-04-16 14:36:44'),
(60, 13, 'ready', NULL, 'Ready to Dispatched', 30, '2026-04-16 14:37:00', '2026-04-16 14:37:00', '2026-04-16 14:37:00'),
(61, 13, 'dispatched', NULL, 'Dispatched', 34, '2026-04-16 14:39:24', '2026-04-16 14:39:24', '2026-04-16 14:39:24'),
(62, 13, 'ok', '2026-04-17', 'Dispatched', 30, '2026-04-16 17:05:13', '2026-04-16 17:05:13', '2026-04-16 17:05:13'),
(63, 13, 'Lead details updated.', '2026-04-17', 'Dispatched', 30, '2026-04-16 17:43:19', '2026-04-16 17:43:19', '2026-04-16 17:43:19'),
(64, 14, 'ok', NULL, 'Received @ Narol', 34, '2026-04-16 18:42:31', '2026-04-16 18:42:31', '2026-04-16 18:42:31'),
(65, 13, 'ok', '2026-04-17', 'Dispatched', 30, '2026-04-17 15:46:04', '2026-04-17 15:46:04', '2026-04-17 15:46:04'),
(66, 13, 'Quotation generated. Version #3', '2026-04-17', 'Dispatched', 30, '2026-04-17 15:51:26', '2026-04-17 15:51:26', '2026-04-17 15:51:26'),
(67, 13, 'ok', '2026-04-17', 'Dispatched', 30, '2026-04-17 17:13:38', '2026-04-17 17:13:38', '2026-04-17 17:13:38'),
(69, 16, 'test', '2026-04-17', 'In Measurement', 30, '2026-04-17 17:42:57', '2026-04-17 17:42:57', '2026-04-17 17:42:57'),
(70, 16, 'ok', '2026-04-17', 'In Measurement', 30, '2026-04-17 18:07:43', '2026-04-17 18:07:43', '2026-04-17 18:07:43'),
(71, 17, 'visit and inform monty if require', '2026-04-20', 'In Measurement', 30, '2026-04-18 14:24:39', '2026-04-18 14:24:39', '2026-04-18 14:24:39'),
(72, 17, '18*24 mirror measurement done', NULL, 'Measurement Done', 37, '2026-04-18 14:47:37', '2026-04-18 14:47:37', '2026-04-18 14:47:37'),
(73, 17, 'pls check and make pdf', '2026-04-20', 'In Design', 30, '2026-04-18 15:06:19', '2026-04-18 15:06:19', '2026-04-18 15:06:19'),
(74, 17, 'Quotation generated. Version #1', '2026-04-20', 'In Design', 30, '2026-04-18 15:19:23', '2026-04-18 15:19:23', '2026-04-18 15:19:23'),
(75, 17, 'Quotation generated. Version #2', '2026-04-20', 'In Design', 30, '2026-04-18 15:19:52', '2026-04-18 15:19:52', '2026-04-18 15:19:52'),
(76, 17, 'Quotation generated. Version #3', '2026-04-20', 'In Design', 30, '2026-04-18 15:19:52', '2026-04-18 15:19:52', '2026-04-18 15:19:52'),
(77, 17, 'Quotation generated. Version #4', '2026-04-20', 'In Design', 30, '2026-04-18 15:20:17', '2026-04-18 15:20:17', '2026-04-18 15:20:17'),
(78, 17, 'Quotation generated. Version #5', '2026-04-20', 'In Design', 30, '2026-04-18 15:20:18', '2026-04-18 15:20:18', '2026-04-18 15:20:18'),
(79, 17, 'ok', '2026-04-20', 'In Design', 30, '2026-04-18 15:40:06', '2026-04-18 15:40:06', '2026-04-18 15:40:06'),
(80, 17, 'need to call and confirm quotation', '2026-04-20', 'Quotation Sent', 30, '2026-04-18 15:58:21', '2026-04-18 15:58:21', '2026-04-18 15:58:21'),
(81, 17, 'ok\nExpected Delivery Date: 2026-04-22', '2026-04-18', 'Quotation Approved', 30, '2026-04-18 15:59:45', '2026-04-18 15:59:45', '2026-04-18 15:59:45'),
(82, 17, 'amt recd', '2026-04-22', 'Advance Received', 30, '2026-04-18 16:02:09', '2026-04-18 16:02:09', '2026-04-18 16:02:09'),
(83, 17, 'ok understand', '2026-04-18', 'Production Accepted', 33, '2026-04-18 16:16:46', '2026-04-18 16:16:46', '2026-04-18 16:16:46'),
(84, 17, 'ok', NULL, 'Ready to Dispatched', 33, '2026-04-18 16:17:48', '2026-04-18 16:17:48', '2026-04-18 16:17:48'),
(85, 17, 'ok', NULL, 'Dispatched', 34, '2026-04-18 16:20:00', '2026-04-18 16:20:00', '2026-04-18 16:20:00'),
(86, 17, 'fitting done', NULL, 'Fitting Done', 35, '2026-04-18 16:21:41', '2026-04-18 16:21:41', '2026-04-18 16:21:41'),
(87, 17, 'payment rec', NULL, 'Deal Done', 32, '2026-04-18 16:26:18', '2026-04-18 16:26:18', '2026-04-18 16:26:18'),
(88, 18, 'ok', '2026-04-19', 'In Measurement', 30, '2026-04-18 16:38:16', '2026-04-18 16:38:16', '2026-04-18 16:38:16'),
(89, 19, 'visit ambali site and meet dhruvilbhai', '2026-04-19', 'In Measurement', 30, '2026-04-18 17:16:16', '2026-04-18 17:16:16', '2026-04-18 17:16:16'),
(90, 19, 'ok', NULL, 'Measurement Done', 31, '2026-04-18 17:20:49', '2026-04-18 17:20:49', '2026-04-18 17:20:49'),
(91, 19, 'ok', '2026-04-20', 'Measurement Done', 30, '2026-04-18 17:37:52', '2026-04-18 17:37:52', '2026-04-18 17:37:52'),
(92, 20, 'NN', '2026-04-19', 'In Design', 30, '2026-04-19 12:36:17', '2026-04-19 12:36:17', '2026-04-19 12:36:17'),
(93, 20, 'Quotation generated. Version #1', '2026-04-19', 'In Design', 30, '2026-04-19 12:50:05', '2026-04-19 12:50:05', '2026-04-19 12:50:05'),
(94, 20, 'Follow up', '2026-04-19', 'Quotation Sent', 30, '2026-04-19 12:52:28', '2026-04-19 12:52:28', '2026-04-19 12:52:28'),
(95, 20, 'quotation approved\nExpected Delivery Date: 2026-04-19', '2026-04-19', 'Quotation Approved', 30, '2026-04-19 12:53:02', '2026-04-19 12:53:02', '2026-04-19 12:53:02'),
(96, 20, '500', '2026-04-19', 'Advance Received', 30, '2026-04-19 12:54:06', '2026-04-19 12:54:06', '2026-04-19 12:54:06'),
(97, 20, 'Quotation generated. Version #2', '2026-04-19', 'Advance Received', 30, '2026-04-19 13:06:05', '2026-04-19 13:06:05', '2026-04-19 13:06:05'),
(98, 20, 'Quotation generated. Version #3', '2026-04-19', 'Advance Received', 30, '2026-04-19 13:08:10', '2026-04-19 13:08:10', '2026-04-19 13:08:10'),
(99, 19, 'Quotation generated. Version #2', '2026-04-20', 'Measurement Done', 30, '2026-04-20 11:15:41', '2026-04-20 11:15:41', '2026-04-20 11:15:41'),
(100, 21, 'MEASUREMENT DONE SEND QUOTATION', '2026-04-30', 'In Design', 30, '2026-04-20 12:49:30', '2026-04-20 12:49:30', '2026-04-20 12:49:30'),
(101, 22, 'REQUIRED QUOTATION', '2026-04-21', 'In Design', 30, '2026-04-20 12:54:39', '2026-04-20 12:54:39', '2026-04-20 12:54:39'),
(102, 16, 'Quotation generated. Version #2', '2026-04-17', 'In Measurement', 30, '2026-04-20 14:21:02', '2026-04-20 14:21:02', '2026-04-20 14:21:02'),
(103, 20, 'Quotation generated. Version #4', '2026-04-19', 'Advance Received', 30, '2026-04-20 15:28:55', '2026-04-20 15:28:55', '2026-04-20 15:28:55');

-- --------------------------------------------------------

--
-- Table structure for table `lead_payments`
--

CREATE TABLE `lead_payments` (
  `iLeadPaymentId` bigint(20) UNSIGNED NOT NULL,
  `iLeadId` bigint(20) UNSIGNED NOT NULL,
  `iPaidAmount` decimal(16,2) NOT NULL,
  `PaymentDate` date NOT NULL,
  `PaymentMode` varchar(30) NOT NULL,
  `iUserID` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_payments`
--

INSERT INTO `lead_payments` (`iLeadPaymentId`, `iLeadId`, `iPaidAmount`, `PaymentDate`, `PaymentMode`, `iUserID`, `created_at`, `updated_at`) VALUES
(1, 2, 23.60, '2026-04-11', 'cash', 32, '2026-04-11 18:37:38', '2026-04-11 18:37:38'),
(2, 7, 40000.00, '2026-04-12', 'cash', 32, '2026-04-12 16:20:33', '2026-04-12 16:25:04'),
(4, 14, 1700.00, '2026-04-15', 'cash', 32, '2026-04-15 23:19:16', '2026-04-15 23:19:16'),
(5, 17, 5000.00, '2026-04-18', 'cash', 32, '2026-04-18 16:05:40', '2026-04-18 16:05:40'),
(6, 17, 7000.00, '2026-04-18', 'cash', 32, '2026-04-18 16:24:28', '2026-04-18 16:24:28');

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotations`
--

CREATE TABLE `lead_quotations` (
  `iQuotationId` bigint(20) UNSIGNED NOT NULL,
  `iLeadId` bigint(20) UNSIGNED NOT NULL,
  `quotation_batch_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `iProductCategoryId` bigint(20) UNSIGNED NOT NULL,
  `iProductId` bigint(20) UNSIGNED NOT NULL,
  `unit_of_measurement` enum('inch','MM','Feet') DEFAULT NULL,
  `shape_id` bigint(20) UNSIGNED DEFAULT NULL,
  `feature_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `decHeight` decimal(16,2) NOT NULL,
  `decWidth` decimal(16,2) NOT NULL,
  `decTotalSqft` decimal(16,2) NOT NULL,
  `decRatePerSqft` decimal(16,2) NOT NULL,
  `iAmount` decimal(16,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_quotations`
--

INSERT INTO `lead_quotations` (`iQuotationId`, `iLeadId`, `quotation_batch_id`, `iProductCategoryId`, `iProductId`, `unit_of_measurement`, `shape_id`, `feature_id`, `remarks`, `quantity`, `decHeight`, `decWidth`, `decTotalSqft`, `decRatePerSqft`, `iAmount`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 4, 3, 'inch', 12, 2, NULL, 1, 2.00, 2.00, 4.00, 5.00, 20.00, '2026-04-11 18:30:19', '2026-04-11 18:30:19'),
(2, 3, 1, 7, 8, 'inch', 6, 2, NULL, 1, 2.00, 2.00, 4.00, 2000.00, 8000.00, '2026-04-12 15:52:10', '2026-04-12 15:52:10'),
(3, 3, 2, 7, 8, 'inch', 6, 2, NULL, 1, 2.00, 2.00, 4.00, 3000.00, 12000.00, '2026-04-12 15:53:41', '2026-04-12 15:53:41'),
(4, 4, 1, 5, 13, 'inch', 6, 2, NULL, 1, 100.00, 100.00, 10000.00, 20.00, 200000.00, '2026-04-12 15:57:14', '2026-04-12 15:57:14'),
(5, 7, 1, 6, 4, 'inch', 9, 2, 'WPVC', 1, 200.00, 200.00, 40000.00, 1.00, 40000.00, '2026-04-12 16:17:11', '2026-04-12 16:17:11'),
(6, 8, 1, 6, 4, 'inch', 1, 2, NULL, 1, 1.00, 1.00, 1.00, 500.00, 500.00, '2026-04-12 16:45:48', '2026-04-12 16:45:48'),
(7, 12, 1, 6, 5, 'inch', 10, 5, NULL, 1, 15.00, 25.00, 375.00, 100.00, 37500.00, '2026-04-13 15:38:56', '2026-04-13 15:38:56'),
(8, 13, 1, 7, 8, 'inch', 9, 5, NULL, 1, 4.00, 5.00, 20.00, 150.00, 3000.00, '2026-04-13 19:07:28', '2026-04-13 19:07:28'),
(9, 14, 1, 6, 4, 'Feet', 4, 2, NULL, 1, 5.00, 5.00, 25.00, 100.00, 3600.00, '2026-04-15 22:45:06', '2026-04-15 22:45:06'),
(10, 13, 2, 7, 8, 'inch', 9, 5, NULL, 1, 4.00, 5.00, 20.00, 150.00, 5400.00, '2026-04-17 15:46:04', '2026-04-17 15:46:04'),
(11, 13, 2, 8, 11, 'MM', 9, 2, 'ok', 1, 12.00, 10.00, 120.00, 1000.00, 144000.00, '2026-04-17 15:46:04', '2026-04-17 15:46:04'),
(12, 13, 3, 7, 8, 'inch', 9, 5, NULL, 1, 4.00, 5.00, 20.00, 150.00, 5400.00, '2026-04-17 15:51:26', '2026-04-17 15:51:26'),
(13, 13, 4, 7, 8, 'inch', 9, 5, NULL, 1, 4.00, 5.00, 0.25, 150.00, 37.50, '2026-04-17 17:13:38', '2026-04-17 17:13:38'),
(14, 16, 1, 6, 4, 'MM', 4, 5, 'ok', 1, 1440.00, 1230.00, 20.19, 100.00, 2018.75, '2026-04-17 18:07:43', '2026-04-17 18:07:43'),
(15, 17, 1, 4, 3, 'Feet', 1, 2, NULL, 1, 2000.00, 1000.00, 2000000.00, 500.00, 1000000000.00, '2026-04-18 15:19:23', '2026-04-18 15:19:23'),
(16, 17, 2, 4, 3, 'Feet', 1, 2, NULL, 1, 2000.00, 1000.00, 2000000.00, 500.00, 1000000000.00, '2026-04-18 15:19:52', '2026-04-18 15:19:52'),
(17, 17, 3, 4, 3, 'Feet', 1, 2, NULL, 1, 2000.00, 1000.00, 2000000.00, 500.00, 1000000000.00, '2026-04-18 15:19:52', '2026-04-18 15:19:52'),
(18, 17, 4, 4, 3, 'MM', 1, 2, NULL, 1, 2000.00, 1000.00, 23.63, 500.00, 11812.50, '2026-04-18 15:20:17', '2026-04-18 15:20:17'),
(19, 17, 5, 4, 3, 'MM', 1, 2, NULL, 1, 2000.00, 1000.00, 23.63, 500.00, 11812.50, '2026-04-18 15:20:18', '2026-04-18 15:20:18'),
(20, 17, 6, 4, 3, 'MM', 1, 2, NULL, 1, 2000.00, 1000.00, 23.63, 500.00, 11812.50, '2026-04-18 15:40:06', '2026-04-18 15:40:06'),
(21, 19, 1, 6, 4, 'inch', 1, 6, '3 light with d fogger', 1, 24.00, 20.00, 4.00, 1200.00, 4800.00, '2026-04-18 17:37:52', '2026-04-18 17:37:52'),
(22, 20, 1, 6, 4, 'inch', 9, 2, NULL, 1, 36.00, 25.00, 6.75, 100.00, 675.00, '2026-04-19 12:50:05', '2026-04-19 12:50:05'),
(23, 20, 2, 6, 4, 'inch', 9, 2, NULL, 1, 36.00, 25.00, 6.75, 100.00, 675.00, '2026-04-19 13:06:05', '2026-04-19 13:06:05'),
(24, 20, 2, 4, 3, 'inch', 9, 2, NULL, 1, 36.00, 25.00, 6.75, 100.00, 675.00, '2026-04-19 13:06:05', '2026-04-19 13:06:05'),
(25, 20, 3, 6, 4, 'inch', 9, 2, NULL, 2, 36.00, 25.00, 6.75, 100.00, 1350.00, '2026-04-19 13:08:10', '2026-04-19 13:08:10'),
(26, 20, 3, 4, 3, 'inch', 9, 2, NULL, 3, 36.00, 25.00, 6.75, 100.00, 2025.00, '2026-04-19 13:08:10', '2026-04-19 13:08:10'),
(27, 19, 2, 4, 3, 'inch', 1, 6, 'corner round', 3, 78.00, 30.00, 16.25, 800.00, 39000.00, '2026-04-20 11:15:41', '2026-04-20 11:15:41'),
(28, 16, 2, 6, 4, 'MM', 4, 5, 'ok', 1, 1440.00, 1230.00, 20.19, 100.00, 2018.75, '2026-04-20 14:21:02', '2026-04-20 14:21:02'),
(29, 20, 4, 6, 4, 'inch', 9, 2, NULL, 2, 36.00, 25.00, 13.50, 100.00, 1350.00, '2026-04-20 15:28:55', '2026-04-20 15:28:55'),
(30, 20, 4, 4, 3, 'inch', 9, 5, NULL, 3, 36.00, 25.00, 20.25, 100.00, 2025.00, '2026-04-20 15:28:55', '2026-04-20 15:28:55');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `iProductId` bigint(20) UNSIGNED NOT NULL,
  `iCategoryId` bigint(20) UNSIGNED NOT NULL,
  `strProductName` varchar(100) NOT NULL,
  `MRP` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`iProductId`, `iCategoryId`, `strProductName`, `MRP`, `created_at`, `updated_at`) VALUES
(3, 4, 'LED Mirror', 2500, '2026-04-08 21:57:15', '2026-04-08 22:08:41'),
(4, 6, 'Black', 0, '2026-04-08 22:02:53', '2026-04-08 22:02:53'),
(5, 6, 'Gold', 0, '2026-04-08 22:03:10', '2026-04-08 22:03:10'),
(6, 6, 'Rose Gold', 0, '2026-04-08 22:03:17', '2026-04-08 22:03:17'),
(7, 6, 'Silver', 0, '2026-04-08 22:03:22', '2026-04-08 22:03:22'),
(8, 7, 'Custom', 0, '2026-04-08 22:03:50', '2026-04-08 22:03:50'),
(9, 2, 'NC Colour Mirror', 0, '2026-04-08 22:04:20', '2026-04-08 22:04:20'),
(10, 1, 'Plain Mirror', 0, '2026-04-08 22:04:31', '2026-04-08 22:04:31'),
(11, 8, 'Custom', 0, '2026-04-08 22:04:39', '2026-04-08 22:04:39'),
(12, 3, 'V-Groove Mirror', 0, '2026-04-08 22:04:55', '2026-04-08 22:04:55'),
(13, 5, 'WPC Frame Mirror', 0, '2026-04-08 22:05:08', '2026-04-08 22:05:08');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `iCategoryId` bigint(20) UNSIGNED NOT NULL,
  `strCategoryName` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `product_feature` (
  `feature_id` int(11) NOT NULL,
  `feature_name` varchar(100) NOT NULL,
  `iStatus` int(11) NOT NULL DEFAULT 1,
  `isDelete` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `product_shape` (
  `shape_id` int(11) NOT NULL,
  `shape_title` varchar(100) NOT NULL,
  `iStatus` int(11) NOT NULL DEFAULT 1,
  `isDelete` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `quotation_cancel_reasons`
--

CREATE TABLE `quotation_cancel_reasons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(150) NOT NULL,
  `iStatus` tinyint(4) NOT NULL DEFAULT 1,
  `isDelete` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotation_cancel_reasons`
--

INSERT INTO `quotation_cancel_reasons` (`id`, `reason`, `iStatus`, `isDelete`, `created_at`, `updated_at`) VALUES
(1, 'Price too high', 1, 0, '2026-04-20 06:52:11', '2026-04-20 06:52:11'),
(2, 'Customer chose a competitor', 1, 0, '2026-04-20 06:52:24', '2026-04-20 06:52:24'),
(3, 'Project was delayed, cancelled, or put on hold by the client', 1, 0, '2026-04-20 06:52:34', '2026-04-20 06:52:34'),
(4, 'Scope of work changed', 1, 0, '2026-04-20 06:52:41', '2026-04-20 06:52:41'),
(5, 'Product not available', 1, 0, '2026-04-20 06:52:54', '2026-04-20 06:52:54'),
(6, 'Duplicate quotation', 1, 0, '2026-04-20 06:53:11', '2026-04-20 06:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
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

CREATE TABLE `sendemaildetails` (
  `id` int(11) NOT NULL,
  `strSubject` varchar(50) DEFAULT NULL,
  `strTitle` varchar(50) DEFAULT NULL,
  `strFromMail` varchar(250) DEFAULT NULL,
  `ToMail` varchar(250) DEFAULT NULL,
  `strCC` varchar(250) DEFAULT NULL,
  `strBCC` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `sitename` varchar(5000) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT 1,
  `isDelete` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `strIP` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `sitename`, `logo`, `email`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `strIP`) VALUES
(1, 'Jewellery crm', '1746446528.png', 'dev5.apolloinfotech@gmail.com', 1, 0, '2025-05-05 12:02:08', NULL, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `showrooms`
--

CREATE TABLE `showrooms` (
  `iShowroomId` bigint(20) UNSIGNED NOT NULL,
  `strShowRoomName` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `showrooms`
--

INSERT INTO `showrooms` (`iShowroomId`, `strShowRoomName`, `created_at`, `updated_at`) VALUES
(1, 'Isanpur', '2026-03-11 09:40:03', '2026-04-07 21:31:58'),
(2, 'Gota', '2026-03-11 09:40:03', '2026-04-07 21:31:50'),
(3, 'Monty Bhai', '2026-04-07 21:32:06', '2026-04-07 21:32:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `strUserName` varchar(50) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `strUserMobile` varchar(15) DEFAULT NULL,
  `strUserAddress` text DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL DEFAULT 2 COMMENT '1=Admin, 2=TA/TP',
  `iRoalId` bigint(20) UNSIGNED DEFAULT NULL,
  `can_view_financial` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = can view quotation with prices, 0 = PDF without financial data',
  `otp` int(11) DEFAULT NULL,
  `otpTimeOut` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `device_token` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `strUserName`, `first_name`, `last_name`, `email`, `mobile_number`, `strUserMobile`, `strUserAddress`, `email_verified_at`, `password`, `role_id`, `iRoalId`, `can_view_financial`, `otp`, `otpTimeOut`, `status`, `remember_token`, `device_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Super', 'admin', 'admin@admin.com', '9876543210', NULL, NULL, NULL, '$2y$10$sPrSb4x/ajMNN4OAnT6pLe4jQXOovPn.05aQ9HlpTA5faYqRTUilO', 1, NULL, 0, NULL, NULL, 1, NULL, NULL, '2022-09-12 04:33:06', '2026-04-07 12:35:07'),
(30, 'Monty', 'Monty', NULL, 'monty@hypermirror.in', '7405800312', '7405800312', 'maninagar', NULL, '$2y$10$JYknw3y3mvUYXijNXXWZpeg8tIoYRnYsVuxXm41zIwR4Bqu/e.H4W', 2, 1, 1, NULL, NULL, 1, NULL, NULL, '2026-04-07 22:02:10', '2026-04-07 22:02:10'),
(31, 'Monty_Measurement', 'Monty_Measurement', NULL, 'Monty_Measurement@hypermirror.in', '9876543210', '9876543210', 'test', NULL, '$2y$10$mou29mLxLjgW8MtQeL9wXOdUsoub5ZqGVeCw84pJ.7Ysthx60EBim', 2, 2, 0, NULL, NULL, 1, NULL, NULL, '2026-04-07 22:23:51', '2026-04-07 22:23:51'),
(32, 'Nikhil', 'Nikhil', NULL, 'Nikhil@hypermirror.in', '7575002108', '7575002108', NULL, NULL, '$2y$10$.XFROhNDx14mokHFMf52mOlKBMj0X6Bz8FhR4WlTd5gUykPIvOLom', 2, 6, 1, NULL, NULL, 1, NULL, NULL, '2026-04-07 22:25:37', '2026-04-07 22:31:15'),
(33, 'Faizu', 'Faizu', NULL, 'Faizu@hypermirror.in', '8200686465', '8200686465', '-', NULL, '$2y$10$Mp.Ivt3v386VNzq.7T6J1eIvuvC49mXFtzldJqny1IFDXVVlHwbDW', 2, 3, 0, NULL, NULL, 1, NULL, NULL, '2026-04-07 22:31:59', '2026-04-07 22:31:59'),
(34, 'Monu', 'Monu', NULL, 'Monu@hypermirror.in', '7567772637', '7567772637', '-', NULL, '$2y$10$9lwVo/leClXpASL6oA/WX.wuiN3BRDhdQvcKKDjuroVJRv20OjHdm', 2, 4, 0, NULL, NULL, 1, NULL, NULL, '2026-04-07 22:33:02', '2026-04-07 22:33:02'),
(35, 'Akbar', 'Akbar', NULL, 'akbar@hypermirror.in', '8000505538', '8000505538', '-', NULL, '$2y$10$kEs2AtKngmU0L2Mwjb9Y9e4eo2y5NPl2038P4Zprpf.r9hus3e2fO', 2, 5, 0, NULL, NULL, 1, NULL, NULL, '2026-04-07 22:33:41', '2026-04-07 22:33:41'),
(36, 'prerna', 'prerna', NULL, 'dev5.apolloinfotech@gmail.com', '9723391747', '9723391747', 'Sola\r\nScience City', NULL, '$2y$10$94sUaOCRgudb7zgZYVw4puIclVHU.BkNzD9NM5iFTCkOI92VSi2NG', 2, 1, 1, NULL, NULL, 1, NULL, NULL, '2026-04-08 13:23:53', '2026-04-08 13:23:53'),
(37, 'akbar_measurement', 'akbar_measurement', NULL, 'akbar1@hypermirror.in', '9712009800', '9712009800', NULL, NULL, '$2y$10$7pAGvr8LQ.KpO4uJVWL3NeRemjl0yjjBlP1cuhlEP8UuX4W1g8FUG', 2, 2, 0, NULL, NULL, 1, NULL, NULL, '2026-04-18 14:38:36', '2026-04-18 14:38:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_showrooms`
--

CREATE TABLE `user_showrooms` (
  `UserShowRoomId` bigint(20) UNSIGNED NOT NULL,
  `UserId` bigint(20) UNSIGNED NOT NULL,
  `ShowRoomId` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_showrooms`
--

INSERT INTO `user_showrooms` (`UserShowRoomId`, `UserId`, `ShowRoomId`, `created_at`, `updated_at`) VALUES
(1, 30, 3, NULL, NULL),
(2, 31, 3, NULL, NULL),
(3, 32, 3, NULL, NULL),
(4, 33, 2, NULL, NULL),
(5, 33, 1, NULL, NULL),
(6, 33, 3, NULL, NULL),
(7, 34, 2, NULL, NULL),
(8, 34, 1, NULL, NULL),
(9, 34, 3, NULL, NULL),
(10, 35, 2, NULL, NULL),
(11, 35, 1, NULL, NULL),
(12, 35, 3, NULL, NULL),
(13, 36, 2, NULL, NULL),
(14, 37, 2, NULL, NULL),
(15, 37, 1, NULL, NULL),
(16, 37, 3, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_payments_collection`
--
ALTER TABLE `account_payments_collection`
  ADD PRIMARY KEY (`account_payment_id`);

--
-- Indexes for table `admin_payments_collection`
--
ALTER TABLE `admin_payments_collection`
  ADD PRIMARY KEY (`admin_payment_id`);

--
-- Indexes for table `cash_payment_ledger`
--
ALTER TABLE `cash_payment_ledger`
  ADD PRIMARY KEY (`cash_payment_ledger_id`);

--
-- Indexes for table `complain_master`
--
ALTER TABLE `complain_master`
  ADD PRIMARY KEY (`complain_id`);

--
-- Indexes for table `crm_roles`
--
ALTER TABLE `crm_roles`
  ADD PRIMARY KEY (`iRoleId`),
  ADD UNIQUE KEY `uk_crm_roles_role` (`strRole`),
  ADD UNIQUE KEY `uk_crm_roles_slug` (`slug`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`iCustomerId`),
  ADD UNIQUE KEY `uk_customers_mobile` (`strMobile`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`iInvoiceId`),
  ADD UNIQUE KEY `uk_invoice_no` (`strInvoiceNo`),
  ADD KEY `fk_inv_showroom` (`iShowroomId`),
  ADD KEY `fk_inv_user` (`iCreatedBy`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`iItemId`),
  ADD KEY `fk_ii_invoice` (`iInvoiceId`),
  ADD KEY `fk_ii_category` (`iCategoryId`),
  ADD KEY `fk_ii_product` (`iProductId`);

--
-- Indexes for table `invoice_pdf_settings`
--
ALTER TABLE `invoice_pdf_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`iLeadId`),
  ADD UNIQUE KEY `uk_leads_no` (`strLeadNo`),
  ADD KEY `idx_leads_customer` (`iCustomerId`),
  ADD KEY `idx_leads_created_by` (`iCreatedBy`),
  ADD KEY `idx_leads_showroom` (`iShowroomId`);

--
-- Indexes for table `lead_designs`
--
ALTER TABLE `lead_designs`
  ADD PRIMARY KEY (`iLeadDesignId`),
  ADD KEY `idx_lead_designs_lead` (`iLeadId`);

--
-- Indexes for table `lead_histories`
--
ALTER TABLE `lead_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lead_histories_lead` (`iLeadId`),
  ADD KEY `idx_lead_histories_user` (`iEnterBy`);

--
-- Indexes for table `lead_payments`
--
ALTER TABLE `lead_payments`
  ADD PRIMARY KEY (`iLeadPaymentId`),
  ADD KEY `idx_lead_payments_lead` (`iLeadId`),
  ADD KEY `idx_lead_payments_user` (`iUserID`);

--
-- Indexes for table `lead_quotations`
--
ALTER TABLE `lead_quotations`
  ADD PRIMARY KEY (`iQuotationId`),
  ADD KEY `idx_lead_quotations_lead` (`iLeadId`),
  ADD KEY `idx_lead_quotations_category` (`iProductCategoryId`),
  ADD KEY `idx_lead_quotations_product` (`iProductId`),
  ADD KEY `lead_quotation_batch_idx` (`iLeadId`,`quotation_batch_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`iProductId`),
  ADD KEY `idx_products_category` (`iCategoryId`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`iCategoryId`),
  ADD UNIQUE KEY `uk_product_categories_name` (`strCategoryName`);

--
-- Indexes for table `product_feature`
--
ALTER TABLE `product_feature`
  ADD PRIMARY KEY (`feature_id`);

--
-- Indexes for table `product_shape`
--
ALTER TABLE `product_shape`
  ADD PRIMARY KEY (`shape_id`);

--
-- Indexes for table `quotation_cancel_reasons`
--
ALTER TABLE `quotation_cancel_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sendemaildetails`
--
ALTER TABLE `sendemaildetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `showrooms`
--
ALTER TABLE `showrooms`
  ADD PRIMARY KEY (`iShowroomId`),
  ADD UNIQUE KEY `uk_showrooms_name` (`strShowRoomName`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `uk_users_strUserMobile` (`strUserMobile`),
  ADD KEY `fk_users_crm_role` (`iRoalId`);

--
-- Indexes for table `user_showrooms`
--
ALTER TABLE `user_showrooms`
  ADD PRIMARY KEY (`UserShowRoomId`),
  ADD UNIQUE KEY `uk_user_showroom` (`UserId`,`ShowRoomId`),
  ADD KEY `idx_user_showrooms_showroom` (`ShowRoomId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_payments_collection`
--
ALTER TABLE `account_payments_collection`
  MODIFY `account_payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_payments_collection`
--
ALTER TABLE `admin_payments_collection`
  MODIFY `admin_payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cash_payment_ledger`
--
ALTER TABLE `cash_payment_ledger`
  MODIFY `cash_payment_ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `complain_master`
--
ALTER TABLE `complain_master`
  MODIFY `complain_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `crm_roles`
--
ALTER TABLE `crm_roles`
  MODIFY `iRoleId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `iCustomerId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `iInvoiceId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `iItemId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoice_pdf_settings`
--
ALTER TABLE `invoice_pdf_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `iLeadId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lead_designs`
--
ALTER TABLE `lead_designs`
  MODIFY `iLeadDesignId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lead_histories`
--
ALTER TABLE `lead_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `lead_payments`
--
ALTER TABLE `lead_payments`
  MODIFY `iLeadPaymentId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lead_quotations`
--
ALTER TABLE `lead_quotations`
  MODIFY `iQuotationId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `iProductId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `iCategoryId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_feature`
--
ALTER TABLE `product_feature`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_shape`
--
ALTER TABLE `product_shape`
  MODIFY `shape_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `quotation_cancel_reasons`
--
ALTER TABLE `quotation_cancel_reasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sendemaildetails`
--
ALTER TABLE `sendemaildetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `showrooms`
--
ALTER TABLE `showrooms`
  MODIFY `iShowroomId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `user_showrooms`
--
ALTER TABLE `user_showrooms`
  MODIFY `UserShowRoomId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_inv_showroom` FOREIGN KEY (`iShowroomId`) REFERENCES `showrooms` (`iShowroomId`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inv_user` FOREIGN KEY (`iCreatedBy`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_ii_category` FOREIGN KEY (`iCategoryId`) REFERENCES `product_categories` (`iCategoryId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ii_invoice` FOREIGN KEY (`iInvoiceId`) REFERENCES `invoices` (`iInvoiceId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ii_product` FOREIGN KEY (`iProductId`) REFERENCES `products` (`iProductId`) ON DELETE CASCADE;

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
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

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
