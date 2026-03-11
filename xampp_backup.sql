-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2026 at 01:59 PM
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
-- Database: `feu_osdms`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `confiscated_items`
--

CREATE TABLE `confiscated_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `confiscated_by` varchar(255) NOT NULL,
  `confiscated_date` date NOT NULL,
  `storage_location` varchar(255) DEFAULT NULL,
  `status` enum('Safekeeping','Returned','Disposed') NOT NULL DEFAULT 'Safekeeping',
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `confiscated_items`
--

INSERT INTO `confiscated_items` (`id`, `student_id`, `item_name`, `description`, `photo_path`, `confiscated_by`, `confiscated_date`, `storage_location`, `status`, `resolution_notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2023676767', 'Vape', NULL, 'evidence_photos/KMurgNq9dmtgj1UtlHJ7UbvVb5PrDr8OJxrTheMW.jpg', 'OSD Admin', '2026-02-27', NULL, 'Disposed', NULL, '2026-02-27 00:50:21', '2026-02-27 03:33:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `firewall_ips`
--

CREATE TABLE `firewall_ips` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(255) NOT NULL,
  `log_id` int(11) DEFAULT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `firewall_logs`
--

CREATE TABLE `firewall_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL DEFAULT 'medium',
  `middleware` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `request` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `firewall_logs`
--

INSERT INTO `firewall_logs` (`id`, `ip`, `level`, `middleware`, `user_id`, `url`, `referrer`, `request`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '127.0.0.1', 'medium', 'xss', 1, 'http://127.0.0.1:8000/assets/id-recovery/store', 'http://127.0.0.1:8000/assets/id-recovery/create', '_token=3Tc159xoCkgI0L4NP3fxCAE8GE3Dp799AhRgXfg7&student_name=Jose Jerry C. Tuazaon Jr.&student_id=202310790&program=BSITWMA&location_found=F706&cropped_image=data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/4gHYSUNDX1BST0ZJTEUAAQEAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlkZXNjAAAA8AAAACRyWFlaAAABFAAAABRnWFlaAAABKAAAABRiWFlaAAABPAAAABR3dHB0AAABUAAAABRyVFJDAAABZAAAAChnVFJDAAABZAAAAChiVFJDAAABZAAAAChjcHJ0AAABjAAAADxtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEJYWVogAAAAAAAAb6IAADj1AAADkFhZWiAAAAAAAABimQAAt4UAABjaWFlaIAAAAAAAACSgAAAPhAAAts9YWVogAAAAAAAA9tYAAQAAAADTLXBhcmEAAAAAAAQAAAACZmYAAPKnAAANWQAAE9AAAApbAAAAAAAAAABtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACAAAAAcAEcAbwBvAGcAbABlACAASQBuAGMALgAgADIAMAAxADb/2wBDAAIBAQEBAQIBAQECAgICAgQDAgICAgUEBAMEBgUGBgYFBgYGBwkIBgcJBwYGCAsICQoKCgoKBggLDAsKDAkKCgr/2wBDAQICAgICAgUDAwUKBwYHCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgr/wAARCAQAAo8DASIAAhEBAxEB/8QAHgAAAAYDAQEAAAAAAAAAAAAAAAECAwQFBgcICQr/xABVEAABAgQFAgQFAgMGBAEJAREBAgMABAURBgcSITEIQQkTIlEUMmFxgSORFUKhFiQzUrHRF2JywVMKGCU0Q1RjguFkc5KTlBknNYOioyZEVXSEsvD/xAAdAQABBQEBAQEAAAAAAAAAAAAAAQIDBAUGBwgJ/8QARREAAgEDAgMEBgcGAwgCAwAAAAECAwQRBSEGEjEHE0FRFCIyYXGRFRYzUnKBsSM0NUJiwRckoQglJjZEVNHhQ1MYsvH/2gAMAwEAAhEDEQA/AOoJKinDNacrdSxK4unzKNIaetZJPtFVOYXapmKRiCUxev8AhbgOtlxQAST3ib/a1usSE3Rq9h4NplHdLCSo+sAcj3iNSCrEjr9Hm6c4w00kFvUk2WLRcAmYgDlCm5LEcnWf7ind0E7KHvEHGD9N/ibON5OrhqWWkeZdXpVEVyqTU7OrwdPUhZlkIshRB3jI8sMqsQYkDtLrtODdGQbNh0bkfS8Z2p6naaVbutczUYrxYFHP0B/Fj0jivA9TdddZUCttncLHtEvF2XmcuJGpd6hNOS8wFArKhYEexjdmFMG4YwFT00rDcglDaffcxbNuqXc8W9o8I17txoW9V0tPhzY8WBynjbp16o67JpYpU9JSzl7lxTigf9Iw+pdJfV8WNLuO5Fg3GlZeP+to7hTMuBOhRuPqYj1SSkqvLiVnWSpF72CiI5SXbfrEl7KRZjFcq2ONv/Mx6uK3TEsv45kEOEW1pfUSfrxE+i9DfVNJ01VPdzDp+s761zBJv+0diSIDMullBISgWTv2h/WVD5r7c3ijPtt4jdR8uMDlFN9DjaU6E+q2XSou5pyLiidiXybf0hwdE3Vk6lUpUc0ZJxpW2nzjx+0disuONJ0lZV94dae1K1KG47QtPts4hlNKTWBZ0vV2OXcIeGp1NqlUzSs2pUIPyIS+SAPyIsE+Fhn/ADc8qZn', '2026-02-23 06:48:54', '2026-02-23 06:48:54', NULL),
(2, '127.0.0.1', 'medium', 'login', 0, 'http://127.0.0.1:8000/login', 'http://127.0.0.1:8000/login', '_token=6w5NQYsgO69tkOxktBwObsWrFIRCv5YkYEImf6x3&email=admin@feu.edu.ph&password=******', '2026-02-24 21:06:10', '2026-02-24 21:06:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gate_entries`
--

