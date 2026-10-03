/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.20-12.3.3-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: pln2
-- ------------------------------------------------------
-- Server version	12.3.3-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `decisions`
--

DROP TABLE IF EXISTS `decisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `decisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `decision_by` bigint(20) unsigned NOT NULL,
  `decision_level` enum('TL','ASMAN','MUP') NOT NULL,
  `decision` text NOT NULL,
  `rationale` text DEFAULT NULL,
  `follow_up` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `decisions_issue_id_foreign` (`issue_id`),
  KEY `decisions_decision_by_foreign` (`decision_by`),
  CONSTRAINT `decisions_decision_by_foreign` FOREIGN KEY (`decision_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `decisions_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `decisions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `decisions` WRITE;
/*!40000 ALTER TABLE `decisions` DISABLE KEYS */;
INSERT INTO `decisions` VALUES
(1,3,11,'TL','yuydytdfty','gada','ter','2026-10-02 06:04:19','2026-10-02 06:04:19'),
(2,3,11,'TL','bjhuyfgu','jvjhvbh','iugiu','2026-10-02 06:09:19','2026-10-02 06:09:19');
/*!40000 ALTER TABLE `decisions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `escalations`
--

DROP TABLE IF EXISTS `escalations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `escalations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `from_level` enum('STAFF','TL','ASMAN') NOT NULL,
  `from_user_id` bigint(20) unsigned NOT NULL,
  `to_level` enum('TL','ASMAN','MUP') NOT NULL,
  `to_user_id` bigint(20) unsigned DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Accepted','Decided','Closed') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `escalations_issue_id_foreign` (`issue_id`),
  KEY `escalations_from_user_id_foreign` (`from_user_id`),
  KEY `escalations_to_user_id_foreign` (`to_user_id`),
  CONSTRAINT `escalations_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `escalations_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `escalations_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escalations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `escalations` WRITE;
/*!40000 ALTER TABLE `escalations` DISABLE KEYS */;
INSERT INTO `escalations` VALUES
(1,1,'TL',11,'ASMAN',NULL,'9315231TA','Pending','2026-10-02 06:19:04','2026-10-02 06:19:04');
/*!40000 ALTER TABLE `escalations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `information`
--

DROP TABLE IF EXISTS `information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `information` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` enum('ARAHAN','KEBIJAKAN','PENGUMUMAN','REGULASI') NOT NULL DEFAULT 'PENGUMUMAN',
  `target_level` enum('ALL','MUP','ASMAN','TL','STAFF') NOT NULL DEFAULT 'ALL',
  `target_unit_id` bigint(20) unsigned DEFAULT NULL,
  `publisher_id` bigint(20) unsigned NOT NULL,
  `published_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `information_target_unit_id_foreign` (`target_unit_id`),
  KEY `information_publisher_id_foreign` (`publisher_id`),
  CONSTRAINT `information_publisher_id_foreign` FOREIGN KEY (`publisher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `information_target_unit_id_foreign` FOREIGN KEY (`target_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `information`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `information` WRITE;
/*!40000 ALTER TABLE `information` DISABLE KEYS */;
INSERT INTO `information` VALUES
(1,'Kesiapan Siaga Beban Puncak Sistem Kelistrikan Sulselrabar','Seluruh seksi Operasi dan Pemeliharaan diinstruksikan menjaga keandalan pembangkit Blok 1 dan Blok 2. Pastikan jadwal patroli inspeksi harian tercatat di sistem koordinasi dan tidak ada pekerjaan berisiko tinggi tanpa approval.','ARAHAN','ALL',NULL,1,'2026-09-27 12:01:48','2026-10-13 12:01:48',1,'2026-09-29 05:01:48','2026-09-29 05:01:48'),
(2,'Peningkatan Disiplin K3 & Keselamatan Operasi Unit Sengkang','Wajib menggunakan APD lengkap di area turbin hall, HRSG, dan switchyard 150kV. Pastikan JSA dan Working Permit sudah diverifikasi sebelum pekerjaan dimulai. Target Zero Accident tanpa kompromi.','KEBIJAKAN','ALL',NULL,1,'2026-09-25 12:01:48','2026-10-29 12:01:48',1,'2026-09-29 05:01:48','2026-09-29 05:01:48'),
(3,'Optimalisasi Pengawasan Heat Rate Blok 1 & Pemantauan Gas Flow','Diharapkan Team Leader Operasi dan staf operator meningkatkan monitoring efisiensi heat rate serta meminimalkan gas venting saat proses start-up & shut-down turbin.','ARAHAN','TL',2,2,'2026-09-28 12:01:48','2026-10-06 12:01:48',1,'2026-09-29 05:01:48','2026-09-29 05:01:48'),
(4,'Jadwal Preventive Maintenance Mingguan Turbin Gas Unit 1.2','Pelaksanaan pemeriksaan filter udara intake, lube oil system, dan kalibrasi sensor temperatur TG 1.2 dijadwalkan hari Rabu pukul 08:30 WITA.','PENGUMUMAN','STAFF',6,3,'2026-09-29 12:01:48','2026-10-04 12:01:48',1,'2026-09-29 05:01:48','2026-09-29 05:01:48');
/*!40000 ALTER TABLE `information` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `issue_discussions`
--

DROP TABLE IF EXISTS `issue_discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `issue_discussions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_discussions_issue_id_foreign` (`issue_id`),
  KEY `issue_discussions_user_id_foreign` (`user_id`),
  CONSTRAINT `issue_discussions_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `issue_discussions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issue_discussions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `issue_discussions` WRITE;
/*!40000 ALTER TABLE `issue_discussions` DISABLE KEYS */;
/*!40000 ALTER TABLE `issue_discussions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `issues`
--

DROP TABLE IF EXISTS `issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_no` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `issue_type` varchar(255) NOT NULL DEFAULT 'PROBLEM',
  `reported_by` bigint(20) unsigned NOT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `current_level` varchar(255) NOT NULL DEFAULT 'TL',
  `priority` varchar(255) NOT NULL DEFAULT 'MEDIUM',
  `status` varchar(255) NOT NULL DEFAULT 'Open',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issues_issue_no_unique` (`issue_no`),
  KEY `issues_reported_by_foreign` (`reported_by`),
  KEY `issues_owner_id_foreign` (`owner_id`),
  KEY `issues_unit_id_index` (`unit_id`),
  CONSTRAINT `issues_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `issues_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issues`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `issues` WRITE;
/*!40000 ALTER TABLE `issues` DISABLE KEYS */;
INSERT INTO `issues` VALUES
(1,'ISS-2026-3702','Ada problem ini','gada apa apa si',NULL,'PROBLEM',12,4,12,'ASMAN','MEDIUM','Escalated','2026-10-04','2026-10-02 05:48:49','2026-10-02 06:19:04'),
(3,'ISS-2026-7492','cobaa','gaada',NULL,'PROBLEM',13,4,13,'TL','HIGH','Resolved','2026-10-03','2026-10-02 06:01:37','2026-10-02 06:09:19'),
(4,'ISS-2026-3983','tumpah','paswword123\n[Lampiran: 1790946766_download (2).jpg]','1790946766_download (2).jpg','PROBLEM',12,4,12,'TL','LOW','Open','2026-10-03','2026-10-02 06:12:46','2026-10-02 06:12:46');
/*!40000 ALTER TABLE `issues` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `meeting_issues`
--

DROP TABLE IF EXISTS `meeting_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` bigint(20) unsigned NOT NULL,
  `issue_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Agenda',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meeting_issues_meeting_id_issue_id_unique` (`meeting_id`,`issue_id`),
  KEY `meeting_issues_issue_id_foreign` (`issue_id`),
  CONSTRAINT `meeting_issues_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meeting_issues_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_issues`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `meeting_issues` WRITE;
/*!40000 ALTER TABLE `meeting_issues` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_issues` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meetings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_date` date NOT NULL,
  `time_start` varchar(20) DEFAULT '08:00',
  `time_end` varchar(20) DEFAULT '10:00',
  `location` varchar(255) DEFAULT 'Ruang Rapat Utama',
  `meeting_mode` varchar(255) NOT NULL DEFAULT 'Offline',
  `zoom_link` text DEFAULT NULL,
  `zoom_meeting_id` varchar(255) DEFAULT NULL,
  `zoom_passcode` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `leader_id` bigint(20) unsigned DEFAULT NULL,
  `week_number` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Planned',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meetings_leader_id_foreign` (`leader_id`),
  CONSTRAINT `meetings_leader_id_foreign` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meetings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `meetings` WRITE;
/*!40000 ALTER TABLE `meetings` DISABLE KEYS */;
INSERT INTO `meetings` VALUES
(1,'2026-09-15','08:00','10:00','Ruang Rapat Utama','Offline',NULL,NULL,NULL,'Rapat TL + Staf',NULL,NULL,NULL,'Akan Datang',NULL,'2026-09-29 03:13:27','2026-09-29 03:13:27'),
(2,'2026-09-15','08:00','10:00','Ruang Rapat Utama','Offline',NULL,NULL,NULL,'Rapat Asman + TL',NULL,NULL,NULL,'Akan Datang',NULL,'2026-09-29 03:13:27','2026-09-29 03:13:27'),
(3,'2026-09-16','08:00','10:00','Ruang Rapat Utama','Offline',NULL,NULL,NULL,'Rapat Tinjauan Manajemen',NULL,NULL,NULL,'Akan Datang',NULL,'2026-09-29 03:13:27','2026-09-29 03:13:27'),
(4,'2026-09-29','08:00','10:00','Ruang Rapat Utama','Offline',NULL,NULL,NULL,'evakuasi',NULL,NULL,NULL,'Akan Datang',NULL,'2026-09-29 05:03:20','2026-09-29 05:03:20'),
(5,'2026-10-05','13:00','15:00','Ruang Rapat Utama','Offline',NULL,NULL,NULL,'Rapat Asman + TL',1,41,2026,'Akan Datang','Rapat koordinasi evaluasi bulanan','2026-09-29 06:12:16','2026-09-29 06:12:16'),
(6,'2026-09-29','09:00','11:00','Online / Video Conference (Virtual)','Online','https://zoom.us/j/1234567890?pwd=testpasscode','123 456 7890','PLNNP2026','Rapat Koordinasi Operasi & Pemeliharaan',1,40,2026,'Berlangsung','Rapat koordinasi teknis via Zoom','2026-09-29 06:29:11','2026-09-29 06:29:11');
/*!40000 ALTER TABLE `meetings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_09_29_000001_create_organization_units_table',1),
(5,'2026_09_29_000002_create_positions_table',1),
(6,'2026_09_29_000003_create_roles_table',1),
(7,'2026_09_29_000004_update_users_table_add_organization_fields',1),
(8,'2026_09_29_000005_create_meetings_table',1),
(9,'2026_09_29_000006_create_issues_table',1),
(10,'2026_09_29_000007_create_tasks_table',1),
(11,'2026_09_29_000008_create_escalations_table',1),
(12,'2026_09_29_000009_create_decisions_table',1),
(13,'2026_09_29_102743_create_issue_discussions_table',2),
(14,'2026_09_29_112732_create_organization_hierarchies_table',3),
(15,'2026_09_29_114851_create_information_table',4),
(16,'2026_09_29_115518_add_fields_to_tasks_and_meetings_table',4),
(17,'2026_09_29_130831_add_time_and_location_to_meetings_table',5),
(18,'2026_09_29_132317_add_zoom_and_meeting_issues_and_profile_fields',6),
(19,'2026_09_29_150235_add_avatar_to_users_table',7),
(20,'2026_09_29_160000_add_completion_proof_to_tasks_table',8),
(21,'2026_10_01_031411_add_completion_fields_to_tasks_table',9),
(22,'2026_10_02_115608_add_unit_id_to_issues_and_tasks_tables',9),
(23,'2026_10_02_131537_add_attachment_to_issues_table',10),
(24,'2026_10_02_135430_add_tl_and_asman_to_users_table',11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `organization_hierarchies`
--

DROP TABLE IF EXISTS `organization_hierarchies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `organization_hierarchies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `incumbent` varchar(255) NOT NULL,
  `parent` varchar(255) NOT NULL,
  `incumbent_title` varchar(255) DEFAULT NULL,
  `parent_title` varchar(255) DEFAULT NULL,
  `person_name` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_hierarchies`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `organization_hierarchies` WRITE;
/*!40000 ALTER TABLE `organization_hierarchies` DISABLE KEYS */;
INSERT INTO `organization_hierarchies` VALUES
(1,'ASMAN OP','MUP','Asisten Manajer Operasi','Manager UP Sengkang','H. MUHAMMAD ISMAIL',1,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(2,'ASMAN HAR','MUP','Asisten Manajer Pemeliharaan','Manager UP Sengkang','CAHYA PATRIA C.T',2,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(3,'ASMAN ENG','MUP','Asisten Manajer Engineering','Manager UP Sengkang','FUAD ANWAR',3,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(4,'ASMAN BS','MUP','Asisten Manajer Business Support','Manager UP Sengkang','PRAN MOCH SOLEH',4,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(5,'TL K3','MUP','Team Leader K3 & Keamanan','Manager UP Sengkang','YOGA KURNIAWAN',5,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(6,'TL LINGKUNGAN','MUP','Team Leader Lingkungan','Manager UP Sengkang','H. ABDUL MALIK',6,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(7,'TL RENDALOP','ASMAN OP','Team Leader Rendal Operasi','Asisten Manajer Operasi','SINUNG RAHARJO',7,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(8,'TL EP','ASMAN OP','Team Leader Evaluasi Kinerja & Kimia','Asisten Manajer Operasi','ANDI ANANDA MAGFIRAH',8,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(9,'TL PROD 1A','ASMAN OP','Team Leader Produksi Blok 1A','Asisten Manajer Operasi','MURSALAM',9,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(10,'TL PROD 1B','ASMAN OP','Team Leader Produksi Blok 1B','Asisten Manajer Operasi','MEDY IVANTRY TANDI BUA',10,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(11,'TL PROD 1C','ASMAN OP','Team Leader Produksi Blok 1C','Asisten Manajer Operasi','H. HERMAN JAYA, ST',11,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(12,'TL PROD 1D','ASMAN OP','Team Leader Produksi Blok 1D','Asisten Manajer Operasi','MUH NASAR D',12,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(13,'TL PROD 2B','ASMAN OP','Team Leader Produksi Blok 2B','Asisten Manajer Operasi','NADAR',13,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(14,'TL PROD 2C','ASMAN OP','Team Leader Produksi Blok 2C','Asisten Manajer Operasi','AGUSNAWAN',14,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(15,'TL PROD 2D','ASMAN OP','Team Leader Produksi Blok 2D','Asisten Manajer Operasi','SUNARDI SM',15,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(16,'TL HAR MESIN','ASMAN HAR','Team Leader Pemeliharaan Mesin & Sipil','Asisten Manajer Pemeliharaan','SAINAL TAJANG',16,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(17,'TL RENDAL HAR','ASMAN HAR','Team Leader Rendal Pemeliharaan','Asisten Manajer Pemeliharaan','YERSON',17,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(18,'TL HAR LISTRIK','ASMAN HAR','Team Leader Pemeliharaan Listrik','Asisten Manajer Pemeliharaan','SUHAIMI',18,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(19,'TL HAR KONIN','ASMAN HAR','Team Leader Pemeliharaan Kontrol & Instrumen','Asisten Manajer Pemeliharaan','HENDRA GULANG',19,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(20,'TL INVENTORY','ASMAN HAR','Team Leader Inventory & Gudang','Asisten Manajer Pemeliharaan','NURHALIM',20,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(21,'TL OUTAGE','ASMAN HAR','Team Leader Outage Management','Asisten Manajer Pemeliharaan','MUHAMMAD SIDDIQ B.',21,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(22,'TL CBM','ASMAN ENG','Team Leader Condition Based Maintenance','Asisten Manajer Engineering','ARMAN ARIFIN',22,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(23,'TL SO & CA','ASMAN ENG','Team Leader System Owner & Component Analyst','Asisten Manajer Engineering','HARI AKBAR SYUAIB',23,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(24,'TL MMRK','ASMAN ENG','Team Leader Manajemen Mutu, Risiko & Kinerja','Asisten Manajer Engineering','AFFANDY HERMAWAN',24,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(25,'TL KEUANGAN','ASMAN BS','Team Leader Keuangan','Asisten Manajer Business Support','ZUL QADRI A. SADIK',25,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(26,'TL SDM & UMUM','ASMAN BS','Team Leader SDM, Umum & CSR','Asisten Manajer Business Support','ANDI RIDWANSYAH B.P',26,'2026-09-29 04:28:27','2026-09-29 04:28:27'),
(27,'TL PENGADAAN','ASMAN BS','Team Leader Pengadaan & Logistik','Asisten Manajer Business Support','AGUS SALIM HANAFI',27,'2026-09-29 04:28:27','2026-09-29 04:28:27');
/*!40000 ALTER TABLE `organization_hierarchies` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `organization_units`
--

DROP TABLE IF EXISTS `organization_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `organization_units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_unit_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organization_units_parent_unit_id_foreign` (`parent_unit_id`),
  CONSTRAINT `organization_units_parent_unit_id_foreign` FOREIGN KEY (`parent_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_units`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `organization_units` WRITE;
/*!40000 ALTER TABLE `organization_units` DISABLE KEYS */;
INSERT INTO `organization_units` VALUES
(1,NULL,'UP Sengkang','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(2,1,'Bagian Operasi','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(3,2,'Seksi Operasi 1A','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(4,1,'K3 & Keamanan','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(5,1,'Lingkungan','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(6,1,'Bagian Pemeliharaan','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(7,1,'Bagian Engineering & QA','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(8,1,'Bagian Business Support','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(9,2,'Rendal Operasi','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(10,2,'Kimia','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(11,2,'Produksi Blok 1 A','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(12,2,'Produksi Blok 1 B','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(13,2,'Produksi Blok 1 C','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(14,2,'Produksi Blok 1 D','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(15,2,'Produksi Blok 2 A','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(16,2,'Produksi Blok 2 B','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(17,2,'Produksi Blok 2 C','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(18,2,'Produksi Blok 2 D','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(19,6,'Rendal Pemeliharaan','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(20,6,'Pemeliharaan Listrik','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(21,6,'Pemeliharaan Kontrol & Instrumen','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(22,6,'Pemeliharaan Mesin & Sipil','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(23,6,'Outage Management','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(24,6,'Inventory Control & Gudang','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(25,7,'System Owner & Component Analyst','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(26,7,'Condition Based Maintenance','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(27,7,'Manajemen Mutu, Risiko & Kinerja','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(28,8,'SDM, Umum & CSR','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(29,8,'Sekretaris','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(30,8,'Logistik & Pengadaan','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(31,8,'Keuangan','2026-09-29 04:17:30','2026-09-29 04:17:30');
/*!40000 ALTER TABLE `organization_units` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES
(1,'Manajer Unit Pelaksana','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(2,'Asisten Manajer Operasi','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(3,'Team Leader Operasi 1A','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(4,'Operator & Teknisi Pembangkit','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(5,'MANAGER UNIT','2026-09-29 04:17:30','2026-09-29 04:17:30'),
(6,'TEAM LEADER K3 DAN KEAMANAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(7,'TECHNICIAN K3 & KEAMANAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(8,'JUNIOR K3 & KEAMANAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(9,'TENAGA PENDUKUNG K3 & KEAMANAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(10,'TEAM LEADER LINGKUNGAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(11,'TENAGA PENDUKUNG LINGKUNGAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(12,'JUNIOR OFFICER LINGKUNGAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(13,'ASMAN ENGINEERING & QA','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(14,'TEAM LEADER SYSTEM OWNER & COMPONENT ANALYST','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(15,'TECHNICIAN SYSTEM OWNER','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(16,'TECHNICIAN COMPONENT ANALYST','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(17,'TENAGA PENDUKUNG SYSTEM OWNER & COMPONENT ANALYST','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(18,'TEAM LEADER CONDITION BASED MAINTENANCE','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(19,'TECHNICIAN CONDITION BASED MAINTENANCE','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(20,'JUNIOR TECHNICIAN CONDITION BASED MAINTENANCE','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(21,'TENAGA PENDUKUNG TEKNIK CONDITION BASED MAINTENANCE','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(22,'TEAM LEADER MANAJEMEN MUTU, RISIKO & KINERJA','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(23,'OFFICER MANAJEMEN MUTU, RISIKO & KEPATUHAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(24,'JUNIOR OFFICER MANAJEMEN MUTU, RISIKO & KINERJA','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(25,'TENAGA PENDUKUNG MANAJEMEN MUTU, RISIKO & KINERJA','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(26,'ASMAN OPERASI','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(27,'TEAM LEADER RENDAL OPERASI','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(28,'OFFICER RENDAL OPERASI','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(29,'TENAGA PENDUKUNG PERENCANAAN & PENGENDALIAN OPERASI','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(30,'JUNIOR OFFICER KIMIA','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(31,'TENAGA PENDUKUNG KIMIA','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(32,'TEAM LEADER PRODUKSI BLOK 1 A','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(33,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 A','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(34,'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 A','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(35,'JUNIOR TECHNICIAN PRODUKSI BLOK-1 A','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(36,'TEAM LEADER PRODUKSI BLOK 1 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(37,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(38,'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(39,'JUNIOR TECHNICIAN PRODUKSI BLOK-1 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(40,'TEAM LEADER PRODUKSI BLOK 1 C','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(41,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 C','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(42,'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 C','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(43,'JUNIOR TECHNICIAN PRODUKSI BLOK 1 C','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(44,'TEAM LEADER PRODUKSI BLOK 1 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(45,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(46,'JUNIOR TECHNICIAN PRODUKSI BLOK 1 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(47,'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK1 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(48,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 A','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(49,'JUNIOR TECHNICIAN PRODUKSI BLOK 2 A','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(50,'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 A','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(51,'TEAM LEADER PRODUKSI BLOK 2 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(52,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(53,'JUNIOR TECHNICIAN PRODUKSI BLOK 2 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(54,'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 B','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(55,'TEAM LEADER PRODUKSI BLOK 2 C','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(56,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 C','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(57,'JUNIOR TECHNICIAN PRODUKSI BLOK 2 C','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(58,'TEAM LEADER PRODUKSI BLOK 2 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(59,'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(60,'JUNIOR TECHNICIAN PRODUKSI BLOK 2 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(61,'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 D','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(62,'ASMAN PEMELIHARAAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(63,'TEAM LEADER RENDAL PEMELIHARAAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(64,'OFFICER RENDAL PEMELIHARAAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(65,'JUNIOR OFFICER RENDAL PEMELIHARAAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(66,'TENAGA PENDUKUNG TEKNIK PERENCANAAN & PENGENDALIAN PEMELIHARAAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(67,'TEAM LEADER PEMELIHARAAN LISTRIK','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(68,'JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(69,'TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(70,'TEAM LEADER PEMELIHARAAN KONIN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(71,'JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(72,'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN KONTROL & INSTRUMEN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(73,'TEAM LEADER PEMELIHARAAN MESIN & SIPIL','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(74,'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(75,'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(76,'TEAM LEADER OUTAGE MANAGEMENT','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(77,'JUNIOR OFFICER OUTAGE MANAGEMENT','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(78,'TENAGA PENDUKUNG OUTAGE MANAGEMENT','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(79,'TEAM LEADER INVENTORY CONTROL & GUDANG','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(80,'JUNIOR OFFICER INVENTORI KONTROL & GUDANG','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(81,'TENAGA PENDUKUNG OFFICER INVENTORY & GUDANG','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(82,'ASMAN BUSINESS SUPPORT','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(83,'TEAM LEADER SDM, UMUM & CSR','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(84,'JUNIOR OFFICER SDM, UMUM & CSR','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(85,'TENAGA PENDUKUNG SDM & UMUM','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(86,'SEKERTARIS','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(87,'HOUSE KEEPER','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(88,'TEAM LEADER LOGISTIK & PENGADAAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(89,'JUNIOR OFFICER LOGISTIK & PENGADAAN','2026-09-29 04:18:33','2026-09-29 04:18:33'),
(90,'TENAGA PENDUKUNG OFFICER LOGISTIK & PENGADAAN','2026-09-29 04:18:34','2026-09-29 04:18:34'),
(91,'TEAM LEADER KEUANGAN','2026-09-29 04:18:34','2026-09-29 04:18:34'),
(92,'JUNIOR OFFICER KEUANGAN','2026-09-29 04:18:34','2026-09-29 04:18:34'),
(93,'TENAGA PENDUKUNG OFFICER KEUANGAN','2026-09-29 04:18:34','2026-09-29 04:18:34'),
(94,'KOORDINATOR MKP','2026-09-29 04:18:34','2026-09-29 04:18:34'),
(95,'KOORDINATOR NPS','2026-09-29 04:18:34','2026-09-29 04:18:34');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'MUP','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(2,'ASMAN','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(3,'TL','2026-09-29 03:08:09','2026-09-29 03:08:09'),
(4,'STAFF','2026-09-29 03:08:09','2026-09-29 03:08:09');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('CLGnOGs3gAktWqphkXepBWCAg2KxLzU7B0iTEczt',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWWJzWmRSbUdsc013TlhDdnhJUFBQRnY4aDBYTmRIcnE0MVQ0YllyVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO319',1791014206),
('dSq1I91kyiu6RmJjr4cjSGJ5KFxaFWVJ8ymWoBkf',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNlFERXV2MFdWS0I4M2xqaU94YldrT1Z0OW5JU1lncW4xd0c2VkN2VSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1MDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL21hc3Rlci1kYXRhP3BhZ2U9MiZ0YWI9dXNlcnMiO3M6NToicm91dGUiO3M6MTI6Im1hc3Rlci5pbmRleCI7fX0=',1790953565);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned DEFAULT NULL,
  `decision_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` bigint(20) unsigned NOT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `assigned_by` bigint(20) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `week_number` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Open',
  `completion_attachment` varchar(255) DEFAULT NULL,
  `completion_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_issue_id_foreign` (`issue_id`),
  KEY `tasks_assigned_to_foreign` (`assigned_to`),
  KEY `tasks_assigned_by_foreign` (`assigned_by`),
  KEY `tasks_decision_id_foreign` (`decision_id`),
  KEY `tasks_unit_id_index` (`unit_id`),
  CONSTRAINT `tasks_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_decision_id_foreign` FOREIGN KEY (`decision_id`) REFERENCES `decisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_no` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `position_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tl_id` bigint(20) unsigned DEFAULT NULL,
  `asman_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_employee_no_unique` (`employee_no`),
  KEY `users_position_id_foreign` (`position_id`),
  KEY `users_unit_id_foreign` (`unit_id`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_tl_id_foreign` (`tl_id`),
  KEY `users_asman_id_foreign` (`asman_id`),
  CONSTRAINT `users_asman_id_foreign` FOREIGN KEY (`asman_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_tl_id_foreign` FOREIGN KEY (`tl_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `organization_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'8108050JA','MOH. FURQON AKHSANI','mup@plnnp.co.id','081234567890','avatars/5tj6PITqUwg1tBHcd575BrlHOrKvfDvdpRM7kpDL.png',NULL,'$2y$12$kKnnfCqMaULgYb6GBNfeU.Tr96REpcOA6QEEIFibPCoJHQDXAmAW2',5,1,1,1,NULL,'2026-09-29 03:08:10','2026-09-29 08:09:31',NULL,NULL),
(2,'7323002PKW','H. MUHAMMAD ISMAIL','asman@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',26,2,2,1,NULL,'2026-09-29 03:08:10','2026-09-29 04:18:33',NULL,NULL),
(3,'8915056TD','MURSALAM','tl@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',32,11,3,1,NULL,'2026-09-29 03:08:10','2026-09-29 04:18:33',NULL,NULL),
(4,'PLN-004','Budi Santoso','budi@plnnp.co.id',NULL,NULL,NULL,'$2y$12$KOiW9TR0fBiIHHMD5DrM3uxUr6Z6pIoubuHiJSd1Uj5FVHX7dvMs.',4,3,4,1,NULL,'2026-09-29 03:08:10','2026-09-29 03:08:10',NULL,NULL),
(5,'PLN-005','Siti Rahma','siti@plnnp.co.id',NULL,NULL,NULL,'$2y$12$KOiW9TR0fBiIHHMD5DrM3uxUr6Z6pIoubuHiJSd1Uj5FVHX7dvMs.',4,3,4,1,NULL,'2026-09-29 03:08:10','2026-09-29 03:08:10',NULL,NULL),
(6,'PLN-006','Agus Kurniawan','agus@plnnp.co.id',NULL,NULL,NULL,'$2y$12$KOiW9TR0fBiIHHMD5DrM3uxUr6Z6pIoubuHiJSd1Uj5FVHX7dvMs.',4,3,4,1,NULL,'2026-09-29 03:08:10','2026-09-29 03:08:10',NULL,NULL),
(7,'PLN-007','Dewi Lestari','dewi@plnnp.co.id',NULL,NULL,NULL,'$2y$12$KOiW9TR0fBiIHHMD5DrM3uxUr6Z6pIoubuHiJSd1Uj5FVHX7dvMs.',4,3,4,1,NULL,'2026-09-29 03:08:10','2026-09-29 03:08:10',NULL,NULL),
(8,'PLN-008','Rizki Maulana','rizki@plnnp.co.id',NULL,NULL,NULL,'$2y$12$KOiW9TR0fBiIHHMD5DrM3uxUr6Z6pIoubuHiJSd1Uj5FVHX7dvMs.',4,3,4,1,NULL,'2026-09-29 03:08:10','2026-09-29 03:08:10',NULL,NULL),
(9,'PLN-009','Rian Saputra (STAFF)','staff@plnnp.co.id',NULL,NULL,NULL,'$2y$12$KOiW9TR0fBiIHHMD5DrM3uxUr6Z6pIoubuHiJSd1Uj5FVHX7dvMs.',4,3,4,1,NULL,'2026-09-29 03:08:10','2026-09-29 03:08:10',NULL,NULL),
(11,'9315231TA','YOGA KURNIAWAN','tl.k3@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',6,4,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(12,'8723023PKW','RUSDI ALFRIYANTO RIWALDI RAHMA','8723023pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',7,4,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(13,'9623022WGK','IRIANTI APRILLA','9623022wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',8,4,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(14,'8023020WGK','SYAFRUDDIN','8023020wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',9,4,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(15,'8424056WGK','BASO RIDWAN','8424056wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',9,4,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(16,'0525096WGK','MUH. AL AQSHAL KHIDAYATULLAH S.','0525096wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',9,4,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(17,'7223005PKW','H. ABDUL MALIK','7223005pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',10,5,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(18,'8123003WGK','MUH YUSUF','8123003wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',11,5,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(19,'7823006WGK','ISMAIL','7823006wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',11,5,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(20,'9624065WGK','NURUL FAHMIAH','9624065wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',12,5,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(21,'9825097WGK','BASO AWALUDDIN','9825097wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',11,5,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(22,'9825092WGK','ULVANI JULFIRA','9825092wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',11,5,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(23,'8609070JA','FUAD ANWAR','asman.engineering@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',13,7,2,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(24,'9417078BL','HARI AKBAR SYUAIB','9417078bl@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',14,25,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(25,'9017082BL','M TAUFIK HASRIANSYAH ASIARI','9017082bl@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',15,25,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(26,'8423028PKW','ANDI IMRAN MUSTIKA','8423028pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',16,25,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(27,'9824059WGK','IRFAN','9824059wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',17,25,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(28,'8312589KP','ARMAN ARIFIN','8312589kp@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',18,26,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(29,'9217026AM','DAUD MALOMO','9217026am@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',19,26,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(30,'9923039WGK','NUARDI','9923039wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',20,26,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(31,'8424086WGK','ABDUL RAHIM','8424086wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',21,26,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(32,'7423008PKW','AFFANDY HERMAWAN','7423008pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',22,27,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(33,'7123016PKW','H. NASRUL MARZUKI','7123016pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',23,27,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(34,'9523023WGK','TIARA SUHARYATI','9523023wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',24,27,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(35,'9924077WGK','HERI ANUGRAH PRATAMA','9924077wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',24,27,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(36,'9424069WGK','WAHYU HIDAYAT','9424069wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',25,27,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(37,'8812558PC','SINUNG RAHARJO','8812558pc@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',27,9,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(38,'9115083ID','MUH. ASPAR ARIFIN','9115083id@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',28,9,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(39,'0424049WGK','ALGAZALI','0424049wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',29,9,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(40,'9923028WGK','ANDI ANANDA MAGFIRAH','9923028wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',30,10,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(41,'9724066WGK','RIYAN ADRIANSYAH AMRI','9724066wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',30,10,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(42,'9925088WGK','BISMI RATU FORTUNA N','9925088wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',31,10,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(43,'0124068WGK','SITTI MUNAWARAH SYAM','0124068wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',31,10,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(44,'0324064WGK','MUHAMMAD ABDIL MUKMIN SYAH','0324064wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',31,10,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(45,'9123038PKW','SATRIAWAN','9123038pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',33,11,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(46,'9324067WGK','RUDI','9324067wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',34,11,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(47,'9723011WGK','ADRIAN','9723011wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',35,11,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(48,'8817083BL','MUSRIYADI','8817083bl@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',33,11,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(49,'8917074BL','MEDY IVANTRY TANDI BUA','8917074bl@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',36,12,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(50,'8515058TD','AHMAD BAYU PERMADI','8515058td@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',37,12,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(51,'9117084BL','ONESIMUS','9117084bl@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',37,12,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(52,'9923008WGK','MUH. ASHAR','9923008wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',38,12,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(53,'9824079WGK','MOCH. ALIF AFDILAH SALDI','9824079wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',39,12,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(54,'7423003PKW','H.HERMAN JAYA.ST','7423003pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',40,13,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(55,'7323020PKW','BERFRY','7323020pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',41,13,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(56,'9723031WGK','AYU','9723031wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',42,13,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(57,'9723015WGK','ANDI DEDI PRABOWO','9723015wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',43,13,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(58,'0224071WGK','FIRMAN','0224071wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',43,13,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(59,'7223009PKW','MUH NASAR D','7223009pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',44,14,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(60,'9023025PKW','ASRIANTO','9023025pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',45,14,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(61,'0023027WGK','ANDI ALIEM ADRIAN ASRUL','0023027wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',46,14,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(62,'9924063WGK','MUH. SOFYAN','9924063wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',47,14,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(63,'9023039PKW','MUH. TAKDIR T','9023039pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',48,15,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(64,'8915057TD','BAHARUDDIN','8915057td@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',48,15,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(65,'0125098WGK','MUHAMMAD FAJAR','0125098wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',49,15,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(66,'0423036WGK','MUH. AKBAR','0423036wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',50,15,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(67,'8223015PKW','NADAR','8223015pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',51,16,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(68,'8715065KD','ARIFUDDIN NURDIN','8715065kd@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',52,16,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(69,'8923031PKW','SURYA DARMAN SAPUTRA','8923031pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',52,16,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(70,'9924081WGK','MUH. HIDAYAT ARAS','9924081wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',53,16,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(71,'9323041WGK','RIAHASTAMAN','9323041wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',54,16,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(72,'0225089WGK','ALIF IQBAL MAULANA','0225089wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',54,16,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(73,'8323018PKW','AGUSNAWAN','8323018pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',55,17,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(74,'8423029PKW','ADY GUNA','8423029pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',56,17,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(75,'9123041PKW','NURJUMAEDI','9123041pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',56,17,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(76,'9723038WGK','MUKMIN','9723038wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',57,17,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(77,'8623017PKW','SUNARDI SM','8623017pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',58,18,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(78,'8223033PKW','ABD MUSAWWIR AMFAR','8223033pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',59,18,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(79,'8223032PKW','MOH FIQRY','8223032pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',59,18,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(80,'9924075WGK','ANUGRAH','9924075wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',60,18,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(81,'9223044WGK','SUPRIADI','9223044wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',61,18,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(82,'8208036JA','CAHYA PATRIA CITRA TARUNA','asman.pemeliharaan@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',62,6,2,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(83,'9015144ZJY','YERSON','9015144zjy@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',63,19,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(84,'9317123KD','AZWAR RASYID','9317123kd@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',64,19,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(85,'0023021WGK','FILLIA','0023021wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',65,19,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(86,'0023026WGK','NAFTALIA SAMPE RANDAN','0023026wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',65,19,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(87,'0424048WGK','AHMAD RESKY','0424048wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',66,19,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(88,'9115227KP','SUHAIMI','9115227kp@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',67,20,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(89,'7323030PKW','SUPRIYADI LAMMA','7323030pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',68,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(90,'8023024PKW','USMAN','8023024pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',68,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(91,'8023017WGK','WAHYUDDIN','8023017wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',69,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(92,'0124072WGK','ABDI RAHABUDDIN','0124072wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',68,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(93,'0124084WGK','ADITYA WIRATAMA PRAYOGA','0124084wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',68,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(94,'0124083WGK','SUTRISNO.BP, S.T','0124083wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',68,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(95,'9724074WGK','ANDI WAHYUDI WIJAYANTO','9724074wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',68,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(96,'9625094WGK','AGUS SALIM','9625094wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',69,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(97,'9424060WGK','IRFANDI','9424060wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',69,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(98,'9723034WGK','IRWANSYAH','9723034wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',69,20,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(99,'8323007PKW','HENDRA GULANG','8323007pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',70,21,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(100,'8723040PKW','DWI SUPAJAR MUCHTAR SIAJENG','8723040pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',71,21,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(101,'9923033WGK','FHARIS IRHAM MULLIAH','9923033wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',71,21,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(102,'9023035WGK','KAHARDIMAN','9023035wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',72,21,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(103,'9923043WGK','SAHRUL KHAN','9923043wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',72,21,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(104,'8524061WGK','JUMARDI','8524061wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',72,21,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(105,'0025099WGK','AHMAD REZKY NASRUDDIN','0025099wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',71,21,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(106,'7823006PKW','SAINAL TAJANG','7823006pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',73,22,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(107,'8423037PKW','ASRIYADI','8423037pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(108,'9014036ID','MELKI VANDI RANTE','9014036id@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(109,'8623027PKW','MUH. AKBAL','8623027pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(110,'9523010WGK','DEDI SETIADI','9523010wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(111,'9123016WGK','MASDAR','9123016wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',75,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(112,'8923013WGK','MUH.ARAFA','8923013wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',75,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(113,'9123012WGK','SADDAN HUSEN','9123012wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(114,'8023009WGK','SUDARTO','8023009wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',75,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(115,'9624053WGK','ARISMAN','9624053wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',75,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(116,'9123045WGK','YUSRAN JAYA','9123045wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',75,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(117,'0124078WGK','LUCKY EDY PRAKOSO SULASTIYO','0124078wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(118,'9924076WGK','BASO MUHAMMAD AGUNG ANUGERAH','9924076wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(119,'0124080WGK','MUH. ADRIANSYAH','0124080wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',74,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(120,'9424046WGK','A.M.ISHAQ HAQUE AB','9424046wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',75,22,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(121,'9015132ZJY','MUHAMMAD SIDDIQ B.','9015132zjy@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',76,23,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(122,'9216020KA','ASRIEL BASTIAD','9216020ka@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',77,23,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(123,'0023019WGK','PUTRI FATMASARI','0023019wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',77,23,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(124,'9623029WGK','ANDI RYAN ADRIAN','9623029wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',77,23,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(125,'9624050WGK','ANDI HIDAYAT','9624050wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',78,23,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(126,'8716092PC','NURHALIM','8716092pc@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',79,24,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(127,'8523042PKW','MUH. ARCANG','8523042pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',80,24,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(128,'8623018WGK','ANDI BASO MUNAWIR','8623018wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',80,24,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(129,'9923032WGK','FERDI AGUS','9923032wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',80,24,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(130,'0024047WGK','AFRENGKI SAPUTRA','0024047wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',81,24,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(131,'0224055WGK','ARWAN','0224055wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',81,24,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(132,'9724062WGK','MUH. IQBAL RAWANDI, S.KEL','9724062wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',81,24,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(133,'8713108ZJY','PRAN MOCH SOLEH','asman.bs@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',82,8,2,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(134,'9117374KP','ANDI RIDWANSYAH BAHAR PUTRA','9117374kp@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',83,28,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(135,'8923024WGK','ANDI AISYAH NINGRATI ASHARIANA','8923024wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',84,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(136,'7823025WGK','MURLINA','7823025wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',84,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(137,'0024054WGK','ARUN SATRIA PUTRA','0024054wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',85,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(138,'9624057WGK','DIRGA FAJAR MAULANA','9624057wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',85,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(139,'8524070WGK','YAZID RIZAL','8524070wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',85,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(140,'0124058WGK','HILDA RIANYSKA','0124058wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',86,29,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(141,'7423002WGK','ERNA S','7423002wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',87,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(142,'9123001WGK','HERNILA','9123001wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',87,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(143,'7923005WGK','NANNA TRIANA','7923005wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',87,28,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(144,'7823014PKW','AGUS SALIM','7823014pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',88,30,3,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(145,'9923042WGK','RIZAL HAMZAH','9923042wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',89,30,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(146,'9223040WGK','NURDEDI','9223040wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',89,30,4,1,NULL,'2026-09-29 04:18:33','2026-09-29 04:18:33',NULL,NULL),
(147,'0024051WGK','ANNISA MAULIANA AKBAR','0024051wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',89,30,4,1,NULL,'2026-09-29 04:18:34','2026-10-01 08:13:09',NULL,NULL),
(148,'0425095WGK','ANDI ALYA AJRIN ASRUL','0425095wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',90,30,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(149,'9925091WGK','MUHAMMAD RAYHAN SYAKIL','9925091wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',90,30,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(150,'0124087WGK','SANDRI KURNIAWAN','0124087wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',90,30,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(151,'8915104ZJY','ZUL QADRI A. SADIK','8915104zjy@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',91,31,3,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(152,'8923019PKW','NURDIANSAR ARFAH','8923019pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',92,31,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(153,'8623011PKW','WARDA','8623011pkw@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',92,31,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(154,'9923037WGK','MUHAMMAD ZAKIR KAMARUDDIN','9923037wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',93,31,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(155,'9625090WGK','MUHAMMAD ARFAN SANJAYA','9625090wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',93,31,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(156,'7523004WGK','ANSAR ANAS','7523004wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',94,8,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(157,'8814113KP','ANDIK YULIANTO','8814113kp@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',95,8,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL),
(158,'9124085WGK','NELLA SUSANTY PINERASMA','9124085wgk@plnnp.co.id',NULL,NULL,NULL,'$2y$12$kq4Y0EPZd3OyaYtPvGn4L.PuBxAf/PiOtyQE3OHarhSSZqgBwQ2WK',95,8,4,1,NULL,'2026-09-29 04:18:34','2026-09-29 04:18:34',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-10-03 15:25:07
