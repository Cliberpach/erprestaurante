
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `alerts_app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alerts_app` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `alerts_app` WRITE;
/*!40000 ALTER TABLE `alerts_app` DISABLE KEYS */;
/*!40000 ALTER TABLE `alerts_app` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `alerts_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alerts_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` bigint unsigned NOT NULL,
  `sale_id` bigint unsigned NOT NULL,
  `sale_serie` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matched_amount` decimal(16,6) NOT NULL,
  `observation` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('USADO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USADO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alerts_sales_alert_id_foreign` (`alert_id`),
  KEY `alerts_sales_sale_id_foreign` (`sale_id`),
  CONSTRAINT `alerts_sales_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alerts_app` (`id`),
  CONSTRAINT `alerts_sales_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `alerts_sales` WRITE;
/*!40000 ALTER TABLE `alerts_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `alerts_sales` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_id` bigint unsigned NOT NULL COMMENT 'Banco ID',
  `bank_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del banco',
  `bank_abbreviation` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sigla del banco',
  `account_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de cuenta',
  `cci` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CCI',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Celular',
  `holder` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Titular',
  `currency` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Moneda',
  `qr_url` longtext COLLATE utf8mb4_unicode_ci,
  `qr_name` longtext COLLATE utf8mb4_unicode_ci,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL COMMENT 'Usuario que elimina',
  `delete_user_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre del usuario que elimina',
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO' COMMENT 'Estado',
  `editable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Editable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bank_accounts` WRITE;
/*!40000 ALTER TABLE `bank_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `bank_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `bank_id` bigint unsigned NOT NULL,
  `account_holder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `itf` decimal(15,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_companies_company_id_foreign` (`company_id`),
  KEY `bank_companies_bank_id_foreign` (`bank_id`),
  CONSTRAINT `bank_companies_bank_id_foreign` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`),
  CONSTRAINT `bank_companies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bank_companies` WRITE;
/*!40000 ALTER TABLE `bank_companies` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_companies` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `banks` WRITE;
/*!40000 ALTER TABLE `banks` DISABLE KEYS */;
/*!40000 ALTER TABLE `banks` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `billing_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `billing_companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `document_type_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `starting_number` int unsigned NOT NULL DEFAULT '1',
  `initiated` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `billing_companies_company_id_foreign` (`company_id`),
  KEY `billing_companies_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `billing_companies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `billing_companies_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `billing_companies` WRITE;
/*!40000 ALTER TABLE `billing_companies` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_companies` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `booking_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment` double(8,2) NOT NULL DEFAULT '0.00',
  `voucher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_detail_booking_id_foreign` (`booking_id`),
  CONSTRAINT `booking_detail_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `booking_detail` WRITE;
/*!40000 ALTER TABLE `booking_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_detail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned NOT NULL,
  `schedule_id` bigint unsigned NOT NULL,
  `reservation_document_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `total` double(8,2) NOT NULL,
  `modality` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_route` longtext COLLATE utf8mb4_unicode_ci,
  `ball` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica si se pidió balón',
  `vest` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica si se pidió chaleco',
  `dni` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica si se dejó dni',
  `nro_hours` decimal(10,2) unsigned NOT NULL COMMENT 'N° horas alquiler',
  `payment_status` enum('SIN_PAGO','PARCIAL','TOTAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('LIBRE','RESERVADO','ALQUILADO','ADICIONAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_credit` tinyint(1) NOT NULL DEFAULT '0',
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_type_document_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type_document_id` bigint unsigned NOT NULL,
  `sale_document_serie` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_document_id` bigint unsigned DEFAULT NULL,
  `facturado` enum('SI','NO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO' COMMENT 'Indica si una reserva fue facturada',
  `start_time` time NOT NULL COMMENT 'Hora inicio',
  `end_time` time NOT NULL COMMENT 'Hora fin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_field_id_foreign` (`field_id`),
  KEY `bookings_schedule_id_foreign` (`schedule_id`),
  KEY `bookings_reservation_document_id_foreign` (`reservation_document_id`),
  CONSTRAINT `bookings_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`),
  CONSTRAINT `bookings_reservation_document_id_foreign` FOREIGN KEY (`reservation_document_id`) REFERENCES `reservation_documents` (`id`),
  CONSTRAINT `bookings_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `bookings_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings_schedules` (
  `booking_id` bigint unsigned NOT NULL,
  `schedule_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`booking_id`,`schedule_id`),
  KEY `bookings_schedules_schedule_id_foreign` (`schedule_id`),
  CONSTRAINT `bookings_schedules_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  CONSTRAINT `bookings_schedules_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bookings_schedules` WRITE;
/*!40000 ALTER TABLE `bookings_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings_schedules` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'CATEGORIA','INACTIVE',0,'2026-05-12 19:22:55','2026-05-12 19:22:55');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `collaborators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collaborators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_type_id` bigint unsigned NOT NULL,
  `position_id` bigint unsigned NOT NULL,
  `document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(260) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_days` decimal(20,2) unsigned NOT NULL,
  `rest_days` decimal(20,2) unsigned NOT NULL,
  `monthly_salary` decimal(10,2) unsigned NOT NULL,
  `daily_salary` decimal(10,6) unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collaborators_document_number_unique` (`document_number`),
  KEY `collaborators_position_id_foreign` (`position_id`),
  CONSTRAINT `collaborators_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `collaborators` WRITE;
/*!40000 ALTER TABLE `collaborators` DISABLE KEYS */;
INSERT INTO `collaborators` VALUES (1,1,1,'77412431','DNI','LUIS DANIEL ALVA LUJÁN','AV HUSARES 123','999999999',30.00,20.00,9999.00,100.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:00','2026-05-12 19:23:00'),(2,1,2,'74571390','DNI','CAJERO 1','DIRECCION DEMO','994704968',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:05','2026-05-12 19:23:05'),(3,1,3,'71061619','DNI','MESERO 1','DIRECCION DEMO','967429576',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:06','2026-05-12 19:23:06'),(4,1,3,'78647975','DNI','MESERO 2','DIRECCION DEMO','979564671',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:07','2026-05-12 19:23:07'),(5,1,3,'70882021','DNI','MESERO 3','DIRECCION DEMO','971161434',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:07','2026-05-12 19:23:07'),(6,1,3,'76481266','DNI','MESERO 4','DIRECCION DEMO','955656289',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:09','2026-05-12 19:23:09'),(7,1,3,'70796299','DNI','MESERO 5','DIRECCION DEMO','990516261',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:09','2026-05-12 19:23:09'),(8,1,3,'73471853','DNI','MESERO 6','DIRECCION DEMO','936074744',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:10','2026-05-12 19:23:10'),(9,1,3,'75993596','DNI','MESERO 7','DIRECCION DEMO','979397307',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:10','2026-05-12 19:23:10'),(10,1,3,'72963770','DNI','MESERO 8','DIRECCION DEMO','933427362',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:10','2026-05-12 19:23:10'),(11,1,3,'73362913','DNI','MESERO 9','DIRECCION DEMO','969205315',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:11','2026-05-12 19:23:11'),(12,1,3,'74711205','DNI','MESERO 10','DIRECCION DEMO','962697591',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:11','2026-05-12 19:23:11'),(13,1,3,'78680768','DNI','MESERO 11','DIRECCION DEMO','920025842',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:12','2026-05-12 19:23:12'),(14,1,3,'70803457','DNI','MESERO 12','DIRECCION DEMO','953692618',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:12','2026-05-12 19:23:12'),(15,1,3,'79772133','DNI','MESERO 13','DIRECCION DEMO','983753667',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(16,1,3,'76012526','DNI','MESERO 14','DIRECCION DEMO','980961532',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(17,1,3,'79884947','DNI','MESERO 15','DIRECCION DEMO','922531462',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(18,1,3,'71307479','DNI','MESERO 16','DIRECCION DEMO','984608576',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(19,1,3,'77706811','DNI','MESERO 17','DIRECCION DEMO','967683856',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(20,1,3,'76846999','DNI','MESERO 18','DIRECCION DEMO','976628018',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(21,1,3,'73675248','DNI','MESERO 19','DIRECCION DEMO','996801873',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(22,1,3,'78816369','DNI','MESERO 20','DIRECCION DEMO','982587092',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(23,1,4,'77761963','DNI','CONTADOR','DIRECCION DEMO','966425828',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(24,1,5,'70330808','DNI','COCINERO','DIRECCION DEMO','916943513',30.00,20.00,1500.00,50.000000,'ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:23:15','2026-05-12 19:23:15');
/*!40000 ALTER TABLE `collaborators` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ruc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviated_business_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files_route` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base64_logo` longtext COLLATE utf8mb4_unicode_ci,
  `lat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lng` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fiscal_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cellphone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoicing_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `igv` decimal(10,4) unsigned NOT NULL DEFAULT '10.5000',
  `block_account` tinyint(1) NOT NULL DEFAULT '0',
  `plan` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_ruc_unique` (`ruc`),
  UNIQUE KEY `companies_business_name_unique` (`business_name`),
  UNIQUE KEY `companies_abbreviated_business_name_unique` (`abbreviated_business_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'10182260211','ALVARADO TICLIA ANA MARIA','MAMA JUANITA','mamajuanita','mamajuanita_44','44',NULL,NULL,NULL,NULL,NULL,'AV. LARCO 249',NULL,NULL,NULL,'admin@gmail.com',NULL,NULL,NULL,'0','1',10.5000,0,'3','2026-05-12 19:22:59','2026-05-12 19:22:59');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `company_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned DEFAULT NULL,
  `plan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `environment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'DEMO',
  `token_code` longtext COLLATE utf8mb4_unicode_ci,
  `invoice_id` bigint DEFAULT NULL,
  `department_id` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_id` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` char(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubigeo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urbanization` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_user` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_password` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_user_gre` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_password_gre` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_password` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('ACTIVE','CANCELED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_invoices_company_id_foreign` (`company_id`),
  CONSTRAINT `company_invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `company_invoices` WRITE;
/*!40000 ALTER TABLE `company_invoices` DISABLE KEYS */;
INSERT INTO `company_invoices` VALUES (1,1,'3','BETA',NULL,NULL,'13','1301','130111','LA LIBERTAD','TRUJILLO','VICTOR LARCO HERRERA','130111','PALERMO','0000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ACTIVE','2026-05-12 19:22:59','2026-05-12 19:22:59');
/*!40000 ALTER TABLE `company_invoices` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuration` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `configuration` WRITE;
/*!40000 ALTER TABLE `configuration` DISABLE KEYS */;
INSERT INTO `configuration` VALUES (1,'Ambiente Facturación','BETA',1,'2026-05-12 19:22:53','2026-05-12 19:22:53'),(2,'Contraseña eliminar','123456789',1,'2026-05-12 19:22:53','2026-05-12 19:22:53');
/*!40000 ALTER TABLE `configuration` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumable_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consumable_brands_creator_user_id_foreign` (`creator_user_id`),
  KEY `consumable_brands_editor_user_id_foreign` (`editor_user_id`),
  KEY `consumable_brands_deletor_user_id_foreign` (`deletor_user_id`),
  CONSTRAINT `consumable_brands_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_brands_deletor_user_id_foreign` FOREIGN KEY (`deletor_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_brands_editor_user_id_foreign` FOREIGN KEY (`editor_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumable_brands` WRITE;
/*!40000 ALTER TABLE `consumable_brands` DISABLE KEYS */;
INSERT INTO `consumable_brands` VALUES (1,'MARCA','ANULADO',0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53'),(2,'GENERICO','ACTIVO',0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53');
/*!40000 ALTER TABLE `consumable_brands` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumable_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consumable_categories_creator_user_id_foreign` (`creator_user_id`),
  KEY `consumable_categories_editor_user_id_foreign` (`editor_user_id`),
  KEY `consumable_categories_deletor_user_id_foreign` (`deletor_user_id`),
  CONSTRAINT `consumable_categories_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_categories_deletor_user_id_foreign` FOREIGN KEY (`deletor_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_categories_editor_user_id_foreign` FOREIGN KEY (`editor_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumable_categories` WRITE;
/*!40000 ALTER TABLE `consumable_categories` DISABLE KEYS */;
INSERT INTO `consumable_categories` VALUES (1,'CATEGORIA','ANULADO',0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53'),(2,'GENERICO','ACTIVO',1,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53');
/*!40000 ALTER TABLE `consumable_categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumable_income_note_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_income_note_details` (
  `consumable_income_note_id` bigint unsigned NOT NULL,
  `consumable_id` bigint unsigned NOT NULL,
  `consumable_brand_id` bigint unsigned NOT NULL,
  `consumable_category_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `unit_symbol` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumable_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumable_brand_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumable_category_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`consumable_income_note_id`,`consumable_id`),
  KEY `consumable_income_note_details_consumable_id_foreign` (`consumable_id`),
  KEY `consumable_income_note_details_consumable_brand_id_foreign` (`consumable_brand_id`),
  KEY `consumable_income_note_details_consumable_category_id_foreign` (`consumable_category_id`),
  KEY `consumable_income_note_details_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `consumable_income_note_details_consumable_brand_id_foreign` FOREIGN KEY (`consumable_brand_id`) REFERENCES `consumable_brands` (`id`),
  CONSTRAINT `consumable_income_note_details_consumable_category_id_foreign` FOREIGN KEY (`consumable_category_id`) REFERENCES `consumable_categories` (`id`),
  CONSTRAINT `consumable_income_note_details_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`),
  CONSTRAINT `consumable_income_note_details_consumable_income_note_id_foreign` FOREIGN KEY (`consumable_income_note_id`) REFERENCES `consumable_income_notes` (`id`),
  CONSTRAINT `consumable_income_note_details_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumable_income_note_details` WRITE;
/*!40000 ALTER TABLE `consumable_income_note_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumable_income_note_details` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumable_income_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_income_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_user_id` bigint unsigned NOT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consumable_income_notes_warehouse_id_foreign` (`warehouse_id`),
  KEY `consumable_income_notes_creator_user_id_foreign` (`creator_user_id`),
  CONSTRAINT `consumable_income_notes_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_income_notes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumable_income_notes` WRITE;
/*!40000 ALTER TABLE `consumable_income_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumable_income_notes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumable_kardex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_kardex` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned DEFAULT NULL,
  `purchase_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note_income_id` bigint unsigned DEFAULT NULL,
  `note_income_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note_release_id` bigint unsigned DEFAULT NULL,
  `note_release_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('ENTRADA','SALIDA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_serie` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumable_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `unit_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_symbol` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumable_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) unsigned NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `creator_user_id` bigint unsigned NOT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consumable_kardex_purchase_id_foreign` (`purchase_id`),
  KEY `consumable_kardex_note_income_id_foreign` (`note_income_id`),
  KEY `consumable_kardex_note_release_id_foreign` (`note_release_id`),
  KEY `consumable_kardex_warehouse_id_foreign` (`warehouse_id`),
  KEY `consumable_kardex_consumable_id_foreign` (`consumable_id`),
  KEY `consumable_kardex_category_id_foreign` (`category_id`),
  KEY `consumable_kardex_brand_id_foreign` (`brand_id`),
  KEY `consumable_kardex_creator_user_id_foreign` (`creator_user_id`),
  CONSTRAINT `consumable_kardex_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `consumable_brands` (`id`),
  CONSTRAINT `consumable_kardex_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `consumable_categories` (`id`),
  CONSTRAINT `consumable_kardex_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`),
  CONSTRAINT `consumable_kardex_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_kardex_note_income_id_foreign` FOREIGN KEY (`note_income_id`) REFERENCES `consumable_income_notes` (`id`),
  CONSTRAINT `consumable_kardex_note_release_id_foreign` FOREIGN KEY (`note_release_id`) REFERENCES `notes_release` (`id`),
  CONSTRAINT `consumable_kardex_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `consumable_purchases` (`id`),
  CONSTRAINT `consumable_kardex_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumable_kardex` WRITE;
/*!40000 ALTER TABLE `consumable_kardex` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumable_kardex` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumable_purchase_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_purchase_details` (
  `purchase_id` bigint unsigned NOT NULL,
  `consumable_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumable_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_symbol` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `purchase_price` decimal(10,2) unsigned NOT NULL,
  `subtotal` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`purchase_id`,`consumable_id`),
  KEY `consumable_purchase_details_consumable_id_foreign` (`consumable_id`),
  KEY `consumable_purchase_details_category_id_foreign` (`category_id`),
  KEY `consumable_purchase_details_brand_id_foreign` (`brand_id`),
  KEY `consumable_purchase_details_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `consumable_purchase_details_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `consumable_brands` (`id`),
  CONSTRAINT `consumable_purchase_details_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `consumable_categories` (`id`),
  CONSTRAINT `consumable_purchase_details_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`),
  CONSTRAINT `consumable_purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `consumable_purchases` (`id`),
  CONSTRAINT `consumable_purchase_details_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumable_purchase_details` WRITE;
/*!40000 ALTER TABLE `consumable_purchase_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumable_purchase_details` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumable_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `delivery_date` date NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `supplier_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_type_document_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_center_id` bigint unsigned NOT NULL,
  `cost_center_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serie` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correlative` int unsigned NOT NULL,
  `observation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prices_with_igv` tinyint unsigned NOT NULL,
  `igv` decimal(16,4) unsigned NOT NULL,
  `subtotal` decimal(16,4) unsigned NOT NULL,
  `amount_igv` decimal(16,4) unsigned NOT NULL,
  `total` decimal(16,4) unsigned NOT NULL,
  `discount_cash` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `payment_condition_id` bigint unsigned NOT NULL,
  `payment_condition_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_condition_days` int unsigned NOT NULL,
  `payment_status` enum('PAGADO','PENDIENTE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `registration_date` date NOT NULL,
  `expiration_date` date NOT NULL,
  `creator_user_id` bigint unsigned NOT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consumable_purchases_warehouse_id_foreign` (`warehouse_id`),
  KEY `consumable_purchases_supplier_id_foreign` (`supplier_id`),
  KEY `consumable_purchases_cost_center_id_foreign` (`cost_center_id`),
  KEY `consumable_purchases_payment_condition_id_foreign` (`payment_condition_id`),
  KEY `consumable_purchases_creator_user_id_foreign` (`creator_user_id`),
  KEY `consumable_purchases_editor_user_id_foreign` (`editor_user_id`),
  KEY `consumable_purchases_deletor_user_id_foreign` (`deletor_user_id`),
  CONSTRAINT `consumable_purchases_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_center` (`id`),
  CONSTRAINT `consumable_purchases_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_purchases_deletor_user_id_foreign` FOREIGN KEY (`deletor_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_purchases_editor_user_id_foreign` FOREIGN KEY (`editor_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumable_purchases_payment_condition_id_foreign` FOREIGN KEY (`payment_condition_id`) REFERENCES `payment_conditions` (`id`),
  CONSTRAINT `consumable_purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `consumable_purchases_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumable_purchases` WRITE;
/*!40000 ALTER TABLE `consumable_purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumable_purchases` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `consumables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `stock_min` int NOT NULL,
  `code_factory` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_bar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_route` longtext COLLATE utf8mb4_unicode_ci,
  `img_name` longtext COLLATE utf8mb4_unicode_ci,
  `unit_id` bigint unsigned DEFAULT NULL,
  `unit_symbol` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consumables_category_id_foreign` (`category_id`),
  KEY `consumables_brand_id_foreign` (`brand_id`),
  KEY `consumables_creator_user_id_foreign` (`creator_user_id`),
  KEY `consumables_editor_user_id_foreign` (`editor_user_id`),
  KEY `consumables_deletor_user_id_foreign` (`deletor_user_id`),
  CONSTRAINT `consumables_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `consumable_brands` (`id`),
  CONSTRAINT `consumables_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `consumable_categories` (`id`),
  CONSTRAINT `consumables_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumables_deletor_user_id_foreign` FOREIGN KEY (`deletor_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consumables_editor_user_id_foreign` FOREIGN KEY (`editor_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `consumables` WRITE;
/*!40000 ALTER TABLE `consumables` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumables` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cost_center`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cost_center` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cost_center` WRITE;
/*!40000 ALTER TABLE `cost_center` DISABLE KEYS */;
INSERT INTO `cost_center` VALUES (1,'COMPRAS','ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:59','2026-05-12 19:22:59'),(2,'DEVOLUCIÓN','ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:59','2026-05-12 19:22:59');
/*!40000 ALTER TABLE `cost_center` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `credit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `petty_cash_id` bigint unsigned NOT NULL,
  `petty_cash_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `petty_cash_book_id` bigint unsigned NOT NULL,
  `type_doc_affected` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_doc_affected` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_motive` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_motive` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_money` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_sale_id` bigint unsigned NOT NULL COMMENT 'DE LA VENTA AFECTADA',
  `type_sale_code` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'DE LA VENTA AFECTADA',
  `type_sale_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'DE LA VENTA AFECTADA',
  `type_invoice_id` bigint unsigned NOT NULL,
  `type_invoice_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_invoice_code` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type_document` enum('DNI','RUC') COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `igv_percentage` decimal(16,6) unsigned NOT NULL,
  `subtotal` decimal(16,6) unsigned NOT NULL,
  `igv_amount` decimal(16,6) unsigned NOT NULL,
  `total` decimal(16,6) unsigned NOT NULL,
  `legend` varchar(260) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mto_oper_taxed` decimal(15,2) unsigned NOT NULL,
  `mto_igv` decimal(15,2) unsigned NOT NULL,
  `total_taxes` decimal(15,2) unsigned NOT NULL,
  `mto_imp_sale` decimal(15,2) unsigned NOT NULL,
  `ubl_version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2.1',
  `correlative` int unsigned NOT NULL,
  `serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sunat_status` enum('ACEPTADO','PENDIENTE','ENVIADO','RECHAZADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `response_cdrZip` tinyint DEFAULT NULL,
  `response_success` tinyint DEFAULT NULL,
  `response_error_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_error_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_notes` longtext COLLATE utf8mb4_unicode_ci,
  `cdr_response_reference` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_cdr` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_xml` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_qr` longtext COLLATE utf8mb4_unicode_ci,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_send_message` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_notes_sale_id_foreign` (`sale_id`),
  KEY `credit_notes_petty_cash_id_foreign` (`petty_cash_id`),
  KEY `credit_notes_petty_cash_book_id_foreign` (`petty_cash_book_id`),
  KEY `credit_notes_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `credit_notes_petty_cash_book_id_foreign` FOREIGN KEY (`petty_cash_book_id`) REFERENCES `petty_cash_books` (`id`),
  CONSTRAINT `credit_notes_petty_cash_id_foreign` FOREIGN KEY (`petty_cash_id`) REFERENCES `petty_cashes` (`id`),
  CONSTRAINT `credit_notes_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `credit_notes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `credit_notes` WRITE;
/*!40000 ALTER TABLE `credit_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_notes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `credit_notes_dishes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes_dishes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `credit_note_id` bigint unsigned NOT NULL,
  `dish_id` bigint unsigned NOT NULL,
  `dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_dish_id` bigint unsigned NOT NULL,
  `type_dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `programming_id` bigint unsigned NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(16,6) NOT NULL,
  `observation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mto_valor_unitario` decimal(16,6) NOT NULL,
  `mto_valor_venta` decimal(16,6) NOT NULL,
  `mto_base_igv` decimal(16,6) NOT NULL,
  `porcentaje_igv` decimal(16,6) NOT NULL,
  `igv` decimal(16,6) NOT NULL,
  `tip_afe_igv` bigint unsigned NOT NULL,
  `total_impuestos` decimal(16,6) NOT NULL,
  `mto_precio_unitario` decimal(16,6) NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_notes_dishes_credit_note_id_foreign` (`credit_note_id`),
  KEY `credit_notes_dishes_dish_id_foreign` (`dish_id`),
  CONSTRAINT `credit_notes_dishes_credit_note_id_foreign` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_notes` (`id`),
  CONSTRAINT `credit_notes_dishes_dish_id_foreign` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `credit_notes_dishes` WRITE;
/*!40000 ALTER TABLE `credit_notes_dishes` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_notes_dishes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `credit_notes_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `credit_note_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(16,6) NOT NULL,
  `observation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mto_valor_unitario` decimal(16,6) NOT NULL,
  `mto_valor_venta` decimal(16,6) NOT NULL,
  `mto_base_igv` decimal(16,6) NOT NULL,
  `porcentaje_igv` decimal(16,6) NOT NULL,
  `igv` decimal(16,6) NOT NULL,
  `tip_afe_igv` bigint unsigned NOT NULL,
  `total_impuestos` decimal(16,6) NOT NULL,
  `mto_precio_unitario` decimal(16,6) NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_notes_products_credit_note_id_foreign` (`credit_note_id`),
  KEY `credit_notes_products_product_id_foreign` (`product_id`),
  CONSTRAINT `credit_notes_products_credit_note_id_foreign` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_notes` (`id`),
  CONSTRAINT `credit_notes_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `credit_notes_products` WRITE;
/*!40000 ALTER TABLE `credit_notes_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_notes_products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned NOT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_hours` int NOT NULL,
  `ball` tinyint NOT NULL DEFAULT '0',
  `vest` tinyint NOT NULL DEFAULT '0',
  `dni` tinyint NOT NULL DEFAULT '0',
  `ruc_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `estado` enum('PENDIENTE','PAGADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `facturado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `credits_booking_id_foreign` (`booking_id`),
  KEY `credits_field_id_foreign` (`field_id`),
  CONSTRAINT `credits_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credits_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `credits` WRITE;
/*!40000 ALTER TABLE `credits` DISABLE KEYS */;
/*!40000 ALTER TABLE `credits` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `customer_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `agreement` text COLLATE utf8mb4_unicode_ci,
  `balance` decimal(8,2) unsigned NOT NULL,
  `status` enum('PENDIENTE','PAGADO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_order_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_accounts_sale_id_foreign` (`sale_id`),
  KEY `customer_accounts_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `customer_accounts_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `customer_accounts_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `customer_accounts` WRITE;
/*!40000 ALTER TABLE `customer_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_accounts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `customer_accounts_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_accounts_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_account_id` bigint unsigned NOT NULL,
  `petty_cash_book_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci,
  `img_route` longtext COLLATE utf8mb4_unicode_ci,
  `img_name` longtext COLLATE utf8mb4_unicode_ci,
  `payment_method_id` bigint unsigned NOT NULL,
  `cash` decimal(15,6) unsigned DEFAULT '0.000000',
  `amount` decimal(16,6) unsigned DEFAULT '0.000000',
  `balance` decimal(16,6) unsigned DEFAULT NULL,
  `total` decimal(16,6) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_accounts_details_customer_account_id_foreign` (`customer_account_id`),
  KEY `customer_accounts_details_petty_cash_book_id_foreign` (`petty_cash_book_id`),
  KEY `customer_accounts_details_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `customer_accounts_details_customer_account_id_foreign` FOREIGN KEY (`customer_account_id`) REFERENCES `customer_accounts` (`id`),
  CONSTRAINT `customer_accounts_details_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `customer_accounts_details_petty_cash_book_id_foreign` FOREIGN KEY (`petty_cash_book_id`) REFERENCES `petty_cash_books` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `customer_accounts_details` WRITE;
/*!40000 ALTER TABLE `customer_accounts_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_accounts_details` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `dish_consumables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dish_consumables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dish_id` bigint unsigned NOT NULL,
  `consumable_id` bigint unsigned NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dish_consumables_dish_id_consumable_id_unique` (`dish_id`,`consumable_id`),
  KEY `dish_consumables_consumable_id_foreign` (`consumable_id`),
  CONSTRAINT `dish_consumables_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`),
  CONSTRAINT `dish_consumables_dish_id_foreign` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `dish_consumables` WRITE;
/*!40000 ALTER TABLE `dish_consumables` DISABLE KEYS */;
/*!40000 ALTER TABLE `dish_consumables` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `dishes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dishes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_dish_id` bigint unsigned DEFAULT NULL,
  `sale_price` decimal(16,6) unsigned DEFAULT NULL,
  `purchase_price` decimal(16,6) unsigned DEFAULT NULL,
  `img_route` longtext COLLATE utf8mb4_unicode_ci,
  `img_name` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dishes_type_dish_id_foreign` (`type_dish_id`),
  CONSTRAINT `dishes_type_dish_id_foreign` FOREIGN KEY (`type_dish_id`) REFERENCES `types_dish` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `dishes` WRITE;
/*!40000 ALTER TABLE `dishes` DISABLE KEYS */;
/*!40000 ALTER TABLE `dishes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `document_serializations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_serializations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `document_type_id` bigint unsigned NOT NULL,
  `serie` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_limit` int NOT NULL,
  `destiny` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `final_number` int NOT NULL,
  `initiated` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_serializations_company_id_foreign` (`company_id`),
  CONSTRAINT `document_serializations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `document_serializations` WRITE;
/*!40000 ALTER TABLE `document_serializations` DISABLE KEYS */;
INSERT INTO `document_serializations` VALUES (1,1,67,'TK01',8,'TICKET','NO','TICKET','1',0,'NO','2026-05-12 19:23:03',NULL),(2,1,65,'B001',8,'BOLETA ELECTRÓNICA','NO','BOLETA ELECTRÓNICA','1',0,'NO','2026-05-12 19:23:03',NULL),(3,1,66,'F001',8,'FACTURA ELECTRÓNICA','NO','FACTURA ELECTRÓNICA','1',0,'NO','2026-05-12 19:23:03',NULL);
/*!40000 ALTER TABLE `document_serializations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prefix_serie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destiny` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `document_types` WRITE;
/*!40000 ALTER TABLE `document_types` DISABLE KEYS */;
INSERT INTO `document_types` VALUES (1,'FACTURA ELECTRÓNICA','FT','01','F',NULL,1,'2026-05-12 19:22:55','2026-05-12 19:22:55'),(3,'BOLETA DE VENTA ELECTRÓNICA','BV','03','B',NULL,1,'2026-05-12 19:22:55','2026-05-12 19:22:55'),(6,'NOTA DE CRÉDITO BOLETA','NCB','07','BB',NULL,1,'2026-05-12 19:22:55','2026-05-12 19:22:55'),(7,'NOTA DE CRÉDITO FACTURA','NCF','07','FF',NULL,1,'2026-05-12 19:22:55','2026-05-12 19:22:55'),(8,'NOTA DE DÉBITO','ND','08','ND',NULL,1,'2026-05-12 19:22:55','2026-05-12 19:22:55'),(9,'GUIA DE REMISIÓN REMITENTE','GRE','09','T',NULL,1,'2026-05-12 19:22:55','2026-05-12 19:22:55'),(20,'COMPROBANTE DE RETENCIÓN ELECTRÓNICA','CRE','20',NULL,NULL,1,'2026-05-12 19:22:55','2026-05-12 19:22:55'),(50,'TICKET','T','50',NULL,NULL,1,'2026-05-12 19:22:56','2026-05-12 19:22:56'),(51,'TICKET DE COMPRA','TC','51',NULL,NULL,1,'2026-05-12 19:22:56','2026-05-12 19:22:56'),(52,'NOTA DE INGRESO','NI','52',NULL,NULL,1,'2026-05-12 19:22:56','2026-05-12 19:22:56'),(53,'NOTA DE SALIDA','NS','53',NULL,NULL,1,'2026-05-12 19:22:56','2026-05-12 19:22:56'),(76,'NOTA DE ENTRADA','NE','76',NULL,NULL,1,'2026-05-12 19:22:56','2026-05-12 19:22:56'),(80,'NOTA DE VENTA','NV','80','NV',NULL,1,'2026-05-12 19:22:56','2026-05-12 19:22:56');
/*!40000 ALTER TABLE `document_types` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `exit_money`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exit_money` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proof_payment_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `cost_center_id` bigint unsigned NOT NULL,
  `cost_center_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` double NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `petty_cash_book_id` bigint unsigned NOT NULL,
  `payment_method_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_cash` tinyint(1) NOT NULL DEFAULT '0',
  `consumable_purchase_id` bigint unsigned DEFAULT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exit_money_proof_payment_id_foreign` (`proof_payment_id`),
  KEY `exit_money_supplier_id_foreign` (`supplier_id`),
  KEY `exit_money_user_id_foreign` (`user_id`),
  KEY `exit_money_cost_center_id_foreign` (`cost_center_id`),
  KEY `exit_money_consumable_purchase_id_foreign` (`consumable_purchase_id`),
  CONSTRAINT `exit_money_consumable_purchase_id_foreign` FOREIGN KEY (`consumable_purchase_id`) REFERENCES `consumable_purchases` (`id`),
  CONSTRAINT `exit_money_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_center` (`id`),
  CONSTRAINT `exit_money_proof_payment_id_foreign` FOREIGN KEY (`proof_payment_id`) REFERENCES `proof_payments` (`id`),
  CONSTRAINT `exit_money_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `exit_money_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `exit_money` WRITE;
/*!40000 ALTER TABLE `exit_money` DISABLE KEYS */;
/*!40000 ALTER TABLE `exit_money` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `exit_money_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exit_money_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exit_money_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exit_money_detail_exit_money_id_foreign` (`exit_money_id`),
  CONSTRAINT `exit_money_detail_exit_money_id_foreign` FOREIGN KEY (`exit_money_id`) REFERENCES `exit_money` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `exit_money_detail` WRITE;
/*!40000 ALTER TABLE `exit_money_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `exit_money_detail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type_field_id` bigint unsigned NOT NULL,
  `field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('LIBRE','RESERVADO','ALQUILADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LIBRE',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `day_price` decimal(16,6) unsigned NOT NULL,
  `night_price` decimal(16,6) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fields_type_field_id_foreign` (`type_field_id`),
  CONSTRAINT `fields_type_field_id_foreign` FOREIGN KEY (`type_field_id`) REFERENCES `type_fields` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `fields` WRITE;
/*!40000 ALTER TABLE `fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `fields` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `kardex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kardex` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned DEFAULT NULL,
  `order_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_id` bigint unsigned DEFAULT NULL,
  `sale_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_id` bigint unsigned DEFAULT NULL,
  `purchase_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note_income_id` bigint unsigned DEFAULT NULL,
  `note_income_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note_release_id` bigint unsigned DEFAULT NULL,
  `note_release_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note_credit_id` bigint unsigned DEFAULT NULL,
  `note_credit_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('ENTRADA','SALIDA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_serie` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `product_unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) unsigned NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_type_document_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(16,6) unsigned DEFAULT NULL,
  `subtotal` decimal(16,6) unsigned DEFAULT NULL,
  `igv` decimal(16,6) unsigned DEFAULT NULL,
  `creator_user_id` bigint unsigned NOT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kardex_order_id_foreign` (`order_id`),
  KEY `kardex_sale_id_foreign` (`sale_id`),
  KEY `kardex_purchase_id_foreign` (`purchase_id`),
  KEY `kardex_note_income_id_foreign` (`note_income_id`),
  KEY `kardex_note_release_id_foreign` (`note_release_id`),
  KEY `kardex_note_credit_id_foreign` (`note_credit_id`),
  KEY `kardex_warehouse_id_foreign` (`warehouse_id`),
  KEY `kardex_product_id_foreign` (`product_id`),
  KEY `kardex_category_id_foreign` (`category_id`),
  KEY `kardex_brand_id_foreign` (`brand_id`),
  CONSTRAINT `kardex_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `kardex_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `kardex_note_credit_id_foreign` FOREIGN KEY (`note_credit_id`) REFERENCES `credit_notes` (`id`),
  CONSTRAINT `kardex_note_income_id_foreign` FOREIGN KEY (`note_income_id`) REFERENCES `notes_income` (`id`),
  CONSTRAINT `kardex_note_release_id_foreign` FOREIGN KEY (`note_release_id`) REFERENCES `notes_release` (`id`),
  CONSTRAINT `kardex_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `kardex_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `kardex_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchase_documents` (`id`),
  CONSTRAINT `kardex_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `kardex_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `kardex` WRITE;
/*!40000 ALTER TABLE `kardex` DISABLE KEYS */;
/*!40000 ALTER TABLE `kardex` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_11_000000_create_positions_table',1),(2,'2014_10_11_000001_create_collaborators_table',1),(3,'2014_10_12_000000_create_users_table',1),(4,'2014_10_12_100000_create_password_reset_tokens_table',1),(5,'2014_10_12_200000_add_two_factor_columns_to_users_table',1),(6,'2019_12_14_000001_create_personal_access_tokens_table',1),(7,'2023_09_26_134702_create_sessions_table',1),(8,'2023_09_26_140135_create_permission_tables',1),(9,'2023_09_29_010753_create_petty_cashes_table',1),(10,'2023_09_30_020404_create_payment_methods_table',1),(11,'2023_09_30_020405_create_companies_table',1),(12,'2023_09_30_020406_create_bank_accounts_table',1),(13,'2023_09_30_151615_create_company_invoices_table',1),(14,'2023_09_30_153214_create_modules_table',1),(15,'2023_10_02_143241_create_shifts_table',1),(16,'2023_10_02_143242_create_petty_cash_books_table',1),(17,'2023_10_02_183038_create_module_children_table',1),(18,'2023_10_02_183046_create_module_grand_children_table',1),(19,'2023_10_02_183207_create_module_children_companies_table',1),(20,'2023_10_03_135826_create_banks_table',1),(21,'2023_10_03_135841_create_bank_companies_table',1),(22,'2023_10_06_160944_create_categories_table',1),(23,'2023_10_06_175551_create_brands_table',1),(24,'2023_10_06_175628_create_products_table',1),(25,'2023_10_06_210952_create_document_types_table',1),(26,'2023_10_06_211207_create_document_serializations_table',1),(27,'2023_10_11_074926_create_plans_table',1),(28,'2023_10_11_211208_create_vehicles_table',1),(29,'2023_10_11_211209_create_services_table',1),(30,'2023_10_16_150533_create_alerts_app_table',1),(31,'2024_01_18_200313_create_proof_payments_table',1),(32,'2024_01_18_200359_create_suppliers_table',1),(33,'2024_01_18_204643_create_cost_center_table',1),(34,'2024_01_20_110957_create_type_fields_table',1),(35,'2024_01_20_111051_create_fields_table',1),(36,'2024_01_20_131230_create_schedules_table',1),(37,'2024_01_20_131231_create_reservation_documents_table',1),(38,'2024_01_20_131232_create_reservation_documents_detail_table',1),(39,'2024_01_20_131233_create_bookings_table',1),(40,'2024_01_20_132816_create_booking_detail_table',1),(41,'2024_01_20_132817_create_bookings_schedules_table',1),(42,'2024_10_27_113119_create_warehouses_table',1),(43,'2024_10_27_113417_create_warehouse_products_table',1),(44,'2024_10_27_165730_create_types_dish_table',1),(45,'2024_10_27_165731_create_dishes_table',1),(46,'2024_10_27_165736_create_payment_conditions_table',1),(47,'2024_10_27_165737_create_sales_table',1),(48,'2024_10_27_165738_create_sales_products_table',1),(49,'2024_10_27_165739_create_sales_dishes_table',1),(50,'2024_10_27_165740_create_sales_pays_table',1),(51,'2024_10_27_181927_create_billing_companies_table',1),(52,'2024_11_19_174916_create_notes_income_table',1),(53,'2024_11_19_174917_create_notes_income_detail_table',1),(54,'2024_11_27_181539_create_notes_release_table',1),(55,'2024_11_27_181540_create_notes_release_detail_table',1),(56,'2024_11_29_113306_create_purchase_documents_table',1),(57,'2024_11_29_114011_create_purchase_documents_detail_table',1),(58,'2024_12_03_150602_create_configuration_table',1),(59,'2025_03_20_110448_create_credits_table',1),(60,'2025_07_01_102543_create_quotes_table',1),(61,'2025_07_01_102544_create_quotes_products_table',1),(62,'2025_07_01_102545_create_quotes_services_table',1),(63,'2025_07_01_102546_create_work_orders_table',1),(64,'2025_07_01_102547_create_work_orders_inventory_table',1),(65,'2025_07_01_102548_create_work_orders_technicians_table',1),(66,'2025_07_01_102549_create_work_orders_images_table',1),(67,'2025_07_01_102550_create_work_orders_products_table',1),(68,'2025_07_01_102551_create_work_orders_services_table',1),(69,'2025_07_01_102552_create_customer_accounts_table',1),(70,'2025_07_01_102553_create_customer_accounts_details_table',1),(71,'2025_07_01_102554_create_payment_method_account_table',1),(72,'2025_07_01_102555_create_tables_table',1),(73,'2025_07_01_102558_create_programming_table',1),(74,'2025_07_01_102559_create_programming_detail_table',1),(75,'2025_07_01_102560_create_petty_cash_servers_table',1),(76,'2025_07_01_102561_create_orders_table',1),(77,'2025_07_01_102562_create_orders_dishes_table',1),(78,'2025_07_01_102563_create_orders_products_table',1),(79,'2025_07_01_102564_create_reservations_table',1),(80,'2025_07_01_102565_create_credit_notes_table',1),(81,'2025_07_01_102566_create_credit_notes_products_table',1),(82,'2025_07_01_102567_create_credit_notes_dishes_table',1),(83,'2025_07_01_102568_create_kardex_table',1),(84,'2025_07_01_102569_create_calcular_stock_function',1),(85,'2025_07_01_102570_create_sp_kardex_procedure',1),(86,'2025_07_01_102571_create_supplier_accounts_table',1),(87,'2025_07_01_102572_create_supplier_accounts_details_table',1),(88,'2026_03_04_150802_create_alerts_sales_table',1),(89,'2026_03_12_174645_create_consumable_categories_table',1),(90,'2026_03_12_174646_create_consumable_brands_table',1),(91,'2026_03_12_185605_create_consumables_table',1),(92,'2026_03_13_114841_create_warehouse_consumables_table',1),(93,'2026_03_13_152247_create_consumable_income_notes_table',1),(94,'2026_03_13_152414_create_consumable_income_note_details_table',1),(95,'2026_03_13_164158_create_consumable_purchases_table',1),(96,'2026_03_13_164211_create_consumable_purchase_details_table',1),(97,'2026_03_13_164212_create_consumable_kardex_table',1),(98,'2026_03_16_184115_create_fn_stock_consumable',1),(99,'2026_03_16_184241_create_sp_kardex_consumable',1),(100,'2026_03_16_184242_create_exit_money_table',1),(101,'2026_03_16_184243_create_exit_money_detail_table',1),(102,'2026_03_18_144522_create_dish_consumables_table',1),(103,'2026_03_24_123625_create_summaries_table',1),(104,'2026_03_24_123635_create_summaries_details_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\Tenant\\User',1),(3,'App\\Models\\Tenant\\User',2),(2,'App\\Models\\Tenant\\User',3),(2,'App\\Models\\Tenant\\User',4),(2,'App\\Models\\Tenant\\User',5),(2,'App\\Models\\Tenant\\User',6),(2,'App\\Models\\Tenant\\User',7),(2,'App\\Models\\Tenant\\User',8),(2,'App\\Models\\Tenant\\User',9),(2,'App\\Models\\Tenant\\User',10),(2,'App\\Models\\Tenant\\User',11),(2,'App\\Models\\Tenant\\User',12),(2,'App\\Models\\Tenant\\User',13),(2,'App\\Models\\Tenant\\User',14),(2,'App\\Models\\Tenant\\User',15),(2,'App\\Models\\Tenant\\User',16),(2,'App\\Models\\Tenant\\User',17),(2,'App\\Models\\Tenant\\User',18),(2,'App\\Models\\Tenant\\User',19),(2,'App\\Models\\Tenant\\User',20),(2,'App\\Models\\Tenant\\User',21),(2,'App\\Models\\Tenant\\User',22),(5,'App\\Models\\Tenant\\User',23),(4,'App\\Models\\Tenant\\User',24);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `module_children`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_children` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint unsigned NOT NULL,
  `description` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL,
  `show` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'tenant',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_children_module_id_foreign` (`module_id`),
  CONSTRAINT `module_children_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `module_children` WRITE;
/*!40000 ALTER TABLE `module_children` DISABLE KEYS */;
INSERT INTO `module_children` VALUES (1,1,'Caja','cajas.cajas.index',2,'tenant','2026-05-12 19:23:04',NULL),(2,1,'Apertura/Cierre','cajas.apertura_cierre.index',2,'tenant','2026-05-12 19:23:04',NULL),(3,1,'Egresos','cajas.egresos.index',2,'tenant','2026-05-12 19:23:04',NULL),(4,2,'Comprobante Venta','ventas.comprobante_venta.index',2,'tenant','2026-05-12 19:23:04',NULL),(5,2,'Clientes','ventas.clientes.index',2,'tenant','2026-05-12 19:23:04',NULL),(6,2,'Notas Crédito','ventas.notas_credito.index',2,'tenant','2026-05-12 19:23:04',NULL),(7,2,'Métodos Pago','ventas.metodos_pago.index',2,'tenant','2026-05-12 19:23:04',NULL),(8,3,'Mesas','abastecimiento.mesas.index',2,'tenant','2026-05-12 19:23:04',NULL),(9,3,'Tipo Plato','abastecimiento.tipos_plato.index',2,'tenant','2026-05-12 19:23:04',NULL),(10,3,'Platos','abastecimiento.platos.index',2,'tenant','2026-05-12 19:23:04',NULL),(11,3,'Programación','abastecimiento.programacion.index',2,'tenant','2026-05-12 19:23:04',NULL),(12,4,'Categorías','inventario.categorias.index',2,'tenant','2026-05-12 19:23:04',NULL),(13,4,'Marcas','inventario.marcas.index',2,'tenant','2026-05-12 19:23:04',NULL),(14,4,'Productos','inventario.productos.index',2,'tenant','2026-05-12 19:23:04',NULL),(15,4,'Kardex','inventario.kardex.index',2,'tenant','2026-05-12 19:23:04',NULL),(16,4,'Inventario','inventario.inventario.index',2,'tenant','2026-05-12 19:23:04',NULL),(17,4,'Kardex Valorizado','inventario.kardex_valorizado.index',2,'tenant','2026-05-12 19:23:04',NULL),(18,4,'Nota Ingreso','inventario.nota_ingreso.index',2,'tenant','2026-05-12 19:23:04',NULL),(19,4,'Nota Salida','inventario.nota_salida.index',2,'tenant','2026-05-12 19:23:04',NULL),(20,5,'Mostrador','mostrador_mesero.mostrador.index',2,'tenant','2026-05-12 19:23:04',NULL),(21,6,'Mostrador','mostrador_cajero.mostrador.index',2,'tenant','2026-05-12 19:23:04',NULL),(22,7,'Proveedores','compras.proveedores.index',2,'tenant','2026-05-12 19:23:04',NULL),(23,7,'Documento de Compra','compras.documento_compra.index',2,'tenant','2026-05-12 19:23:04',NULL),(24,8,'Ventas Productos','reportes.ventas_productos.index',2,'tenant','2026-05-12 19:23:04',NULL),(25,8,'Ventas Platos','reportes.ventas_platos.index',2,'tenant','2026-05-12 19:23:04',NULL),(26,8,'Reporte de Venta','reportes.ventas.index',2,'tenant','2026-05-12 19:23:04',NULL),(27,8,'Reporte Contable','reportes.contable.index',2,'tenant','2026-05-12 19:23:04',NULL),(28,9,'Empresa','mantenimiento.empresas.index',2,'tenant','2026-05-12 19:23:04',NULL),(29,9,'Cuentas','mantenimiento.cuentas.index',2,'tenant','2026-05-12 19:23:04',NULL),(30,9,'Cargos','mantenimiento.cargos.index',2,'tenant','2026-05-12 19:23:04',NULL),(31,9,'Colaboradores','mantenimiento.colaboradores.index',2,'tenant','2026-05-12 19:23:04',NULL),(32,9,'Usuarios','mantenimiento.usuario.index',2,'tenant','2026-05-12 19:23:04',NULL),(33,9,'Roles','mantenimiento.roles.index',2,'tenant','2026-05-12 19:23:04',NULL),(34,9,'Configuración','mantenimiento.configuracion.index',2,'tenant','2026-05-12 19:23:04',NULL),(35,10,'Créditos','consultas.creditos.index',2,'tenant','2026-05-12 19:23:04',NULL),(36,11,'Cuentas Proveedor','cuentas.proveedor.index',2,'tenant','2026-05-12 19:23:04',NULL),(37,11,'Condiciones Pago','ventas.condiciones_pago.index',2,'tenant','2026-05-12 19:23:04',NULL),(38,12,'Dashboard','dashboard.dashboard.index',2,'tenant','2026-05-12 19:23:04',NULL),(39,13,'Insumos','insumos.insumos.index',2,'tenant','2026-05-12 19:23:04',NULL),(40,13,'Categorias','insumos.categorias.index',2,'tenant','2026-05-12 19:23:04',NULL),(41,13,'Marcas','insumos.marcas.index',2,'tenant','2026-05-12 19:23:04',NULL),(42,13,'Compras','insumos.compras.index',2,'tenant','2026-05-12 19:23:04',NULL),(48,13,'Kardex','insumos.kardex.index',2,'tenant','2026-05-12 19:23:04',NULL);
/*!40000 ALTER TABLE `module_children` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `module_children_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_children_companies` (
  `module_id` bigint unsigned NOT NULL,
  `module_child_id` bigint unsigned NOT NULL,
  `module_grand_child_id` bigint unsigned DEFAULT NULL,
  `company_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`module_id`,`module_child_id`,`company_id`),
  KEY `module_children_companies_module_child_id_foreign` (`module_child_id`),
  KEY `module_children_companies_module_grand_child_id_foreign` (`module_grand_child_id`),
  KEY `module_children_companies_company_id_foreign` (`company_id`),
  CONSTRAINT `module_children_companies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `module_children_companies_module_child_id_foreign` FOREIGN KEY (`module_child_id`) REFERENCES `module_children` (`id`) ON DELETE CASCADE,
  CONSTRAINT `module_children_companies_module_grand_child_id_foreign` FOREIGN KEY (`module_grand_child_id`) REFERENCES `module_grand_children` (`id`) ON DELETE CASCADE,
  CONSTRAINT `module_children_companies_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `module_children_companies` WRITE;
/*!40000 ALTER TABLE `module_children_companies` DISABLE KEYS */;
/*!40000 ALTER TABLE `module_children_companies` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `module_grand_children`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_grand_children` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_child_id` bigint unsigned NOT NULL,
  `description` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL,
  `show` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'tenant',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_grand_children_module_child_id_foreign` (`module_child_id`),
  CONSTRAINT `module_grand_children_module_child_id_foreign` FOREIGN KEY (`module_child_id`) REFERENCES `module_children` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `module_grand_children` WRITE;
/*!40000 ALTER TABLE `module_grand_children` DISABLE KEYS */;
/*!40000 ALTER TABLE `module_grand_children` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL,
  `show` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'tenant',
  `render_order` bigint unsigned NOT NULL,
  `icon` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_render_order_index` (`render_order`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,'Cajas',1,'tenant',2,'cash-register-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(2,'Ventas',1,'tenant',3,'invoice-receipt-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(3,'Abastecimiento',1,'tenant',4,'japanese-food-rice-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(4,'Inventario',1,'tenant',5,'warehouse-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(5,'Mostrador Mesero',1,'tenant',6,'waiter-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(6,'Mostrador Cajero',1,'tenant',7,'reception-hotel-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(7,'Compras',1,'tenant',8,'shopping-cart-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(8,'Reportes',1,'tenant',10,'analytics-financial-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(9,'Mantenimiento',1,'tenant',11,'configuration-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(10,'Consultas',1,'tenant',12,'analytics-report-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(11,'Cuentas',1,'tenant',13,'credit-card-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(12,'Dashboard',1,'tenant',1,'finances-pie-chart-svgrepo-com.svg','2026-05-12 19:23:03',NULL),(13,'Insumos',1,'tenant',9,'insumos.svg','2026-05-12 19:23:03',NULL);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notes_income`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes_income` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_recorder_id` bigint unsigned NOT NULL,
  `user_recorder_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notes_income_user_recorder_id_foreign` (`user_recorder_id`),
  CONSTRAINT `notes_income_user_recorder_id_foreign` FOREIGN KEY (`user_recorder_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `notes_income` WRITE;
/*!40000 ALTER TABLE `notes_income` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes_income` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notes_income_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes_income_detail` (
  `note_income_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`note_income_id`,`product_id`),
  KEY `notes_income_detail_product_id_foreign` (`product_id`),
  KEY `notes_income_detail_brand_id_foreign` (`brand_id`),
  KEY `notes_income_detail_category_id_foreign` (`category_id`),
  KEY `notes_income_detail_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `notes_income_detail_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `notes_income_detail_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `notes_income_detail_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `notes_income_detail_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `notes_income_detail` WRITE;
/*!40000 ALTER TABLE `notes_income_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes_income_detail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notes_release`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes_release` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_recorder_id` bigint unsigned NOT NULL,
  `user_recorder_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notes_release_user_recorder_id_foreign` (`user_recorder_id`),
  CONSTRAINT `notes_release_user_recorder_id_foreign` FOREIGN KEY (`user_recorder_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `notes_release` WRITE;
/*!40000 ALTER TABLE `notes_release` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes_release` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notes_release_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes_release_detail` (
  `note_release_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`note_release_id`,`product_id`),
  KEY `notes_release_detail_product_id_foreign` (`product_id`),
  KEY `notes_release_detail_brand_id_foreign` (`brand_id`),
  KEY `notes_release_detail_category_id_foreign` (`category_id`),
  KEY `notes_release_detail_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `notes_release_detail_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `notes_release_detail_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `notes_release_detail_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `notes_release_detail_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `notes_release_detail` WRITE;
/*!40000 ALTER TABLE `notes_release_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes_release_detail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type_document_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `petty_cash_id` bigint unsigned NOT NULL,
  `petty_cash_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `petty_cash_book_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LOCAL',
  `observation` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_attempts_dishes` int unsigned DEFAULT NULL,
  `n_attempts_products` int unsigned DEFAULT NULL,
  `igv_percentage` decimal(16,6) unsigned NOT NULL,
  `total` decimal(16,6) unsigned NOT NULL,
  `subtotal` decimal(16,6) unsigned NOT NULL,
  `igv` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVO','FINALIZADO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `payref_id` bigint unsigned DEFAULT NULL COMMENT 'ID de la referencia de pago (payref)',
  `payref_name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre de la referencia de pago (payref)',
  `payref_img_url` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Imagen de la referencia de pago (payref)',
  `payref_img_name` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Nombre de la imagen de la referencia de pago (payref)',
  `payref_user_id` bigint unsigned DEFAULT NULL COMMENT 'Usuario que agrega pago referencia',
  `payref_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre usuario que agrega pago referencia',
  `payref_date` datetime DEFAULT NULL COMMENT 'Fecha en la que se agrega pago referencia',
  `cashier_id` bigint unsigned DEFAULT NULL COMMENT 'Cajero que cobra el pedido',
  `cashier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre cajero que cobra el pedido',
  `cashier_date` datetime DEFAULT NULL COMMENT 'Fecha cobro de pedido',
  `pending_print` enum('SI','NO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO',
  `pending_order_printing` enum('SI','NO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO',
  `order_print_mode` enum('TODO','PARCIAL','PLATO','BEBIDA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TODO',
  `waiter_delete_status` tinyint(1) DEFAULT '0',
  `waiter_delete_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waiter_delete_user_id` int unsigned DEFAULT NULL,
  `waiter_delete_at` datetime DEFAULT NULL,
  `waiter_delete_observation` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_invoice` enum('FACTURADO','NO FACTURADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO FACTURADO',
  `sale_id` bigint unsigned DEFAULT NULL,
  `sale_serie` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_correlative` int unsigned DEFAULT NULL,
  `cashier_delete_status` tinyint(1) DEFAULT '0',
  `cashier_delete_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cashier_delete_user_id` int unsigned DEFAULT NULL,
  `cashier_delete_at` datetime DEFAULT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_pending_print` datetime DEFAULT NULL,
  `date_pending_order_print` datetime DEFAULT NULL,
  `date_change_table` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_code_unique` (`code`),
  KEY `orders_table_id_foreign` (`table_id`),
  KEY `orders_petty_cash_id_foreign` (`petty_cash_id`),
  KEY `orders_petty_cash_book_id_foreign` (`petty_cash_book_id`),
  KEY `orders_payref_id_foreign` (`payref_id`),
  KEY `orders_payref_user_id_foreign` (`payref_user_id`),
  KEY `orders_cashier_id_foreign` (`cashier_id`),
  CONSTRAINT `orders_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_payref_id_foreign` FOREIGN KEY (`payref_id`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `orders_payref_user_id_foreign` FOREIGN KEY (`payref_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_petty_cash_book_id_foreign` FOREIGN KEY (`petty_cash_book_id`) REFERENCES `petty_cash_books` (`id`),
  CONSTRAINT `orders_petty_cash_id_foreign` FOREIGN KEY (`petty_cash_id`) REFERENCES `petty_cashes` (`id`),
  CONSTRAINT `orders_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `orders_dishes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_dishes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `dish_id` bigint unsigned NOT NULL,
  `dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_dish_id` bigint unsigned NOT NULL,
  `type_dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `programming_id` bigint unsigned NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(16,6) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `delete_status` tinyint(1) NOT NULL DEFAULT '0',
  `cancellation_date` datetime DEFAULT NULL,
  `observation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `print_status` enum('IMPRESO','SIN_IMPRIMIR') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SIN_IMPRIMIR',
  `print_delivery_status` enum('CREADO','ENTREGADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CREADO',
  `detail_printed` enum('SI','NO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_dishes_dish_id_foreign` (`dish_id`),
  KEY `orders_dishes_order_id_index` (`order_id`),
  CONSTRAINT `orders_dishes_dish_id_foreign` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`),
  CONSTRAINT `orders_dishes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `orders_dishes` WRITE;
/*!40000 ALTER TABLE `orders_dishes` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders_dishes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `orders_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(16,6) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `delete_status` tinyint(1) NOT NULL DEFAULT '0',
  `cancellation_date` datetime DEFAULT NULL,
  `observation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `print_status` enum('IMPRESO','SIN_IMPRIMIR') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SIN_IMPRIMIR',
  `print_delivery_status` enum('CREADO','ENTREGADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CREADO',
  `detail_printed` enum('SI','NO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_products_product_id_foreign` (`product_id`),
  KEY `orders_products_order_id_index` (`order_id`),
  CONSTRAINT `orders_products_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `orders_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `orders_products` WRITE;
/*!40000 ALTER TABLE `orders_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders_products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payment_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_conditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_days` int unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payment_conditions` WRITE;
/*!40000 ALTER TABLE `payment_conditions` DISABLE KEYS */;
INSERT INTO `payment_conditions` VALUES (1,'CONTADO','CONTADO',0,'ACTIVO',0,'2026-05-12 19:22:58','2026-05-12 19:22:58'),(2,'CREDITO','CREDITO',10,'ACTIVO',1,'2026-05-12 19:22:58','2026-05-12 19:22:58'),(3,'CREDITO','CREDITO',20,'ACTIVO',1,'2026-05-12 19:22:58','2026-05-12 19:22:58');
/*!40000 ALTER TABLE `payment_conditions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payment_method_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_method_accounts` (
  `payment_method_id` bigint unsigned NOT NULL,
  `bank_account_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'CUENTA ACTIVA PARA QR MOZO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_method_id`,`bank_account_id`),
  KEY `payment_method_accounts_bank_account_id_foreign` (`bank_account_id`),
  CONSTRAINT `payment_method_accounts_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`),
  CONSTRAINT `payment_method_accounts_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payment_method_accounts` WRITE;
/*!40000 ALTER TABLE `payment_method_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_method_accounts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,'EFECTIVO','ACTIVO','2026-05-12 19:22:53','2026-05-12 19:22:53'),(2,'YAPE','ACTIVO','2026-05-12 19:22:53','2026-05-12 19:22:53'),(3,'PLIN','ACTIVO','2026-05-12 19:22:53','2026-05-12 19:22:53'),(4,'POS','ACTIVO','2026-05-12 19:22:53','2026-05-12 19:22:53');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'cajas.cajas.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(2,'cajas.apertura_cierre.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(3,'cajas.egresos.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(4,'ventas.comprobante_venta.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(5,'ventas.clientes.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(6,'ventas.notas_credito.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(7,'ventas.metodos_pago.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(8,'abastecimiento.mesas.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(9,'abastecimiento.tipos_plato.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(10,'abastecimiento.platos.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(11,'abastecimiento.programacion.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(12,'inventario.categorias.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(13,'inventario.marcas.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(14,'inventario.productos.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(15,'inventario.kardex.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(16,'inventario.inventario.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(17,'inventario.kardex_valorizado.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(18,'inventario.nota_ingreso.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(19,'inventario.nota_salida.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(20,'mostrador_mesero.mostrador.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(21,'mostrador_cajero.mostrador.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(22,'compras.proveedores.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(23,'compras.documento_compra.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(24,'reportes.ventas_productos.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(25,'reportes.ventas_platos.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(26,'reportes.ventas.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(27,'reportes.contable.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(28,'mantenimiento.empresas.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(29,'mantenimiento.cuentas.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(30,'mantenimiento.cargos.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(31,'mantenimiento.colaboradores.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(32,'mantenimiento.usuario.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(33,'mantenimiento.roles.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(34,'mantenimiento.configuracion.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(35,'consultas.creditos.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(36,'ventas.condiciones_pago.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(37,'dashboard.dashboard.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(38,'cuentas.cliente.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(39,'cuentas.proveedor.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00'),(40,'ventas.resumenes.index','web','ACTIVO','2026-05-12 19:23:00','2026-05-12 19:23:00');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `petty_cash_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `petty_cash_books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `petty_cash_id` bigint unsigned NOT NULL,
  `shift_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `petty_cash_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ANULADO','ABIERTO','CERRADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ABIERTO',
  `type` enum('CAJA','FICTICIO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CAJA',
  `initial_amount` decimal(10,2) NOT NULL,
  `closing_amount` decimal(10,2) DEFAULT NULL,
  `initial_date` datetime NOT NULL,
  `final_date` datetime DEFAULT NULL,
  `sale_day` decimal(10,2) DEFAULT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `petty_cash_books_petty_cash_id_foreign` (`petty_cash_id`),
  KEY `petty_cash_books_shift_id_foreign` (`shift_id`),
  KEY `petty_cash_books_user_id_foreign` (`user_id`),
  CONSTRAINT `petty_cash_books_petty_cash_id_foreign` FOREIGN KEY (`petty_cash_id`) REFERENCES `petty_cashes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `petty_cash_books_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`),
  CONSTRAINT `petty_cash_books_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `petty_cash_books` WRITE;
/*!40000 ALTER TABLE `petty_cash_books` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash_books` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `petty_cash_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `petty_cash_servers` (
  `petty_cash_book_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL COMMENT 'Mesero',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`petty_cash_book_id`,`user_id`),
  KEY `petty_cash_servers_user_id_foreign` (`user_id`),
  CONSTRAINT `petty_cash_servers_petty_cash_book_id_foreign` FOREIGN KEY (`petty_cash_book_id`) REFERENCES `petty_cash_books` (`id`),
  CONSTRAINT `petty_cash_servers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `petty_cash_servers` WRITE;
/*!40000 ALTER TABLE `petty_cash_servers` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash_servers` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `petty_cashes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `petty_cashes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ABIERTO','ANULADO','CERRADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CERRADO',
  `type` enum('CAJA','FICTICIO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CAJA',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `petty_cashes` WRITE;
/*!40000 ALTER TABLE `petty_cashes` DISABLE KEYS */;
INSERT INTO `petty_cashes` VALUES (1,'CAJA PRINCIPAL','CERRADO','CAJA',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:56','2026-05-12 19:22:56');
/*!40000 ALTER TABLE `petty_cashes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_fields` int NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES (3,'PLAN FULL',9999,300.00,'2026-05-12 19:23:05','2026-05-12 19:23:05');
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'admin','ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53'),(2,'MESERO','ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53'),(3,'CAJERO','ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53'),(4,'COCINERO','ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53'),(5,'CONTADOR','ACTIVO',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-12 19:22:53','2026-05-12 19:22:53');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `stock_min` int NOT NULL,
  `code_factory` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_bar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_route` longtext COLLATE utf8mb4_unicode_ci,
  `img_name` longtext COLLATE utf8mb4_unicode_ci,
  `unit_id` bigint unsigned DEFAULT NULL,
  `unit_symbol` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `programming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programming` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `petty_cash_book_id` bigint unsigned DEFAULT NULL,
  `petty_cash_id` bigint unsigned DEFAULT NULL,
  `petty_cash_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `quantity_dishes` decimal(16,6) unsigned NOT NULL,
  `total` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO','CERRADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `closer_user_id` bigint unsigned DEFAULT NULL,
  `closer_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `programming_petty_cash_book_id_foreign` (`petty_cash_book_id`),
  KEY `programming_petty_cash_id_foreign` (`petty_cash_id`),
  KEY `programming_user_id_foreign` (`user_id`),
  CONSTRAINT `programming_petty_cash_book_id_foreign` FOREIGN KEY (`petty_cash_book_id`) REFERENCES `petty_cash_books` (`id`),
  CONSTRAINT `programming_petty_cash_id_foreign` FOREIGN KEY (`petty_cash_id`) REFERENCES `petty_cashes` (`id`),
  CONSTRAINT `programming_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `programming` WRITE;
/*!40000 ALTER TABLE `programming` DISABLE KEYS */;
/*!40000 ALTER TABLE `programming` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `programming_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programming_detail` (
  `programming_id` bigint unsigned NOT NULL,
  `dish_id` bigint unsigned NOT NULL,
  `dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(16,6) unsigned NOT NULL,
  `stock` decimal(16,6) unsigned NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`programming_id`,`dish_id`),
  KEY `programming_detail_dish_id_foreign` (`dish_id`),
  CONSTRAINT `programming_detail_dish_id_foreign` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`),
  CONSTRAINT `programming_detail_programming_id_foreign` FOREIGN KEY (`programming_id`) REFERENCES `programming` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `programming_detail` WRITE;
/*!40000 ALTER TABLE `programming_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `programming_detail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `proof_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proof_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `proof_payments` WRITE;
/*!40000 ALTER TABLE `proof_payments` DISABLE KEYS */;
INSERT INTO `proof_payments` VALUES (1,'BOLETA ELECTRÓNICA','2026-05-12 19:22:57','2026-05-12 19:22:57'),(2,'FACTURA ELECTRÓNICA','2026-05-12 19:22:57','2026-05-12 19:22:57'),(3,'PLANILLA','2026-05-12 19:22:58','2026-05-12 19:22:58'),(4,'MENÚ','2026-05-12 19:22:58','2026-05-12 19:22:58'),(5,'OTROS','2026-05-12 19:22:58','2026-05-12 19:22:58');
/*!40000 ALTER TABLE `proof_payments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `purchase_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `delivery_date` date NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `supplier_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_type_document_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_center_id` bigint unsigned NOT NULL,
  `cost_center_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serie` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correlative` int unsigned NOT NULL,
  `observation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prices_with_igv` tinyint unsigned NOT NULL,
  `igv` decimal(16,4) unsigned NOT NULL,
  `subtotal` decimal(16,4) unsigned NOT NULL,
  `amount_igv` decimal(16,4) unsigned NOT NULL,
  `total` decimal(16,4) unsigned NOT NULL,
  `discount_cash` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `payment_condition_id` bigint unsigned NOT NULL,
  `payment_condition_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_condition_days` int unsigned NOT NULL,
  `payment_status` enum('PAGADO','PENDIENTE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `registration_date` date NOT NULL,
  `expiration_date` date NOT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_documents_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_documents_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_documents_cost_center_id_foreign` (`cost_center_id`),
  KEY `purchase_documents_payment_condition_id_foreign` (`payment_condition_id`),
  CONSTRAINT `purchase_documents_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_center` (`id`),
  CONSTRAINT `purchase_documents_payment_condition_id_foreign` FOREIGN KEY (`payment_condition_id`) REFERENCES `payment_conditions` (`id`),
  CONSTRAINT `purchase_documents_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_documents_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `purchase_documents` WRITE;
/*!40000 ALTER TABLE `purchase_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_documents` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `purchase_documents_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_documents_detail` (
  `purchase_document_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `purchase_price` decimal(10,2) unsigned NOT NULL,
  `subtotal` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`purchase_document_id`,`product_id`),
  KEY `purchase_documents_detail_product_id_foreign` (`product_id`),
  KEY `purchase_documents_detail_category_id_foreign` (`category_id`),
  KEY `purchase_documents_detail_brand_id_foreign` (`brand_id`),
  KEY `purchase_documents_detail_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `purchase_documents_detail_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `purchase_documents_detail_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `purchase_documents_detail_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `purchase_documents_detail_purchase_document_id_foreign` FOREIGN KEY (`purchase_document_id`) REFERENCES `purchase_documents` (`id`),
  CONSTRAINT `purchase_documents_detail_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `purchase_documents_detail` WRITE;
/*!40000 ALTER TABLE `purchase_documents_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_documents_detail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expiration_date` date DEFAULT NULL,
  `days_validity` int unsigned NOT NULL DEFAULT '0',
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type_document_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `plate` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(16,6) unsigned NOT NULL,
  `subtotal` decimal(16,6) unsigned NOT NULL,
  `igv` decimal(16,6) unsigned NOT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVO','ANULADO','CONVERTIDO','EXPIRADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotes_warehouse_id_foreign` (`warehouse_id`),
  KEY `quotes_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `quotes_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  CONSTRAINT `quotes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `quotes` WRITE;
/*!40000 ALTER TABLE `quotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `quotes_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotes_products` (
  `quote_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `product_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_description` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(16,6) unsigned NOT NULL,
  `price_sale` decimal(16,6) unsigned NOT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`quote_id`,`product_id`),
  KEY `quotes_products_warehouse_id_foreign` (`warehouse_id`),
  KEY `quotes_products_product_id_foreign` (`product_id`),
  KEY `quotes_products_category_id_foreign` (`category_id`),
  KEY `quotes_products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `quotes_products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `quotes_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `quotes_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `quotes_products_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`),
  CONSTRAINT `quotes_products_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `quotes_products` WRITE;
/*!40000 ALTER TABLE `quotes_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotes_products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `quotes_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotes_services` (
  `quote_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `service_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(16,6) unsigned NOT NULL,
  `price_sale` decimal(16,6) unsigned NOT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`quote_id`,`service_id`),
  KEY `quotes_services_service_id_foreign` (`service_id`),
  CONSTRAINT `quotes_services_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`),
  CONSTRAINT `quotes_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `quotes_services` WRITE;
/*!40000 ALTER TABLE `quotes_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotes_services` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reservation_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type_document` enum('DNI','RUC') COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_recorder_id` bigint unsigned NOT NULL,
  `user_recorder_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_sale_code` enum('80','3','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_sale_name` enum('NOTA DE VENTA','BOLETA DE VENTA ELECTRÓNICA','FACTURA ELECTRÓNICA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `igv_percentage` decimal(14,6) unsigned NOT NULL,
  `subtotal` decimal(14,6) unsigned NOT NULL,
  `igv_amount` decimal(14,6) unsigned NOT NULL,
  `total` decimal(14,6) unsigned NOT NULL,
  `legend` varchar(260) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method_pay_id_1` bigint unsigned NOT NULL,
  `amount_pay_1` decimal(14,6) NOT NULL,
  `method_pay_id_2` bigint unsigned DEFAULT NULL,
  `amount_pay_2` decimal(14,6) DEFAULT NULL,
  `correlative` int unsigned NOT NULL,
  `serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('ACEPTADO','PENDIENTE','ENVIADO','RECHAZADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `response_cdrZip` tinyint DEFAULT NULL,
  `response_success` tinyint DEFAULT NULL,
  `response_error_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_error_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_notes` longtext COLLATE utf8mb4_unicode_ci,
  `cdr_response_reference` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_cdr` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_xml` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_qr` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_documents_user_recorder_id_foreign` (`user_recorder_id`),
  KEY `reservation_documents_method_pay_id_1_foreign` (`method_pay_id_1`),
  KEY `reservation_documents_method_pay_id_2_foreign` (`method_pay_id_2`),
  CONSTRAINT `reservation_documents_method_pay_id_1_foreign` FOREIGN KEY (`method_pay_id_1`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `reservation_documents_method_pay_id_2_foreign` FOREIGN KEY (`method_pay_id_2`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `reservation_documents_user_recorder_id_foreign` FOREIGN KEY (`user_recorder_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reservation_documents` WRITE;
/*!40000 ALTER TABLE `reservation_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_documents` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reservation_documents_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_documents_detail` (
  `reservation_document_id` bigint unsigned NOT NULL,
  `product_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_description` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) unsigned NOT NULL,
  `price_sale` decimal(10,2) unsigned NOT NULL,
  `amount` decimal(10,2) unsigned NOT NULL,
  `mto_valor_unitario` decimal(16,6) NOT NULL,
  `mto_valor_venta` decimal(16,6) NOT NULL,
  `mto_base_igv` decimal(16,6) NOT NULL,
  `porcentaje_igv` decimal(16,6) NOT NULL,
  `igv` decimal(16,6) NOT NULL,
  `tip_afe_igv` bigint unsigned NOT NULL,
  `total_impuestos` decimal(16,6) NOT NULL,
  `mto_precio_unitario` decimal(16,6) NOT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`reservation_document_id`),
  CONSTRAINT `reservation_documents_detail_reservation_document_id_foreign` FOREIGN KEY (`reservation_document_id`) REFERENCES `reservation_documents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reservation_documents_detail` WRITE;
/*!40000 ALTER TABLE `reservation_documents_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_documents_detail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('OCUPADO','FINALIZADO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OCUPADO',
  `estado_delete` int NOT NULL DEFAULT '1',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_code_unique` (`code`),
  KEY `reservations_table_id_foreign` (`table_id`),
  KEY `reservations_order_id_foreign` (`order_id`),
  KEY `reservations_customer_id_index` (`customer_id`),
  CONSTRAINT `reservations_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `reservations_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(39,1),(20,2),(1,3),(2,3),(3,3),(4,3),(5,3),(6,3),(7,3),(21,3);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','ACTIVO','2026-05-12 19:22:57','2026-05-12 19:22:57'),(2,'MESERO','web','ACTIVO','2026-05-12 19:22:57','2026-05-12 19:22:57'),(3,'CAJERO','web','ACTIVO','2026-05-12 19:22:57','2026-05-12 19:22:57'),(4,'COCINERO','web','ACTIVO','2026-05-12 19:22:57','2026-05-12 19:22:57'),(5,'CONTADOR','web','ACTIVO','2026-05-12 19:22:57','2026-05-12 19:22:57');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type_document` enum('DNI','RUC') COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `petty_cash_id` bigint unsigned NOT NULL,
  `petty_cash_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `petty_cash_book_id` bigint unsigned NOT NULL,
  `type_sale_id` bigint unsigned NOT NULL,
  `type_sale_code` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_sale_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `igv_percentage` decimal(16,6) unsigned NOT NULL,
  `subtotal` decimal(16,6) unsigned NOT NULL,
  `igv_amount` decimal(16,6) unsigned NOT NULL,
  `total` decimal(16,6) unsigned NOT NULL,
  `total_pay` decimal(16,6) unsigned NOT NULL,
  `discount` decimal(16,6) unsigned NOT NULL DEFAULT '0.000000',
  `discount_base` decimal(16,6) unsigned NOT NULL DEFAULT '0.000000',
  `discount_igv` decimal(16,6) unsigned NOT NULL DEFAULT '0.000000',
  `mto_oper_gravadas` decimal(16,6) unsigned NOT NULL,
  `mto_igv` decimal(16,6) unsigned NOT NULL,
  `total_impuestos` decimal(16,6) unsigned NOT NULL,
  `valor_venta` decimal(16,6) unsigned NOT NULL,
  `sub_total` decimal(16,6) unsigned NOT NULL,
  `mto_imp_venta` decimal(16,6) unsigned NOT NULL,
  `change_pay` decimal(16,6) unsigned NOT NULL DEFAULT '0.000000',
  `legend` varchar(260) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correlative` int unsigned NOT NULL,
  `serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sunat_status` enum('ACEPTADO','PENDIENTE','RECHAZADO','EN PROCESO','OBSERVADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `pay_status` enum('PAGADO','PENDIENTE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `pending_print` enum('SI','NO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO',
  `response_cdrZip` tinyint DEFAULT NULL,
  `response_success` tinyint DEFAULT NULL,
  `response_error_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_error_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_notes` longtext COLLATE utf8mb4_unicode_ci,
  `cdr_response_reference` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_cdr` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_xml` longtext COLLATE utf8mb4_unicode_ci,
  `ruta_qr` longtext COLLATE utf8mb4_unicode_ci,
  `last_send_message` longtext COLLATE utf8mb4_unicode_ci,
  `type` enum('PRODUCTOS','RESERVAS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `converted_to_id` bigint unsigned DEFAULT NULL,
  `converted_to_serie` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `converted_from_id` bigint unsigned DEFAULT NULL,
  `converted_from_serie` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_pending_print` datetime DEFAULT NULL,
  `payment_condition_id` bigint unsigned NOT NULL,
  `payment_condition_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_condition_days` int unsigned NOT NULL,
  `payment_status` enum('PAGADO','PENDIENTE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `registration_date` date NOT NULL,
  `expiration_date` date NOT NULL,
  `summary_id` bigint unsigned DEFAULT NULL,
  `summary_serie` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `processing_at` datetime DEFAULT NULL,
  `send_at` datetime DEFAULT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `next_retry_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_public_hash_unique` (`public_hash`),
  KEY `sales_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_petty_cash_id_foreign` (`petty_cash_id`),
  KEY `sales_petty_cash_book_id_foreign` (`petty_cash_book_id`),
  KEY `sales_payment_condition_id_foreign` (`payment_condition_id`),
  CONSTRAINT `sales_payment_condition_id_foreign` FOREIGN KEY (`payment_condition_id`) REFERENCES `payment_conditions` (`id`),
  CONSTRAINT `sales_petty_cash_book_id_foreign` FOREIGN KEY (`petty_cash_book_id`) REFERENCES `petty_cash_books` (`id`),
  CONSTRAINT `sales_petty_cash_id_foreign` FOREIGN KEY (`petty_cash_id`) REFERENCES `petty_cashes` (`id`),
  CONSTRAINT `sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sales_dishes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_dishes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `dish_id` bigint unsigned NOT NULL,
  `dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_dish_id` bigint unsigned NOT NULL,
  `type_dish_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `programming_id` bigint unsigned NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(16,6) NOT NULL,
  `observation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mto_valor_unitario` decimal(16,6) NOT NULL,
  `mto_valor_venta` decimal(16,6) NOT NULL,
  `mto_base_igv` decimal(16,6) NOT NULL,
  `porcentaje_igv` decimal(16,6) NOT NULL,
  `igv` decimal(16,6) NOT NULL,
  `tip_afe_igv` bigint unsigned NOT NULL,
  `total_impuestos` decimal(16,6) NOT NULL,
  `mto_precio_unitario` decimal(16,6) NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_dishes_sale_id_foreign` (`sale_id`),
  KEY `sales_dishes_dish_id_foreign` (`dish_id`),
  CONSTRAINT `sales_dishes_dish_id_foreign` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`),
  CONSTRAINT `sales_dishes_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sales_dishes` WRITE;
/*!40000 ALTER TABLE `sales_dishes` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_dishes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sales_pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_pays` (
  `sale_id` bigint unsigned NOT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `payment_method_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(16,6) NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sale_id`,`payment_method_id`),
  KEY `sales_pays_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `sales_pays_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `sales_pays_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sales_pays` WRITE;
/*!40000 ALTER TABLE `sales_pays` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_pays` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sales_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_price` decimal(16,6) unsigned NOT NULL,
  `sale_price` decimal(16,6) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(16,6) NOT NULL,
  `observation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mto_valor_unitario` decimal(16,6) NOT NULL,
  `mto_valor_venta` decimal(16,6) NOT NULL,
  `mto_base_igv` decimal(16,6) NOT NULL,
  `porcentaje_igv` decimal(16,6) NOT NULL,
  `igv` decimal(16,6) NOT NULL,
  `tip_afe_igv` bigint unsigned NOT NULL,
  `total_impuestos` decimal(16,6) NOT NULL,
  `mto_precio_unitario` decimal(16,6) NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_products_sale_id_foreign` (`sale_id`),
  KEY `sales_products_product_id_foreign` (`product_id`),
  CONSTRAINT `sales_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sales_products_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sales_products` WRITE;
/*!40000 ALTER TABLE `sales_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `schedules` WRITE;
/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,'MAÑANA','2026-05-12 19:22:56','2026-05-12 19:22:56'),(2,'TARDE','2026-05-12 19:22:56','2026-05-12 19:22:56'),(3,'NOCHE','2026-05-12 19:22:57','2026-05-12 19:22:57');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `summaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `serie` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correlative` bigint unsigned NOT NULL,
  `regularize` tinyint(1) NOT NULL DEFAULT '0',
  `send_sunat` tinyint(1) NOT NULL DEFAULT '0',
  `summary_result_success` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary_result_error` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary_result_ticket` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_invoices` date DEFAULT NULL,
  `route_xml` longtext COLLATE utf8mb4_unicode_ci,
  `route_cdr` longtext COLLATE utf8mb4_unicode_ci,
  `response_error` longtext COLLATE utf8mb4_unicode_ci,
  `cdr_response_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_description` longtext COLLATE utf8mb4_unicode_ci,
  `summary_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_response_notes` longtext COLLATE utf8mb4_unicode_ci,
  `status_result_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_result_success` tinyint DEFAULT NULL,
  `status_result_error_code` longtext COLLATE utf8mb4_unicode_ci,
  `status_result_error_message` longtext COLLATE utf8mb4_unicode_ci,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `sunat_status` enum('PENDIENTE','EN PROCESO','ACEPTADO','RECHAZADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `cdr_response_reference` longtext COLLATE utf8mb4_unicode_ci,
  `summary_result` longtext COLLATE utf8mb4_unicode_ci,
  `last_message` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `summaries` WRITE;
/*!40000 ALTER TABLE `summaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `summaries` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `summaries_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `summaries_details` (
  `summary_id` bigint unsigned NOT NULL,
  `sale_id` bigint unsigned NOT NULL,
  `serie` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correlative` bigint DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `igv` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `customer_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_document_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `percentage_igv` decimal(15,2) DEFAULT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`summary_id`,`sale_id`),
  CONSTRAINT `summaries_details_summary_id_foreign` FOREIGN KEY (`summary_id`) REFERENCES `summaries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `summaries_details` WRITE;
/*!40000 ALTER TABLE `summaries_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `summaries_details` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `supplier_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned DEFAULT NULL,
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `paid` decimal(16,6) unsigned NOT NULL DEFAULT '0.000000',
  `balance` decimal(16,6) unsigned NOT NULL,
  `status` enum('PENDIENTE','PAGADO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deletor_user_id` bigint unsigned DEFAULT NULL,
  `deletor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_accounts_purchase_id_foreign` (`purchase_id`),
  CONSTRAINT `supplier_accounts_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchase_documents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `supplier_accounts` WRITE;
/*!40000 ALTER TABLE `supplier_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_accounts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `supplier_accounts_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_accounts_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_account_id` bigint unsigned NOT NULL,
  `petty_cash_book_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `observation` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_route` longtext COLLATE utf8mb4_unicode_ci,
  `img_name` longtext COLLATE utf8mb4_unicode_ci,
  `payment_method_id` bigint unsigned NOT NULL,
  `cash` decimal(16,6) unsigned DEFAULT '0.000000',
  `amount` decimal(16,6) unsigned DEFAULT '0.000000',
  `balance` decimal(16,6) unsigned DEFAULT NULL,
  `total` decimal(16,6) unsigned DEFAULT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid` decimal(16,6) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_accounts_details_supplier_account_id_foreign` (`supplier_account_id`),
  KEY `supplier_accounts_details_petty_cash_book_id_foreign` (`petty_cash_book_id`),
  KEY `supplier_accounts_details_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `supplier_accounts_details_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `supplier_accounts_details_petty_cash_book_id_foreign` FOREIGN KEY (`petty_cash_book_id`) REFERENCES `petty_cash_books` (`id`),
  CONSTRAINT `supplier_accounts_details_supplier_account_id_foreign` FOREIGN KEY (`supplier_account_id`) REFERENCES `supplier_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `supplier_accounts_details` WRITE;
/*!40000 ALTER TABLE `supplier_accounts_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_accounts_details` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type_identity_document_id` bigint unsigned NOT NULL,
  `type_document_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_document_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_document_code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,1,'DOCUMENTO NACIONAL DE IDENTIDAD','DNI','01','99999999','PROVEEDOR VARIOS',NULL,NULL,NULL,'ACTIVO','2026-05-12 19:22:57','2026-05-12 19:22:57');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('LIBRE','ANULADO','OCUPADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LIBRE',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `type_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `type_fields` WRITE;
/*!40000 ALTER TABLE `type_fields` DISABLE KEYS */;
INSERT INTO `type_fields` VALUES (1,'Fútbol','ACTIVO','2026-05-12 19:22:58','2026-05-12 19:22:58'),(2,'Voleibol','ACTIVO','2026-05-12 19:22:58','2026-05-12 19:22:58'),(3,'Tenis','ACTIVO','2026-05-12 19:22:58','2026-05-12 19:22:58');
/*!40000 ALTER TABLE `type_fields` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `types_dish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types_dish` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_route` longtext COLLATE utf8mb4_unicode_ci,
  `img_name` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `creator_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `types_dish` WRITE;
/*!40000 ALTER TABLE `types_dish` DISABLE KEYS */;
/*!40000 ALTER TABLE `types_dish` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `password_visible` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `collaborator_id` bigint unsigned NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_collaborator_id_foreign` (`collaborator_id`),
  CONSTRAINT `users_collaborator_id_foreign` FOREIGN KEY (`collaborator_id`) REFERENCES `collaborators` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'ADMIN','admin@gmail.com',NULL,'$2y$10$eCwyYdoxu.GJ3v34z/x74eDBml8pP62PM58h/NZaRf9Zlv59N.d6i',NULL,NULL,NULL,'123456789','ACTIVO',1,NULL,'2026-05-12 19:23:02','2026-05-12 19:23:02'),(2,'CAJERO 1','cajero@gmail.com',NULL,'$2y$10$HMH0996nXey7A4qKOlF0euUh3RrfmJeurQO/VprJXezGbaRP54mTm',NULL,NULL,NULL,'123456789','ACTIVO',2,NULL,'2026-05-12 19:23:05','2026-05-12 19:23:05'),(3,'MESERO 1','mesero1@gmail.com',NULL,'$2y$10$BsM8JHj9p4NqSk/YaU5Pc.Tit1dWSvvVRmBsYsRrMQbLXzxxdawE6',NULL,NULL,NULL,'123456789','ACTIVO',3,NULL,'2026-05-12 19:23:06','2026-05-12 19:23:06'),(4,'MESERO 2','mesero2@gmail.com',NULL,'$2y$10$G8eNfiJENfAJ9rba5jSyD.NELYwgAKyGGOyRp500zG5833EN0IKmu',NULL,NULL,NULL,'123456789','ACTIVO',4,NULL,'2026-05-12 19:23:07','2026-05-12 19:23:07'),(5,'MESERO 3','mesero3@gmail.com',NULL,'$2y$10$/FZg2BP5Uchdod6tDOgYzupNOCZqwbmQQpzhTgB2PPABVLcYWBu0G',NULL,NULL,NULL,'123456789','ACTIVO',5,NULL,'2026-05-12 19:23:08','2026-05-12 19:23:08'),(6,'MESERO 4','mesero4@gmail.com',NULL,'$2y$10$WoX.GNpElUathjRPw9Ytf.oW.0KyTKE51LCjEhsjFXdSynmfkdzPq',NULL,NULL,NULL,'123456789','ACTIVO',6,NULL,'2026-05-12 19:23:09','2026-05-12 19:23:09'),(7,'MESERO 5','mesero5@gmail.com',NULL,'$2y$10$cO.nGk2nxMynxRWTgTa8P.6aS14DbgODWDh0jn8ketrdLYH/0tmgK',NULL,NULL,NULL,'123456789','ACTIVO',7,NULL,'2026-05-12 19:23:09','2026-05-12 19:23:09'),(8,'MESERO 6','mesero6@gmail.com',NULL,'$2y$10$9Vu2MHKlzVTRlv6c86qJ3.1fq99kjsom2sP56gv/vJid.6YixNdVK',NULL,NULL,NULL,'123456789','ACTIVO',8,NULL,'2026-05-12 19:23:10','2026-05-12 19:23:10'),(9,'MESERO 7','mesero7@gmail.com',NULL,'$2y$10$9SyeeItxJYT6Trhai9xe3OYZY6ioM2B5hxVVFFtrJVMU9izdyehn6',NULL,NULL,NULL,'123456789','ACTIVO',9,NULL,'2026-05-12 19:23:10','2026-05-12 19:23:10'),(10,'MESERO 8','mesero8@gmail.com',NULL,'$2y$10$lCBcacmdLzeuvnkf8dwi8Op.xUvAvbZAuc7kBIqxXpye1Dytag71m',NULL,NULL,NULL,'123456789','ACTIVO',10,NULL,'2026-05-12 19:23:10','2026-05-12 19:23:10'),(11,'MESERO 9','mesero9@gmail.com',NULL,'$2y$10$mQS9SkUmXioyVdE5c5nWm.3mKo4iSZvJgfqiMmwtj21usQ98o8mXK',NULL,NULL,NULL,'123456789','ACTIVO',11,NULL,'2026-05-12 19:23:11','2026-05-12 19:23:11'),(12,'MESERO 10','mesero10@gmail.com',NULL,'$2y$10$lc3noZLgheTpbcA9fOWxS.QLAGYXJKbnLJcO1xgHx2MseatmSjrAm',NULL,NULL,NULL,'123456789','ACTIVO',12,NULL,'2026-05-12 19:23:11','2026-05-12 19:23:11'),(13,'MESERO 11','mesero11@gmail.com',NULL,'$2y$10$5KcW4OdngYzDojdHtKJJQ.I0sdTQ1rkm16kLybnr3CXpC3kwP18Ny',NULL,NULL,NULL,'123456789','ACTIVO',13,NULL,'2026-05-12 19:23:12','2026-05-12 19:23:12'),(14,'MESERO 12','mesero12@gmail.com',NULL,'$2y$10$2.zSapw8WUVq/g6JE6ml4OAiT58KiBPYsdnvxtj.gyd/iypqRL5a.',NULL,NULL,NULL,'123456789','ACTIVO',14,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(15,'MESERO 13','mesero13@gmail.com',NULL,'$2y$10$QOkGJpThiyMM5r3kfT9mDO4O4TnBhycRQeu9jCPcXkjf8o9gruJne',NULL,NULL,NULL,'123456789','ACTIVO',15,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(16,'MESERO 14','mesero14@gmail.com',NULL,'$2y$10$4QeR4oD8nKSmz3sd5G79PuGCcFwJE6bZ47cIeXyLhAREoBBuYRwAm',NULL,NULL,NULL,'123456789','ACTIVO',16,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(17,'MESERO 15','mesero15@gmail.com',NULL,'$2y$10$ztrCZDx4kT9xsH0uKAIhg.gg3hwzJ9IOweG0Z2kHnK83J1lV1OcC6',NULL,NULL,NULL,'123456789','ACTIVO',17,NULL,'2026-05-12 19:23:13','2026-05-12 19:23:13'),(18,'MESERO 16','mesero16@gmail.com',NULL,'$2y$10$s/HucXyir3HBjNxSwNRLCOLrFkyv/H/iEW9XvajOWfApDFRI/ek7S',NULL,NULL,NULL,'123456789','ACTIVO',18,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(19,'MESERO 17','mesero17@gmail.com',NULL,'$2y$10$GfMJfVmgQVDSrM4txQJMm.xLCI7XMT/bM0Cev4ydzGXkmwc5B.jF.',NULL,NULL,NULL,'123456789','ACTIVO',19,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(20,'MESERO 18','mesero18@gmail.com',NULL,'$2y$10$YnP0XORNTjUg2w35KuAi5.9Kef5KGGxyKSohvBaHs.yk5SN3c.ICG',NULL,NULL,NULL,'123456789','ACTIVO',20,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(21,'MESERO 19','mesero19@gmail.com',NULL,'$2y$10$f8PC7z1TuwKiAK4JAq9ESuGi5nLIV2FWfrmlsLo9IA6yMn9u310RW',NULL,NULL,NULL,'123456789','ACTIVO',21,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(22,'MESERO 20','mesero20@gmail.com',NULL,'$2y$10$KCUW3y2avCO.AqEY.4MyVO5GEVESIWHRvQ7qswy6FA6jzCGnWTYeW',NULL,NULL,NULL,'123456789','ACTIVO',22,NULL,'2026-05-12 19:23:14','2026-05-12 19:23:14'),(23,'CONTADOR','contador@gmail.com',NULL,'$2y$10$xUxXPONxoZPS.DRBjAP2vuZup8fVGcJj7eVX2yPSuoVs.lepluX8S',NULL,NULL,NULL,'123456789','ACTIVO',23,NULL,'2026-05-12 19:23:15','2026-05-12 19:23:15'),(24,'COCINERO','cocinero@gmail.com',NULL,'$2y$10$BURQkBlmtIhVeTm.YQXTbOi28Zw2dfvsMiCtjJBVVUL/wC/bswpIe',NULL,NULL,NULL,'123456789','ACTIVO',24,NULL,'2026-05-12 19:23:15','2026-05-12 19:23:15');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plate` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vin` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `year_id` bigint unsigned DEFAULT NULL,
  `color_id` bigint unsigned NOT NULL,
  `observation` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `warehouse_consumables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_consumables` (
  `warehouse_id` bigint unsigned NOT NULL,
  `consumable_id` bigint unsigned NOT NULL,
  `stock` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`warehouse_id`,`consumable_id`),
  KEY `warehouse_consumables_consumable_id_foreign` (`consumable_id`),
  CONSTRAINT `warehouse_consumables_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`),
  CONSTRAINT `warehouse_consumables_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `warehouse_consumables` WRITE;
/*!40000 ALTER TABLE `warehouse_consumables` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_consumables` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `warehouse_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_products` (
  `warehouse_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `stock` decimal(10,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`warehouse_id`,`product_id`),
  KEY `warehouse_products_product_id_foreign` (`product_id`),
  CONSTRAINT `warehouse_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `warehouse_products_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `warehouse_products` WRITE;
/*!40000 ALTER TABLE `warehouse_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (1,'CENTRAL','ACTIVO','2026-05-12 19:22:53','2026-05-12 19:22:53');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `work_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type_document_abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `plate` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(16,6) unsigned NOT NULL,
  `subtotal` decimal(16,6) unsigned NOT NULL,
  `igv` decimal(16,6) unsigned NOT NULL,
  `creator_user_id` bigint unsigned DEFAULT NULL,
  `editor_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_id` bigint unsigned DEFAULT NULL,
  `delete_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVO','ANULADO','FINALIZADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_orders_warehouse_id_foreign` (`warehouse_id`),
  KEY `work_orders_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `work_orders_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  CONSTRAINT `work_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `work_orders` WRITE;
/*!40000 ALTER TABLE `work_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_orders` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `work_orders_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `img_route` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_orders_images_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `work_orders_images_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `work_orders_images` WRITE;
/*!40000 ALTER TABLE `work_orders_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_orders_images` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `work_orders_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders_inventory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `inventory_id` bigint unsigned NOT NULL,
  `inventory_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_orders_inventory_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `work_orders_inventory_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `work_orders_inventory` WRITE;
/*!40000 ALTER TABLE `work_orders_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_orders_inventory` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `work_orders_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders_products` (
  `work_order_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `warehouse_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `product_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_description` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(16,6) unsigned NOT NULL,
  `price_sale` decimal(16,6) unsigned NOT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`work_order_id`,`product_id`),
  KEY `work_orders_products_warehouse_id_foreign` (`warehouse_id`),
  KEY `work_orders_products_product_id_foreign` (`product_id`),
  KEY `work_orders_products_category_id_foreign` (`category_id`),
  KEY `work_orders_products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `work_orders_products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `work_orders_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `work_orders_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `work_orders_products_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `work_orders_products_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `work_orders_products` WRITE;
/*!40000 ALTER TABLE `work_orders_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_orders_products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `work_orders_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders_services` (
  `work_order_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `service_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(16,6) unsigned NOT NULL,
  `price_sale` decimal(16,6) unsigned NOT NULL,
  `amount` decimal(16,6) unsigned NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`work_order_id`,`service_id`),
  KEY `work_orders_services_service_id_foreign` (`service_id`),
  CONSTRAINT `work_orders_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  CONSTRAINT `work_orders_services_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `work_orders_services` WRITE;
/*!40000 ALTER TABLE `work_orders_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_orders_services` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `work_orders_technicians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders_technicians` (
  `work_order_id` bigint unsigned NOT NULL,
  `technical_id` bigint unsigned NOT NULL,
  `technical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVO','ANULADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`work_order_id`,`technical_id`),
  KEY `work_orders_technicians_technical_id_foreign` (`technical_id`),
  CONSTRAINT `work_orders_technicians_technical_id_foreign` FOREIGN KEY (`technical_id`) REFERENCES `users` (`id`),
  CONSTRAINT `work_orders_technicians_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `work_orders_technicians` WRITE;
/*!40000 ALTER TABLE `work_orders_technicians` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_orders_technicians` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 DROP FUNCTION IF EXISTS `fn_stock` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_stock`(p_almacen_id INT, p_producto_id INT, p_fecha DATE) RETURNS decimal(16,6)
    DETERMINISTIC
BEGIN
                DECLARE p_stock DECIMAL(16,6) DEFAULT 0;

                SELECT
                    COALESCE(SUM(cantidad_entrada) - SUM(cantidad_salida), 0)
                INTO p_stock
                FROM (

                    SELECT
                        CASE
                            WHEN k.type = 'ENTRADA' THEN k.quantity
                            ELSE 0
                        END AS cantidad_entrada,

                        CASE
                            WHEN k.type = 'SALIDA' THEN k.quantity
                            ELSE 0
                        END AS cantidad_salida
                    FROM kardex AS k
                    WHERE k.warehouse_id = p_almacen_id
                    AND k.product_id = p_producto_id
                    AND DATE(k.date) < p_fecha

                ) AS t;

                RETURN p_stock;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_stock_consumable` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_stock_consumable`(p_almacen_id INT, p_consumable_id INT, p_fecha DATE) RETURNS decimal(16,6)
    DETERMINISTIC
BEGIN
                DECLARE p_stock DECIMAL(16,6) DEFAULT 0;

                SELECT
                    COALESCE(SUM(cantidad_entrada) - SUM(cantidad_salida), 0)
                INTO p_stock
                FROM (

                    SELECT
                        CASE
                            WHEN k.type = 'ENTRADA' THEN k.quantity
                            ELSE 0
                        END AS cantidad_entrada,

                        CASE
                            WHEN k.type = 'SALIDA' THEN k.quantity
                            ELSE 0
                        END AS cantidad_salida
                    FROM consumable_kardex AS k
                    WHERE k.warehouse_id = p_almacen_id
                    AND k.consumable_id = p_consumable_id
                    AND DATE(k.date) < p_fecha

                ) AS t;

                RETURN p_stock;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_kardex` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_kardex`(
                IN p_almacen_id INT,
                IN p_producto_id INT,
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE
            )
BEGIN
                -- Variables de stock por producto
                DECLARE stock_actual INT DEFAULT 0;
                DECLARE stock_anterior INT DEFAULT 0;
                DECLARE producto_actual INT DEFAULT NULL;

                -- Reset de variables
                SET @rownum := 0;
                SET @stock_actual := 0, @stock_anterior := 0, @producto_actual := NULL;

                -- Consulta del historial de stock con filtros
                SELECT
                    @rownum := @rownum + 1 AS kardex_order,
                    t.product_id,
                    t.date,
                    t.type,
                    t.document_serie,
                    t.warehouse_name,
                    t.product_name,
                    t.category_name,
                    t.brand_name,
                    t.creator_user_name,

                    -- SI EL PRODUCTO ES EL MISMO COLOCAR EL STOCK POSTERIOR ANTERIOR, CASO CONTRARIO CALCULAR STOCK
                    @stock_anterior := IF(
                        @producto_actual = t.product_id,
                        @stock_actual,
                        fn_stock(p_almacen_id, t.product_id, p_fecha_inicio)
                    ) AS previous_stock,

                    -- Asignamos el valor de stock posterior
                    t.quantity_in AS entrada,
                    t.quantity_out AS salida,

                    -- Calculamos el stock posterior
                    @stock_actual := @stock_anterior + t.quantity_in - t.quantity_out AS later_stock,

                    -- Asignamos el producto actual para controlar la iteración
                    @producto_actual := t.product_id AS producto_actual
                FROM (

                    -- KARDEX
                    SELECT
                        k.product_id,
                        k.date,
                        k.type,
                        k.document_serie,
                        k.warehouse_name,
                        k.product_name,
                        k.category_name,
                        k.brand_name,
                        k.creator_user_name,
                        CASE
                            WHEN k.type = 'ENTRADA' THEN k.quantity
                            ELSE 0
                        END AS quantity_in,

                        CASE
                            WHEN k.type = 'SALIDA' THEN k.quantity
                            ELSE 0
                        END AS quantity_out
                    FROM kardex AS k
                    WHERE k.warehouse_id = p_almacen_id
                    AND k.product_id = p_producto_id
                    AND (p_fecha_inicio IS NOT NULL
                    AND p_fecha_fin IS NOT NULL
                    AND DATE(k.date) BETWEEN p_fecha_inicio AND p_fecha_fin)

                ) t
                ORDER BY t.product_id, t.date ASC;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_kardex_consumable` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_kardex_consumable`(
                IN p_almacen_id INT,
                IN p_consumable_id INT,
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE
            )
BEGIN
                -- Variables de stock por producto
                DECLARE stock_actual INT DEFAULT 0;
                DECLARE stock_anterior INT DEFAULT 0;
                DECLARE producto_actual INT DEFAULT NULL;

                -- Reset de variables
                SET @rownum := 0;
                SET @stock_actual := 0, @stock_anterior := 0, @producto_actual := NULL;

                -- Consulta del historial de stock con filtros
                SELECT
                    @rownum := @rownum + 1 AS kardex_order,
                    t.consumable_id,
                    t.date,
                    t.type,
                    t.document_serie,
                    t.warehouse_name,
                    t.consumable_name,
                    t.category_name,
                    t.brand_name,
                    t.creator_user_name,

                    -- SI EL PRODUCTO ES EL MISMO COLOCAR EL STOCK POSTERIOR ANTERIOR, CASO CONTRARIO CALCULAR STOCK
                    @stock_anterior := IF(
                        @producto_actual = t.consumable_id,
                        @stock_actual,
                        fn_stock(p_almacen_id, t.consumable_id, p_fecha_inicio)
                    ) AS previous_stock,

                    -- Asignamos el valor de stock posterior
                    t.quantity_in AS entrada,
                    t.quantity_out AS salida,

                    -- Calculamos el stock posterior
                    @stock_actual := @stock_anterior + t.quantity_in - t.quantity_out AS later_stock,

                    -- Asignamos el producto actual para controlar la iteración
                    @producto_actual := t.consumable_id AS producto_actual
                FROM (

                    -- KARDEX
                    SELECT
                        k.consumable_id,
                        k.date,
                        k.type,
                        k.document_serie,
                        k.warehouse_name,
                        k.consumable_name,
                        k.category_name,
                        k.brand_name,
                        k.creator_user_name,
                        CASE
                            WHEN k.type = 'ENTRADA' THEN k.quantity
                            ELSE 0
                        END AS quantity_in,

                        CASE
                            WHEN k.type = 'SALIDA' THEN k.quantity
                            ELSE 0
                        END AS quantity_out
                    FROM consumable_kardex AS k
                    WHERE k.warehouse_id = p_almacen_id
                    AND k.consumable_id = p_consumable_id
                    AND (p_fecha_inicio IS NOT NULL
                    AND p_fecha_fin IS NOT NULL
                    AND DATE(k.date) BETWEEN p_fecha_inicio AND p_fecha_fin)

                ) t
                ORDER BY t.consumable_id, t.date ASC;
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