CREATE TABLE `gate_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `guard_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(255) NOT NULL DEFAULT 'No ID',
  `time_in` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_out` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gate_entries`
--

INSERT INTO `gate_entries` (`id`, `student_id`, `guard_id`, `reason`, `time_in`, `time_out`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 3, 1, 'Forgot ID', '2026-02-20 04:02:06', NULL, '2026-02-20 04:02:06', '2026-02-20 04:02:06', NULL),
(5, 3, 1, 'Forgot ID', '2026-02-20 04:02:12', NULL, '2026-02-20 04:02:12', '2026-02-20 04:02:12', NULL),
(6, 3, 1, 'Forgot ID', '2026-02-20 04:02:14', NULL, '2026-02-20 04:02:14', '2026-02-20 04:02:14', NULL),
(7, 3, 1, 'Lost ID', '2026-02-20 23:35:29', NULL, '2026-02-20 23:35:29', '2026-02-20 23:35:29', NULL),
(8, 3, 1, 'Lost ID', '2026-02-20 23:44:47', NULL, '2026-02-20 23:44:47', '2026-02-20 23:44:47', NULL),
(9, 3, 1, 'Forgot ID', '2026-02-20 23:45:25', NULL, '2026-02-20 23:45:25', '2026-02-20 23:45:25', NULL),
(10, 3, 1, 'Forgot ID', '2026-02-22 23:42:42', NULL, '2026-02-22 23:42:42', '2026-02-22 23:42:42', NULL),
(11, 3, 1, 'Forgot ID', '2026-02-22 23:42:50', NULL, '2026-02-22 23:42:50', '2026-02-22 23:42:50', NULL),
(12, 3, 1, 'Forgot ID', '2026-02-22 23:42:56', NULL, '2026-02-22 23:42:56', '2026-02-22 23:42:56', NULL),
(14, 3, 1, 'Forgot ID', '2026-02-24 04:40:42', NULL, '2026-02-24 04:40:42', '2026-02-24 04:40:42', NULL),
(15, 3, 1, 'Forgot ID', '2026-02-24 04:40:42', NULL, '2026-02-24 04:40:42', '2026-02-24 04:40:42', NULL),
(16, 3, 1, 'Forgot ID', '2026-02-24 21:14:06', NULL, '2026-02-24 21:14:06', '2026-02-24 21:14:06', NULL),
(17, 3, 1, 'Forgot ID', '2026-02-24 21:14:17', NULL, '2026-02-24 21:14:17', '2026-02-24 21:14:17', NULL),
(18, 3, 1, 'Forgot ID', '2026-02-24 21:14:25', NULL, '2026-02-24 21:14:25', '2026-02-24 21:14:25', NULL),
(19, 3, 1, 'Forgot ID', '2026-02-26 21:20:42', NULL, '2026-02-26 21:20:42', '2026-02-27 03:41:25', '2026-02-27 03:41:25'),
(20, 3, 1, 'Forgot ID', '2026-02-27 04:34:01', NULL, '2026-02-27 04:34:01', '2026-02-27 04:34:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `incident_reports`
--

CREATE TABLE `incident_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_name` varchar(255) NOT NULL,
  `reporter_email` varchar(255) DEFAULT NULL,
  `reporter_affiliation` varchar(255) DEFAULT NULL,
  `incident_date` date NOT NULL,
  `incident_location` varchar(255) NOT NULL,
  `incident_category` varchar(255) NOT NULL,
  `severity` varchar(255) NOT NULL DEFAULT 'Routine',
  `description` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending Review',
  `action_taken` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incident_reports`
--

INSERT INTO `incident_reports` (`id`, `reporter_name`, `reporter_email`, `reporter_affiliation`, `incident_date`, `incident_location`, `incident_category`, `severity`, `description`, `status`, `action_taken`, `evidence_path`, `created_at`, `updated_at`, `deleted_at`, `student_id`) VALUES
(2, 'Jose Jerry C. Tuazon Jr.', 'josejerry26@example.com', 'BSITWMA', '2026-02-27', 'Canteen', 'Theft', 'Routine', 'Someone stole my Hirono', 'Pending Review', NULL, NULL, '2026-02-27 04:08:51', '2026-02-27 04:08:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_items`
--

CREATE TABLE `lost_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `founder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `report_type` enum('Lost','Found') NOT NULL DEFAULT 'Lost',
  `item_category` varchar(255) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `student_number` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location_found` varchar(255) DEFAULT NULL,
  `date_lost` date DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `is_claimed` tinyint(1) NOT NULL DEFAULT 0,
  `is_stock_image` tinyint(1) NOT NULL DEFAULT 0,
  `flagged_for_review` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lost_items`
--

INSERT INTO `lost_items` (`id`, `item_name`, `tracking_number`, `student_id`, `founder_id`, `report_type`, `item_category`, `student_name`, `student_number`, `course`, `description`, `location_found`, `date_lost`, `image_path`, `status`, `is_claimed`, `is_stock_image`, `flagged_for_review`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Hirono King Figure', 'TRK-699A8D2F', NULL, NULL, 'Lost', 'Others', NULL, NULL, NULL, 'From The Le Petit Prince Series. Has a crown and a staff.', 'Canteen', '2026-02-22', 'assets/Mt3eLqXztBQ15cJLa3VX5PkiylvHzM6SbgvtLUaY.webp', 'Claimed', 0, 1, 0, '2026-02-21 20:59:27', '2026-02-21 21:06:35', NULL),
(3, 'Hirono King Figure', 'TRK-699C21DC', NULL, NULL, 'Lost', 'Others', NULL, NULL, NULL, 'Hirono King Figure', 'Pavillon', '2026-02-23', 'assets/bpX2gq44cvO4pH49BN39vtVsA0gpm1pGSTUY42oa.jpg', 'Active', 0, 0, 0, '2026-02-23 01:46:04', '2026-02-23 01:46:04', NULL),
(4, 'Flask', 'TRK-699C2855', NULL, NULL, 'Lost', 'Others', NULL, NULL, NULL, 'Pink Sunnies Flask', 'F706', '2026-02-23', 'assets/Wkv83taCXrnQJCl6JGDLAlGiYDuJ81uXbq65b9ri.jpg', 'Active', 0, 0, 0, '2026-02-23 02:13:41', '2026-02-23 02:13:41', NULL),
(5, 'Student ID: Jose Jerry C. Tuazaon Jr.', 'TRK-699C6B74', NULL, NULL, 'Found', 'ID / Identification', NULL, NULL, NULL, 'NAME: Jose Jerry C. Tuazaon Jr. | ID: 202310790 | PROGRAM: BSITWMA', 'F706', '2026-02-23', 'assets/699c6b74ee054.jpg', 'Active', 0, 0, 0, '2026-02-23 07:00:04', '2026-02-23 07:00:04', NULL),
(6, 'Flask', 'TRK-699D8B52', NULL, NULL, 'Found', 'Others', NULL, NULL, NULL, 'A pink sunnies flask', 'Pavillon', '2026-02-24', 'assets/brW55YreFyPCEXH9VYWSYgukyiY7gdEKNtPR7E6p.jpg', 'Claimed', 0, 1, 0, '2026-02-24 03:28:18', '2026-02-27 03:26:35', '2026-02-27 03:26:35'),
(7, 'Blue Aquaflask', 'TRK-69A12A17', NULL, NULL, 'Lost', 'Others', NULL, NULL, NULL, 'Cobalt Blue Aquaflask', 'Pavillon', '2026-02-27', 'assets/LRjMcx4gLLPpT96RYk9D8XUJ7cxZA4Dt81PjfRoJ.jpg', 'Active', 0, 1, 0, '2026-02-26 21:22:31', '2026-02-27 04:36:57', '2026-02-27 04:36:57'),
(8, 'ipad', 'TRK-69A271AA', NULL, NULL, 'Lost', 'Electronics', NULL, NULL, NULL, 'dadas', 'Canteen', '2026-02-28', 'assets/BK0V375quAs8GaOtVBeYt74Edshu61O5Qy5yeASC.jpg', 'Active', 0, 0, 0, '2026-02-27 20:40:10', '2026-02-27 20:40:10', NULL),
(9, 'Cobalt Blue Aquaflask', 'TRK-69A27382', NULL, NULL, 'Lost', 'Others', NULL, NULL, NULL, 'Plain', 'Canteen', '2026-02-28', 'assets/XaAh2Vmhswzol5QFvyOjjeuKcdi1aypGPj0cAoti.jpg', 'Active', 0, 1, 0, '2026-02-27 20:48:02', '2026-02-27 20:49:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(9, '0001_01_01_000000_create_users_table', 1),
(10, '0001_01_01_000001_create_cache_table', 1),
(11, '0001_01_01_000002_create_jobs_table', 1),
(12, '2026_02_18_131607_add_fields_to_users_table', 1),
(13, '2026_02_18_131624_create_lost_items_table', 1),
(14, '2026_02_18_131628_create_gate_entries_table', 1),
(15, '2026_02_18_131823_create_violations_table', 1),
(16, '2026_02_18_133124_create_personal_access_tokens_table', 1),
(17, '2026_02_20_031934_create_table_name_here_table', 2),
(18, '2026_02_20_133055_add_forensic_fields_to_lost_items', 3),
(19, '2026_02_20_133801_add_missing_id_fields_to_lost_items', 4),
(20, '2026_02_21_144932_create_incident_reports_table', 5),
(21, '2026_02_21_154057_add_student_id_to_lost_items_table', 6),
(22, '2026_02_21_155834_add_item_name_to_lost_items_table', 7),
(23, '2019_07_15_000000_create_firewall_ips_table', 8),
(24, '2019_07_15_000000_create_firewall_logs_table', 8),
(25, '2026_02_27_071709_create_confiscated_items_table', 9),
(26, '2026_02_27_082754_add_photo_to_confiscated_items_table', 10),
(27, '2026_02_27_102413_enable_soft_deletes_on_all_modules', 11),
(28, '2026_02_27_111158_add_deleted_at_to_users_table', 12),
(29, '2026_02_27_122953_add_student_id_to_incident_reports_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('5X2RADYyjn9o7VHTnnrMre7sNvK5z5v3j1jYopfU', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQW1NMFZURkZmSENURlNxWU9wc1FDZTlEUDM1aEhER0kxbkJCV1hCVCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zdG9yYWdlL2Fzc2V0cy9YYUFoMlZtaHN3em9sNVFGdnlPampldUtjZGkxYXlwR1BqMGNBb3RpLmpwZyI7czo1OiJyb3V0ZSI7czoxMToiYXNzZXRzLnNob3ciO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1772254204),
('kIjTURLusYPgAjqn0lYfJZd6YskikBZOlMZutx44', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZk1xTzZHU1RueWxmQ3BHMDdDVVdHQmZmQ2xaQm84UW9OVGpQcWdobyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zdG9yYWdlL2Fzc2V0cy82OTljNmI3NGVlMDU0LmpwZyI7czo1OiJyb3V0ZSI7czoxMToiYXNzZXRzLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1772367860);

-- --------------------------------------------------------

--
-- Table structure for table `table_name_here`
--

CREATE TABLE `table_name_here` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_number` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'student',
  `program_code` varchar(255) DEFAULT NULL,
  `campus` varchar(255) NOT NULL DEFAULT 'Manila',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `id_number`, `role`, `program_code`, `campus`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'OSD Admin', 'admin@feu.edu.ph', '20200001', 'admin', NULL, 'Manila', NULL, '$2y$12$k/dZjigvOaOfzEQRb5V1quexof1Au6a/gQ.GdTgaC1XKr2Qjpq4Hu', 'rwpChtvgj7zR7K60nXxhUIPxMEqc7EgCNT9ItYSlT89RfQtX2HMMmbGy97AY', '2026-02-19 19:26:28', '2026-02-19 19:26:28', NULL),
(2, 'Kuya Guard', 'guard@feu.edu.ph', 'GUARD001', 'guard', NULL, 'Manila', NULL, '$2y$12$6csSGzhKhuhKC2vYWA811.E1X9oUCDGzEq.rthm.5hc/BFGbbF9ve', NULL, '2026-02-19 19:26:28', '2026-02-19 19:26:28', NULL),
(3, 'Jose Jerry Tuazon', 'student@feu.edu.ph', '202312345', 'student', 'BSIT', 'Manila', NULL, '$2y$12$Mzzs6GEgvbegdWKqUJKsyuvUWaAl2J/1tkH83fh5ZuoQSqs54Zzr.', NULL, '2026-02-19 19:26:28', '2026-02-19 19:26:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

CREATE TABLE `violations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL,
  `offense_type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `findings` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `final_action` text DEFAULT NULL,
  `academic_term` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `violations`
--

INSERT INTO `violations` (`id`, `student_id`, `reporter_id`, `offense_type`, `description`, `findings`, `recommendation`, `final_action`, `academic_term`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-24 03:37:53', '2026-02-24 03:37:53', NULL),
(5, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-24 04:40:42', '2026-02-24 04:40:42', NULL),
(6, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-24 04:40:42', '2026-02-24 04:40:42', NULL),
(7, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-24 21:14:06', '2026-02-24 21:14:06', NULL),
(8, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-24 21:14:17', '2026-02-24 21:14:17', NULL),
(9, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-24 21:14:25', '2026-02-24 21:14:25', NULL),
(10, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-26 21:20:42', '2026-02-26 21:20:42', NULL),
(11, 3, 1, 'Excessive ID Passes (Automated)', 'System Auto-Generated: Student reached maximum temporary gate passes.', NULL, NULL, NULL, '2nd Semester 2025-2026', 'Active', '2026-02-27 04:34:01', '2026-02-27 04:34:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `confiscated_items`
--
ALTER TABLE `confiscated_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `firewall_ips`
--
ALTER TABLE `firewall_ips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `firewall_ips_ip_index` (`ip`);

--
-- Indexes for table `firewall_logs`
--
ALTER TABLE `firewall_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `firewall_logs_ip_index` (`ip`);

--
-- Indexes for table `gate_entries`
--
ALTER TABLE `gate_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incident_reports`
--
ALTER TABLE `incident_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_reports_student_id_foreign` (`student_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `table_name_here`
--
ALTER TABLE `table_name_here`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_id_number_unique` (`id_number`);

--
-- Indexes for table `violations`
--
ALTER TABLE `violations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `violations_student_id_foreign` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `confiscated_items`
--
ALTER TABLE `confiscated_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `firewall_ips`
--
ALTER TABLE `firewall_ips`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `firewall_logs`
--
ALTER TABLE `firewall_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gate_entries`
--
ALTER TABLE `gate_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `incident_reports`
--
ALTER TABLE `incident_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_name_here`
--
ALTER TABLE `table_name_here`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `violations`
--
ALTER TABLE `violations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `incident_reports`
--
ALTER TABLE `incident_reports`
  ADD CONSTRAINT `incident_reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `violations`
--
ALTER TABLE `violations`
  ADD CONSTRAINT `violations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
