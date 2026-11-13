-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: nexon_db
-- ------------------------------------------------------
-- Server version	8.4.3

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

--
-- Table structure for table `abandoned_carts`
--

DROP TABLE IF EXISTS `abandoned_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abandoned_carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_data` json NOT NULL,
  `recovery_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `items_count` int unsigned NOT NULL DEFAULT '1',
  `recovery_step` tinyint unsigned NOT NULL DEFAULT '0',
  `last_reminded_at` timestamp NULL DEFAULT NULL,
  `is_recovered` tinyint(1) NOT NULL DEFAULT '0',
  `recovered_at` timestamp NULL DEFAULT NULL,
  `recovered_order_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `abandoned_carts_recovery_hash_unique` (`recovery_hash`),
  KEY `abandoned_carts_user_id_foreign` (`user_id`),
  KEY `abandoned_carts_recovered_order_id_foreign` (`recovered_order_id`),
  KEY `abandoned_carts_is_recovered_created_at_index` (`is_recovered`,`created_at`),
  KEY `abandoned_carts_email_index` (`email`),
  KEY `abandoned_carts_phone_index` (`phone`),
  CONSTRAINT `abandoned_carts_recovered_order_id_foreign` FOREIGN KEY (`recovered_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `abandoned_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abandoned_carts`
--

LOCK TABLES `abandoned_carts` WRITE;
/*!40000 ALTER TABLE `abandoned_carts` DISABLE KEYS */;
INSERT INTO `abandoned_carts` VALUES (1,11,'priya.sharma@example.com','+91 98201 12345','Priya Sharma','[{\"id\": 8, \"qty\": 1, \"name\": \"Ratri Saree\", \"size\": \"M\", \"image\": \"http://nexon.test/images/products/women/ratri-saree-sare-ratr-qbl7-xv6wwm.avif\", \"price\": 3299, \"colorName\": \"Black\"}]','oSNHW927fI2lC6xa8EjKtCQqbDbpotSC',3299.00,1,1,'2026-11-12 07:40:29',0,NULL,NULL,'2026-11-11 11:40:29','2026-11-12 07:40:29'),(2,NULL,'kavita.deshmukh@gmail.com','+91 98112 44332','Kavita Deshmukh','[{\"id\": 9, \"qty\": 1, \"name\": \"Lounge Pajama Set\", \"size\": \"S\", \"image\": \"http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif\", \"price\": 3899, \"colorName\": \"Pastel Lavender\"}]','ICwiLG0q9Y7PeeVLPQWlwIoYBzZcZo4d',3899.00,1,2,'2026-11-11 23:40:29',0,NULL,NULL,'2026-11-10 11:40:29','2026-11-11 23:40:29');
/*!40000 ALTER TABLE `abandoned_carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounting_sync_logs`
--

DROP TABLE IF EXISTS `accounting_sync_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounting_sync_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sync_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `voucher_number` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `direction` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'export_xml',
  `payload_xml` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `synced_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounting_sync_logs_order_id_foreign` (`order_id`),
  KEY `accounting_sync_logs_synced_by_foreign` (`synced_by`),
  KEY `accounting_sync_logs_sync_type_index` (`sync_type`),
  KEY `accounting_sync_logs_voucher_number_index` (`voucher_number`),
  KEY `accounting_sync_logs_status_index` (`status`),
  CONSTRAINT `accounting_sync_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `accounting_sync_logs_synced_by_foreign` FOREIGN KEY (`synced_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_sync_logs`
--

LOCK TABLES `accounting_sync_logs` WRITE;
/*!40000 ALTER TABLE `accounting_sync_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounting_sync_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `addon_product`
--

DROP TABLE IF EXISTS `addon_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addon_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `addon_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `addon_product_product_id_addon_id_unique` (`product_id`,`addon_id`),
  KEY `addon_product_addon_id_foreign` (`addon_id`),
  CONSTRAINT `addon_product_addon_id_foreign` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `addon_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addon_product`
--

LOCK TABLES `addon_product` WRITE;
/*!40000 ALTER TABLE `addon_product` DISABLE KEYS */;
INSERT INTO `addon_product` VALUES (19,8,1,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(20,8,2,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(21,8,3,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(22,9,1,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(23,9,2,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(24,9,3,'2026-11-12 11:40:29','2026-11-12 11:40:29');
/*!40000 ALTER TABLE `addon_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `addons`
--

DROP TABLE IF EXISTS `addons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addons`
--

LOCK TABLES `addons` WRITE;
/*!40000 ALTER TABLE `addons` DISABLE KEYS */;
INSERT INTO `addons` VALUES (1,'Discreet Gift Box','Plain exterior shipping box with silk tissue wrapping inside for total privacy.',49.00,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(2,'Signature Satin Gift Bag','Reusable champagne gold satin pouch with woven ribbon.',129.00,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(3,'Handwritten Love Note','Custom handwritten greeting card with your personalized message.',29.00,1,'2026-11-12 10:57:02','2026-11-12 10:57:02');
/*!40000 ALTER TABLE `addons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appraisal_cycles`
--

DROP TABLE IF EXISTS `appraisal_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appraisal_cycles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `cutoff_date` date NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('upcoming','active','evaluation','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appraisal_cycles`
--

LOCK TABLES `appraisal_cycles` WRITE;
/*!40000 ALTER TABLE `appraisal_cycles` DISABLE KEYS */;
INSERT INTO `appraisal_cycles` VALUES (1,'Annual Performance Review FY 2026-27','2026-01-01','2026-12-31','Comprehensive annual evaluation covering sales targets, client hospitality, and teamwork.','active','2026-11-12 09:21:56','2026-11-12 09:21:56');
/*!40000 ALTER TABLE `appraisal_cycles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attribute_groups`
--

DROP TABLE IF EXISTS `attribute_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attribute_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attribute_groups`
--

LOCK TABLES `attribute_groups` WRITE;
/*!40000 ALTER TABLE `attribute_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `attribute_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_group_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('text','select','multiselect','color','boolean') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `options` json DEFAULT NULL,
  `is_filterable` tinyint(1) NOT NULL DEFAULT '1',
  `is_comparable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attributes_code_unique` (`code`),
  KEY `attributes_attribute_group_id_foreign` (`attribute_group_id`),
  CONSTRAINT `attributes_attribute_group_id_foreign` FOREIGN KEY (`attribute_group_id`) REFERENCES `attribute_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attributes`
--

LOCK TABLES `attributes` WRITE;
/*!40000 ALTER TABLE `attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Nex-on Signature','nexon-signature','Flagship couture & signature collection.',NULL,NULL,1,1,'2026-11-12 09:21:59','2026-11-12 09:21:59'),(2,'Sabyasachi Atelier','sabyasachi-atelier','Opulent royal bridal lehengas, sarees, and heirloom ensembles.',NULL,NULL,1,2,'2026-11-12 09:21:59','2026-11-12 09:21:59'),(3,'Raw Mango','raw-mango','Contemporary handwoven Chanderi, Banarasi, and Mashru silks.',NULL,NULL,1,3,'2026-11-12 09:21:59','2026-11-12 09:21:59'),(4,'Bespoke Heritage','bespoke-heritage','Handcrafted pure tussar, linen, and artisanal handloom fabrics.',NULL,NULL,1,4,'2026-11-12 09:21:59','2026-11-12 09:21:59'),(5,'Vedic Organics','vedic-organics','Pure organic produce, botanicals, and farm-fresh natural wellness.',NULL,NULL,1,5,'2026-11-12 09:21:59','2026-11-12 09:21:59');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (2,23,'Lingerie Sets','lingerie-sets',NULL,2,'2026-11-12 10:57:02','2026-11-12 11:34:11'),(6,23,'Everyday Essentials','everyday-essentials',NULL,6,'2026-11-12 10:57:02','2026-11-12 11:34:29'),(7,NULL,'Men','men',NULL,10,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(8,7,'Shirt','men-shirt',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(9,8,'Formal Shirt','men-shirt-formal-shirt',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(10,8,'Casual Shirt','men-shirt-casual-shirt',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(11,8,'Linen Kurta Shirt','men-shirt-linen-kurta-shirt',NULL,3,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(12,7,'Pants','men-pants',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(13,12,'Formal Trousers','men-pants-formal-trousers',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(14,12,'Chino Pants','men-pants-chino-pants',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(15,12,'Denim Jeans','men-pants-denim-jeans',NULL,3,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(16,7,'Dhuti','men-dhuti',NULL,3,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(17,16,'Silk Dhuti','men-dhuti-silk-dhuti',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(18,16,'Cotton Dhuti','men-dhuti-cotton-dhuti',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(19,7,'Brief','men-brief',NULL,4,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(20,19,'Boxer Briefs','men-brief-boxer-briefs',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(21,19,'Classic Briefs','men-brief-classic-briefs',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(22,19,'Trunks','men-brief-trunks',NULL,3,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(23,NULL,'Women','women',NULL,20,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(24,23,'T-shirt','women-t-shirt',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(25,24,'Oversized Tees','women-t-shirt-oversized-tees',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(26,24,'Crop Tops','women-t-shirt-crop-tops',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(27,23,'Bra','women-bra',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(28,27,'T-Shirt Bra','women-bra-t-shirt-bra',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(29,27,'Strapless Bra','women-bra-strapless-bra',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(30,27,'Push-Up Bra','women-bra-push-up-bra',NULL,3,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(31,23,'Panties','women-panties',NULL,3,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(32,31,'Seamless Hipsters','women-panties-seamless-hipsters',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(33,31,'Bikini Briefs','women-panties-bikini-briefs',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(34,31,'Lace Thongs','women-panties-lace-thongs',NULL,3,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(35,23,'Bralettes','women-bralettes',NULL,4,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(36,35,'Triangle Lace','women-bralettes-triangle-lace',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(37,35,'Longline Bralette','women-bralettes-longline-bralette',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(38,23,'Saree','women-saree',NULL,5,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(39,38,'Chiffon Saree','women-saree-chiffon-saree',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(40,38,'Kanjivaram Silk','women-saree-kanjivaram-silk',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(41,23,'Blouse','women-blouse',NULL,6,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(42,41,'Padded Blouse','women-blouse-padded-blouse',NULL,1,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(43,41,'Embroidered Blouse','women-blouse-embroidered-blouse',NULL,2,'2026-11-12 10:57:02','2026-11-12 10:57:02');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commercial_invoice_items`
--

DROP TABLE IF EXISTS `commercial_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commercial_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commercial_invoice_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hsn_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '18.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commercial_invoice_items_commercial_invoice_id_foreign` (`commercial_invoice_id`),
  KEY `commercial_invoice_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `commercial_invoice_items_commercial_invoice_id_foreign` FOREIGN KEY (`commercial_invoice_id`) REFERENCES `commercial_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commercial_invoice_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commercial_invoice_items`
--

LOCK TABLES `commercial_invoice_items` WRITE;
/*!40000 ALTER TABLE `commercial_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `commercial_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commercial_invoices`
--

DROP TABLE IF EXISTS `commercial_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commercial_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('tax_invoice','credit_note','proforma') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tax_invoice',
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `client_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_gstin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_pan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `place_of_supply` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Maharashtra',
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cgst_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sgst_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igst_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','issued','paid','partial','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commercial_invoices_invoice_number_unique` (`invoice_number`),
  KEY `commercial_invoices_created_by_foreign` (`created_by`),
  KEY `commercial_invoices_invoice_date_status_index` (`invoice_date`,`status`),
  KEY `commercial_invoices_client_gstin_index` (`client_gstin`),
  CONSTRAINT `commercial_invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commercial_invoices`
--

LOCK TABLES `commercial_invoices` WRITE;
/*!40000 ALTER TABLE `commercial_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `commercial_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_uses` int unsigned DEFAULT NULL,
  `used_count` int unsigned NOT NULL DEFAULT '0',
  `expires_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'WELCOME10','percentage',10.00,999.00,1000,0,'2027-05-12',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(2,'FESTIVE500','fixed',500.00,2499.00,500,0,'2027-02-12',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(3,'LUXE15','percentage',15.00,1999.00,300,0,'2027-03-12',1,'2026-11-12 10:57:03','2026-11-12 10:57:03');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_pages`
--

DROP TABLE IF EXISTS `custom_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_blocks` json DEFAULT NULL,
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_pages`
--

LOCK TABLES `custom_pages` WRITE;
/*!40000 ALTER TABLE `custom_pages` DISABLE KEYS */;
INSERT INTO `custom_pages` VALUES (1,'Royal Bridal Silk Trousseau 2026','bridal-silk-trousseau','[{\"type\": \"hero\", \"cta_link\": \"/#products\", \"cta_text\": \"Explore The Edit\", \"headline\": \"Handcrafted Bridal Grandeur\", \"subheadline\": \"Woven in pure mulberry silk and embellished with hand-carved zardozi motifs for timeless royal elegance.\"}, {\"type\": \"countdown\", \"title\": \"Exclusive Bridal Trunk Show Drops In\", \"target_date\": \"2026-11-17T16:27\"}, {\"type\": \"product_grid\", \"limit\": \"4\", \"title\": \"Iconic Bridal Ensembles\"}, {\"type\": \"testimonials\", \"quote\": \"The craftsmanship of Saaya’s silk robe exceeded all expectations on my wedding morning. Pure royal elegance.\", \"author\": \"Aishwarya Rai Bachchan\", \"location\": \"Mumbai &amp; London\"}, {\"type\": \"faq\", \"answer\": \"Visit our Colaba Flagship or Indiranagar Experience Studio for a one-on-one measurement fitting with our master couturiers.\", \"question\": \"How do I book a private bridal consultation?\"}, {\"type\": \"newsletter\", \"headline\": \"Join The VIP Private Salon\", \"subheadline\": \"Receive private invites to trunk shows and bespoke bridal preview edits.\"}]','Handcrafted Bridal Silk Collection | Haute Couture','Explore the handcrafted bridal trousseau collection in Varanasi pure mulberry silk and French chantilly lace.',1,'2026-11-12 10:57:05','2026-11-12 10:57:05'),(2,'Summer Resort Wear & Loungewear','summer-resort-edit','[{\"type\": \"hero\", \"cta_link\": \"/#products\", \"cta_text\": \"Shop Resort Wear\", \"headline\": \"Sun-Drenched Riviera Chic\", \"subheadline\": \"Ultralight pure mulberry silk designed for Mediterranean sunsets and relaxed private villas.\"}, {\"type\": \"product_grid\", \"limit\": \"4\", \"title\": \"Curated Riviera Looks\"}, {\"type\": \"newsletter\", \"headline\": \"Unlock Complimentary Resort Delivery\", \"subheadline\": \"Subscribe for priority express shipping to over 24 global destinations.\"}]','Breezy Silks & French Lace Loungewear 2026','Lightweight mulberry chemises and French lace sets designed for sun-kissed Riviera getaways.',1,'2026-11-12 10:57:05','2026-11-12 10:57:05');
/*!40000 ALTER TABLE `custom_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_user_id_foreign` (`user_id`),
  CONSTRAINT `customer_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_addresses`
--

LOCK TABLES `customer_addresses` WRITE;
/*!40000 ALTER TABLE `customer_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_group_user`
--

DROP TABLE IF EXISTS `customer_group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_group_user` (
  `customer_group_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_group_id`,`user_id`),
  KEY `customer_group_user_user_id_foreign` (`user_id`),
  CONSTRAINT `customer_group_user_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_group_user`
--

LOCK TABLES `customer_group_user` WRITE;
/*!40000 ALTER TABLE `customer_group_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_group_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_groups`
--

DROP TABLE IF EXISTS `customer_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_groups_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_groups`
--

LOCK TABLES `customer_groups` WRITE;
/*!40000 ALTER TABLE `customer_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_personnel`
--

DROP TABLE IF EXISTS `delivery_personnel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_personnel` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bike',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_personnel_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_personnel`
--

LOCK TABLES `delivery_personnel` WRITE;
/*!40000 ALTER TABLE `delivery_personnel` DISABLE KEYS */;
INSERT INTO `delivery_personnel` VALUES (1,'Rahul Sharma','9876543210','rahul.courier@nexon.com','MH 01 AB 1001','bike','active','2026-11-12 10:57:05','2026-11-12 10:57:05'),(2,'Deepak Verma','9876543211','deepak.courier@nexon.com','KA 05 EV 2020','scooter','active','2026-11-12 10:57:05','2026-11-12 10:57:05'),(3,'Sanjay Patil','9876543212','sanjay.fleet@nexon.com','MH 04 VN 8888','van','active','2026-11-12 10:57:05','2026-11-12 10:57:05');
/*!40000 ALTER TABLE `delivery_personnel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_user`
--

DROP TABLE IF EXISTS `department_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_user_department_id_user_id_unique` (`department_id`,`user_id`),
  KEY `department_user_user_id_foreign` (`user_id`),
  CONSTRAINT `department_user_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_user`
--

LOCK TABLES `department_user` WRITE;
/*!40000 ALTER TABLE `department_user` DISABLE KEYS */;
INSERT INTO `department_user` VALUES (1,6,12,'Warehouse Manager',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(2,5,13,'Store Manager',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(3,1,14,'Sales Associate',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(4,7,15,'Cashier',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(5,6,16,'Inventory Analyst',1,'2026-11-12 10:57:04','2026-11-12 10:57:04'),(6,8,17,'Customer Support',1,'2026-11-12 10:57:04','2026-11-12 10:57:04');
/*!40000 ALTER TABLE `department_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `head_user_id` bigint unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'indigo',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`),
  KEY `departments_head_user_id_foreign` (`head_user_id`),
  CONSTRAINT `departments_head_user_id_foreign` FOREIGN KEY (`head_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Retail Store Sales','SALES',NULL,'Boutique storefront customer styling, floor service, and POS billing.','indigo',1,'2026-11-12 09:21:56','2026-11-12 09:21:56'),(2,'Warehouse & Fulfillment','WH',NULL,'Inventory picking, packing, dispatch, and inter-store stock transfers.','blue',1,'2026-11-12 09:21:56','2026-11-12 09:21:56'),(3,'Atelier & Bespoke Tailoring','ATELIER',NULL,'Garment alterations, custom silhouettes, and bespoke embroidery.','rose',1,'2026-11-12 09:21:56','2026-11-12 09:21:56'),(4,'Customer Care & Concierge','CARE',NULL,'VIP concierge service, order support tickets, and post-purchase follow-up.','emerald',1,'2026-11-12 09:21:56','2026-11-12 09:21:56'),(5,'Store Management & Admin','MGMT',NULL,'General store administration, cash drawer audits, and staff supervision.','purple',1,'2026-11-12 09:21:56','2026-11-12 09:21:56'),(6,'Operations','OPERAT',NULL,NULL,'indigo',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(7,'POS','POS',NULL,NULL,'indigo',1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(8,'Support','SUPPOR',NULL,NULL,'indigo',1,'2026-11-12 10:57:04','2026-11-12 10:57:04');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_feedbacks`
--

DROP TABLE IF EXISTS `employee_feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_feedbacks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` bigint unsigned NOT NULL,
  `to_user_id` bigint unsigned NOT NULL,
  `type` enum('praise','manager_feedback','peer_review') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'praise',
  `badge` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_feedbacks_from_user_id_foreign` (`from_user_id`),
  KEY `employee_feedbacks_to_user_id_created_at_index` (`to_user_id`,`created_at`),
  CONSTRAINT `employee_feedbacks_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedbacks_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_feedbacks`
--

LOCK TABLES `employee_feedbacks` WRITE;
/*!40000 ALTER TABLE `employee_feedbacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_feedbacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_goals`
--

DROP TABLE IF EXISTS `employee_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_goals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `creator_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metric_type` enum('numeric','revenue','percentage','boolean') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `target_value` decimal(12,2) NOT NULL DEFAULT '100.00',
  `current_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '%',
  `progress_percent` tinyint unsigned NOT NULL DEFAULT '0',
  `status` enum('not_started','on_track','at_risk','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `due_date` date NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General Performance',
  `weightage` tinyint unsigned NOT NULL DEFAULT '20',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_goals_creator_id_foreign` (`creator_id`),
  KEY `employee_goals_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `employee_goals_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_goals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_goals`
--

LOCK TABLES `employee_goals` WRITE;
/*!40000 ALTER TABLE `employee_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366f1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,'Rent & Store Leases','rent-leases','#3b82f6','Commercial showroom, warehouse, and office rent',1,'2026-11-12 09:21:54','2026-11-12 09:21:54'),(2,'Packaging & Bags','packaging-bags','#10b981','Boxes, tissue wrap, shopping bags, and branded tags',1,'2026-11-12 09:21:54','2026-11-12 09:21:54'),(3,'Courier & Freight Outward','courier-freight','#f59e0b','BlueDart, Delhivery, and express logistics shipping charges',1,'2026-11-12 09:21:54','2026-11-12 09:21:54'),(4,'Salaries, Wages & Stipends','salaries-wages','#8b5cf6','Staff payroll, store cashier wages, and commissions',1,'2026-11-12 09:21:54','2026-11-12 09:21:54'),(5,'Marketing & Paid Ads','marketing-ads','#ec4899','Meta ads, Google Ads, influencers, and creative campaigns',1,'2026-11-12 09:21:54','2026-11-12 09:21:54'),(6,'Electricity & Utilities','utilities','#06b6d4','Store electricity, water, internet, and phone bills',1,'2026-11-12 09:21:54','2026-11-12 09:21:54'),(7,'Maintenance & Fixtures','maintenance-fixtures','#64748b','HVAC repairs, lighting, plumbing, and showroom upkeep',1,'2026-11-12 09:21:54','2026-11-12 09:21:54'),(8,'Software & Cloud SaaS','software-saas','#6366f1','Hosting, POS licenses, Shopify/ERP subscriptions',1,'2026-11-12 09:21:54','2026-11-12 09:21:54');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expense_category_id` bigint unsigned NOT NULL,
  `outlet_id` bigint unsigned DEFAULT NULL,
  `expense_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `expense_date` date NOT NULL,
  `vendor_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_gstin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurring_frequency` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  KEY `expenses_outlet_id_foreign` (`outlet_id`),
  KEY `expenses_created_by_foreign` (`created_by`),
  KEY `expenses_expense_date_expense_category_id_index` (`expense_date`,`expense_category_id`),
  CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faqs_category_is_active_sort_order_index` (`category`,`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faqs`
--

LOCK TABLES `faqs` WRITE;
/*!40000 ALTER TABLE `faqs` DISABLE KEYS */;
INSERT INTO `faqs` VALUES (1,'How long does domestic shipping usually take?','Domestic courier dispatches take between 2 to 4 business days for major metro cities (Mumbai, Delhi, Bengaluru, Hyderabad). Custom bespoke alterations require an additional 48 hours in our atelier.','Shipping & Delivery',1,1,'2026-11-12 09:21:57','2026-11-12 09:21:57'),(2,'What is the return and exchange window?','We provide a 7-day complimentary return or size exchange window from the date of physical delivery for all unworn garments with original designer tags intact.','Returns & Exchanges',2,1,'2026-11-12 09:21:57','2026-11-12 09:21:57'),(3,'Can I request custom silhouette alterations before dispatch?','Yes, during checkout or immediately after placing your order, you can contact our concierge with your bust, waist, and length measurements for complimentary basic tailoring.','Atelier & Customization',3,1,'2026-11-12 09:21:57','2026-11-12 09:21:57'),(4,'Are B2B GST tax invoices available for wholesale or corporate purchases?','Yes. Simply enter your company name and GSTIN during the checkout step or contact our finance desk to receive an official GST tax invoice with input credit claims.','Payments & Billing',4,1,'2026-11-12 09:21:57','2026-11-12 09:21:57');
/*!40000 ALTER TABLE `faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_logs`
--

DROP TABLE IF EXISTS `inventory_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_variant_id` bigint unsigned NOT NULL,
  `sku` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_qty` int NOT NULL,
  `new_stock` int NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual_adjustment',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_logs_product_variant_id_foreign` (`product_variant_id`),
  KEY `inventory_logs_created_by_foreign` (`created_by`),
  KEY `inventory_logs_sku_index` (`sku`),
  CONSTRAINT `inventory_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_logs_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_logs`
--

LOCK TABLES `inventory_logs` WRITE;
/*!40000 ALTER TABLE `inventory_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kb_articles`
--

DROP TABLE IF EXISTS `kb_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kb_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `views_count` int unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `author_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kb_articles_slug_unique` (`slug`),
  KEY `kb_articles_author_id_foreign` (`author_id`),
  KEY `kb_articles_category_id_is_published_index` (`category_id`,`is_published`),
  CONSTRAINT `kb_articles_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kb_articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `kb_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kb_articles`
--

LOCK TABLES `kb_articles` WRITE;
/*!40000 ALTER TABLE `kb_articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `kb_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kb_categories`
--

DROP TABLE IF EXISTS `kb_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kb_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 0xF09F939A,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kb_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kb_categories`
--

LOCK TABLES `kb_categories` WRITE;
/*!40000 ALTER TABLE `kb_categories` DISABLE KEYS */;
INSERT INTO `kb_categories` VALUES (1,'Shipping & Delivery','shipping-delivery','Delivery timelines, domestic courier tracking, and white-glove doorstep delivery.','📦',1,1,'2026-11-12 09:21:57','2026-11-12 09:21:57'),(2,'Returns & Exchanges','returns-exchanges','7-day hassle-free returns policy, exchange process, and refund settlement schedules.','🔄',2,1,'2026-11-12 09:21:57','2026-11-12 09:21:57'),(3,'Bespoke Sizing & Fit','sizing-fit','How to take measurements, international sizing conversions, and custom alterations.','📏',3,1,'2026-11-12 09:21:57','2026-11-12 09:21:57'),(4,'Garment Care & Fabric Maintenance','garment-care','Dry cleaning recommendations, silk and velvet preservation, and embroidery care.','✨',4,1,'2026-11-12 09:21:57','2026-11-12 09:21:57'),(5,'Payments & Security','payments-security','Credit card safety, UPI, EMI installment options, and B2B GST tax invoices.','💳',5,1,'2026-11-12 09:21:57','2026-11-12 09:21:57');
/*!40000 ALTER TABLE `kb_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_accounts`
--

DROP TABLE IF EXISTS `loyalty_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loyalty_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `loyalty_tier_id` bigint unsigned NOT NULL,
  `points_balance` int NOT NULL DEFAULT '0',
  `points_earned_lifetime` int NOT NULL DEFAULT '0',
  `points_redeemed_lifetime` int NOT NULL DEFAULT '0',
  `lifetime_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loyalty_accounts_user_id_unique` (`user_id`),
  KEY `loyalty_accounts_loyalty_tier_id_foreign` (`loyalty_tier_id`),
  KEY `loyalty_accounts_points_balance_loyalty_tier_id_index` (`points_balance`,`loyalty_tier_id`),
  CONSTRAINT `loyalty_accounts_loyalty_tier_id_foreign` FOREIGN KEY (`loyalty_tier_id`) REFERENCES `loyalty_tiers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loyalty_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_accounts`
--

LOCK TABLES `loyalty_accounts` WRITE;
/*!40000 ALTER TABLE `loyalty_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `loyalty_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_tiers`
--

DROP TABLE IF EXISTS `loyalty_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loyalty_tiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `points_multiplier` decimal(3,2) NOT NULL DEFAULT '1.00',
  `perks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `badge_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#94a3b8',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loyalty_tiers_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_tiers`
--

LOCK TABLES `loyalty_tiers` WRITE;
/*!40000 ALTER TABLE `loyalty_tiers` DISABLE KEYS */;
INSERT INTO `loyalty_tiers` VALUES (1,'Silver Member','silver',0.00,1.00,'1 Point per ₹50 spent. Standard checkout redemption. Seasonal member newsletters.','#94a3b8',1,'2026-11-12 09:21:55','2026-11-12 09:21:55'),(2,'Gold VIP','gold',15000.00,1.25,'1.25x point multiplier. Free express courier delivery on all orders. ₹500 Birthday reward coupon.','#f59e0b',1,'2026-11-12 09:21:55','2026-11-12 09:21:55'),(3,'Platinum Elite','platinum',40000.00,1.50,'1.50x point multiplier. Priority same-day atelier dispatch. Private WhatsApp concierge styling.','#8b5cf6',1,'2026-11-12 09:21:55','2026-11-12 09:21:55'),(4,'Diamond Patron','diamond',100000.00,2.00,'2.0x points multiplier. Complimentary satin packaging with every shipment. Exclusive runway previews.','#06b6d4',1,'2026-11-12 09:21:55','2026-11-12 09:21:55');
/*!40000 ALTER TABLE `loyalty_tiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_transactions`
--

DROP TABLE IF EXISTS `loyalty_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loyalty_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `loyalty_account_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `type` enum('earned','redeemed','adjusted','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'earned',
  `points` int NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loyalty_transactions_order_id_foreign` (`order_id`),
  KEY `loyalty_transactions_loyalty_account_id_created_at_index` (`loyalty_account_id`,`created_at`),
  CONSTRAINT `loyalty_transactions_loyalty_account_id_foreign` FOREIGN KEY (`loyalty_account_id`) REFERENCES `loyalty_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loyalty_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_transactions`
--

LOCK TABLES `loyalty_transactions` WRITE;
/*!40000 ALTER TABLE `loyalty_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `loyalty_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_01_000000_create_categories_table',1),(5,'2026_01_01_000001_create_products_table',1),(6,'2026_01_01_000002_create_product_colors_table',1),(7,'2026_01_01_000003_create_product_variants_table',1),(8,'2026_01_01_000004_create_templates_table',1),(9,'2026_01_01_000005_create_orders_table',1),(10,'2026_01_01_000006_create_order_items_table',1),(11,'2026_01_01_000007_create_settings_table',1),(12,'2026_01_01_000008_create_coupons_table',1),(13,'2026_01_01_000009_add_coupon_code_to_orders_table',1),(14,'2026_01_01_000010_create_customer_addresses_table',1),(15,'2026_01_01_000011_create_order_requests_table',1),(16,'2026_01_01_000012_create_pincode_zones_table',1),(17,'2026_01_01_000013_create_product_reviews_table',1),(18,'2026_01_01_000014_create_subscribers_table',1),(19,'2026_01_01_000015_add_sku_to_products_table',1),(20,'2026_01_01_000016_create_wishlists_table',1),(21,'2026_01_01_000017_create_addons_table',1),(22,'2026_01_01_000018_create_inventory_logs_table',1),(23,'2026_01_01_000019_add_sku_to_order_items_table',1),(24,'2026_01_01_000020_add_deactivated_at_to_users_table',1),(25,'2026_01_01_000021_add_pos_fields_to_orders_table',1),(26,'2026_01_01_000022_create_suppliers_and_purchase_orders_tables',1),(27,'2026_01_01_000023_create_accounting_sync_logs_table',1),(28,'2026_01_01_000024_create_sms_logs_and_otp_tables',1),(29,'2026_01_01_000025_create_product_batches_and_allocations_tables',1),(30,'2026_01_01_000026_create_roles_and_permissions_tables',1),(31,'2026_01_01_000027_create_push_subscriptions_and_campaigns_tables',1),(32,'2026_01_01_000028_create_outlets_and_stock_transfers_tables',1),(33,'2026_01_01_000029_create_custom_pages_table',1),(34,'2026_01_01_000030_create_delivery_personnel_and_order_dispatch_tables',1),(35,'2026_01_01_000031_add_long_description_to_products_table',1),(36,'2026_01_01_000032_add_parent_id_to_categories_table',1),(37,'2026_01_01_000033_add_image_to_categories_table',1),(38,'2026_01_01_000034_create_attributes_and_product_values_tables',1),(39,'2026_01_01_000035_create_customer_groups_and_user_pivot_tables',1),(40,'2026_01_01_000036_create_supplier_invoices_and_payments_tables',1),(41,'2026_01_01_000037_create_purchase_returns_and_items_tables',1),(42,'2026_01_01_000038_create_pos_shifts_and_movements_tables',1),(43,'2026_01_01_000039_create_expenses_and_expense_categories_tables',1),(44,'2026_01_01_000040_create_commercial_invoices_and_items_tables',1),(45,'2026_01_01_000041_create_abandoned_carts_table',1),(46,'2026_01_01_000042_create_loyalty_program_tables',1),(47,'2026_01_01_000043_create_payroll_tables',1),(48,'2026_01_01_000044_create_performance_management_tables',1),(49,'2026_01_01_000045_create_departments_and_shifts_tables',1),(50,'2026_01_01_000046_create_staff_attendances_table',1),(51,'2026_01_01_000047_create_support_tickets_tables',1),(52,'2026_01_01_000048_create_knowledge_base_tables',1),(53,'2026_01_01_000049_create_faqs_table',1),(54,'2026_01_01_000050_add_staff_fields_to_users_table',1),(55,'2026_01_01_000051_add_city_to_product_reviews_table',1),(56,'2026_01_01_000052_add_otp_fields_to_users_table',1),(57,'2026_01_01_000053_create_brands_and_addon_product_tables',1),(58,'2026_01_01_000054_add_two_factor_columns_to_users_table',1),(59,'2026_11_08_110155_add_cover_image_to_product_colors_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_item_batches`
--

DROP TABLE IF EXISTS `order_item_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_item_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint unsigned NOT NULL,
  `product_batch_id` bigint unsigned NOT NULL,
  `qty_allocated` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_item_batches_order_item_id_foreign` (`order_item_id`),
  KEY `order_item_batches_product_batch_id_foreign` (`product_batch_id`),
  CONSTRAINT `order_item_batches_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_item_batches_product_batch_id_foreign` FOREIGN KEY (`product_batch_id`) REFERENCES `product_batches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_item_batches`
--

LOCK TABLES `order_item_batches` WRITE;
/*!40000 ALTER TABLE `order_item_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_item_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_color_id` bigint unsigned DEFAULT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `sku` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int unsigned NOT NULL,
  `compare_at_price` int unsigned DEFAULT NULL,
  `qty` smallint unsigned NOT NULL,
  `line_total` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_order_variant` (`order_id`,`product_variant_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_product_color_id_foreign` (`product_color_id`),
  KEY `order_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_color_id_foreign` FOREIGN KEY (`product_color_id`) REFERENCES `product_colors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (46,1,8,9,22,'SARE-RATR-QBL7-BLA-S','Ratri Saree','ratri-saree','Black','#000000','S','http://nexon.test/images/products/women/ratri-saree-sare-ratr-qbl7-xv6wwm.avif',3299,3999,2,6598,'2026-11-12 08:57:02','2026-11-12 11:39:25'),(47,1,9,10,26,'LS-LAV-QINP-PAS-M','Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#c084fc','M','http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',3899,4599,1,3899,'2026-11-12 08:57:02','2026-11-12 11:39:25'),(48,2,9,10,25,'LS-LAV-QINP-PAS-S','Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#c084fc','S','http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',3899,4599,1,3899,'2026-11-12 06:57:02','2026-11-12 11:39:25'),(49,3,8,9,22,'SARE-RATR-QBL7-BLA-S','Ratri Saree','ratri-saree','Black','#000000','S','http://nexon.test/images/products/women/ratri-saree-sare-ratr-qbl7-xv6wwm.avif',3299,3999,1,3299,'2026-11-11 08:57:02','2026-11-12 11:39:25'),(50,3,9,10,26,'LS-LAV-QINP-PAS-M','Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#c084fc','M','http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',3899,4599,1,3899,'2026-11-11 08:57:02','2026-11-12 11:39:25'),(51,4,9,10,25,'LS-LAV-QINP-PAS-S','Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#c084fc','S','http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',3899,4599,2,7798,'2026-11-10 04:57:02','2026-11-12 11:39:25'),(52,5,8,9,22,'SARE-RATR-QBL7-BLA-S','Ratri Saree','ratri-saree','Black','#000000','S','http://nexon.test/images/products/women/ratri-saree-sare-ratr-qbl7-xv6wwm.avif',3299,3999,1,3299,'2026-11-07 06:57:02','2026-11-12 11:39:25'),(53,5,9,10,26,'LS-LAV-QINP-PAS-M','Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#c084fc','M','http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',3899,4599,1,3899,'2026-11-07 06:57:02','2026-11-12 11:39:25'),(54,6,9,10,25,'LS-LAV-QINP-PAS-S','Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#c084fc','S','http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',3899,4599,1,3899,'2026-11-05 09:57:02','2026-11-12 11:39:25'),(55,7,8,9,22,'SARE-RATR-QBL7-BLA-S','Ratri Saree','ratri-saree','Black','#000000','S','http://nexon.test/images/products/women/ratri-saree-sare-ratr-qbl7-xv6wwm.avif',3299,3999,2,6598,'2026-11-03 05:57:02','2026-11-12 11:39:25'),(56,7,9,10,26,'LS-LAV-QINP-PAS-M','Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#c084fc','M','http://nexon.test/images/products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',3899,4599,1,3899,'2026-11-03 05:57:02','2026-11-12 11:39:25');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_requests`
--

DROP TABLE IF EXISTS `order_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_requests_order_id_foreign` (`order_id`),
  KEY `order_requests_user_id_foreign` (`user_id`),
  CONSTRAINT `order_requests_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_requests`
--

LOCK TABLES `order_requests` WRITE;
/*!40000 ALTER TABLE `order_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `pos_cashier_id` bigint unsigned DEFAULT NULL,
  `reference` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` int unsigned NOT NULL,
  `shipping_charge` int unsigned NOT NULL DEFAULT '0',
  `cod_charge` int unsigned NOT NULL DEFAULT '0',
  `discount` int unsigned NOT NULL DEFAULT '0',
  `coupon_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` int unsigned NOT NULL,
  `tendered_amount` int unsigned DEFAULT NULL,
  `change_amount` int unsigned DEFAULT NULL,
  `payment_method` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_personnel_id` bigint unsigned DEFAULT NULL,
  `delivery_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unassigned',
  `proof_signature` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `proof_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_reference_unique` (`reference`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_pos_cashier_id_foreign` (`pos_cashier_id`),
  KEY `orders_channel_index` (`channel`),
  KEY `orders_delivery_personnel_id_foreign` (`delivery_personnel_id`),
  CONSTRAINT `orders_delivery_personnel_id_foreign` FOREIGN KEY (`delivery_personnel_id`) REFERENCES `delivery_personnel` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_pos_cashier_id_foreign` FOREIGN KEY (`pos_cashier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,11,NULL,'SAA-20261014-0001','online','pending',10497,50,40,0,NULL,10587,NULL,NULL,'cod','pending',NULL,'Ananya Kapoor','+91 98201 12345','ananya.k@gmail.com','Flat 402, Sea Green Apts, Juhu Tara Road','Opposite Palm Beach Hotel','Mumbai','Maharashtra','400049',NULL,NULL,NULL,NULL,'Please call before delivery. Ring bell twice.','2026-11-12 08:57:02','2026-11-12 11:39:25',2,'assigned',NULL,NULL),(2,11,NULL,'SAA-20261014-0002','online','shipped',3899,0,0,200,NULL,3699,NULL,NULL,'upi','paid','UPI/261014/98127391','Rhea Sen','+91 99302 98765','rhea.sen@outlook.com','Villa 12, Sobha Malabar, Lavelle Road','Near Cubbon Park','Bengaluru','Karnataka','560001',NULL,NULL,'2026-11-12 08:57:05',NULL,'Leave with concierge if not available.','2026-11-12 06:57:02','2026-11-12 11:39:25',1,'out_for_delivery',NULL,NULL),(3,NULL,NULL,'SAA-20261013-0001','online','delivered',7198,0,0,0,NULL,7198,NULL,NULL,'card','paid','TXN_VISA_9831411','Kavita Verma','+91 97110 54321','kavita.v@yahoo.com','B-14, Defence Colony',NULL,'New Delhi','Delhi','110024','BD98231049IN','BlueDart Express',NULL,'2026-11-12 06:57:05','Gift packaging requested.','2026-11-11 08:57:02','2026-11-12 11:39:25',3,'delivered','Mrs. Radhika Merchant','Package handed directly to concierge reception with boutique gift wrap intact.'),(4,11,NULL,'SAA-20261012-0001','online','shipped',7798,0,0,300,NULL,7498,NULL,NULL,'upi','paid','UPI/261012/11094821','Sneha Mukherjee','+91 98305 77123','sneha.m@gmail.com','44/2 Ballygunge Circular Road','Opposite Ice Skating Rink','Kolkata','West Bengal','700019','DEL192837465','Delhivery Surface','2026-11-10 05:57:02',NULL,NULL,'2026-11-10 04:57:02','2026-11-12 11:39:25',NULL,'unassigned',NULL,NULL),(5,NULL,NULL,'SAA-20261010-0001','online','delivered',7198,0,0,0,NULL,7198,NULL,NULL,'card','paid','TXN_MC_49201948','Natasha D\'Souza','+91 98221 44556','natasha.ds@gmail.com','Row House 4, Silver Sands Estate, Candolim','Near Lemon Tree Amarante','North Goa','Goa','403515','BD77441199IN','BlueDart Air','2026-11-08 03:57:02','2026-11-09 10:57:02','Delivered safely at front desk.','2026-11-07 06:57:02','2026-11-12 11:39:25',NULL,'unassigned',NULL,NULL),(6,11,NULL,'SAA-20261008-0001','online','delivered',3899,50,40,0,NULL,3989,NULL,NULL,'cod','paid',NULL,'Meera Joshi','+91 94230 66789','meera.joshi@gmail.com','Plot 89, Kalyani Nagar','Lane 4, Near Joggers Park','Pune','Maharashtra','411006','DTDC66554433','DTDC Express','2026-11-06 04:57:02','2026-11-07 08:57:02','Cash collected on delivery.','2026-11-05 09:57:02','2026-11-12 11:39:25',NULL,'unassigned',NULL,NULL),(7,NULL,NULL,'SAA-20261005-0001','online','cancelled',10497,0,0,0,NULL,10497,NULL,NULL,'upi','refunded','UPI/261005/77889900','Tara Singhania','+91 98199 00112','tara.singh@gmail.com','101, Golf Links Tower','Near DLF Cyber City','Gurugram','Haryana','122002',NULL,NULL,NULL,NULL,'Customer cancelled prior to dispatch. Refund processed.','2026-11-03 05:57:02','2026-11-12 11:39:25',NULL,'unassigned',NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outlet_stocks`
--

DROP TABLE IF EXISTS `outlet_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outlet_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `shelf_bin_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outlet_stocks_outlet_id_product_variant_id_unique` (`outlet_id`,`product_variant_id`),
  KEY `outlet_stocks_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `outlet_stocks_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `outlet_stocks_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outlet_stocks`
--

LOCK TABLES `outlet_stocks` WRITE;
/*!40000 ALTER TABLE `outlet_stocks` DISABLE KEYS */;
INSERT INTO `outlet_stocks` VALUES (85,1,22,10,'RACK-A-1','2026-11-12 11:40:29','2026-11-12 11:40:29'),(86,2,22,14,'RACK-B-1','2026-11-12 11:40:29','2026-11-12 11:40:29'),(87,3,22,18,'RACK-C-1','2026-11-12 11:40:29','2026-11-12 11:40:29'),(88,1,23,13,'RACK-A-2','2026-11-12 11:40:29','2026-11-12 11:40:29'),(89,2,23,17,'RACK-B-2','2026-11-12 11:40:29','2026-11-12 11:40:29'),(90,3,23,21,'RACK-C-2','2026-11-12 11:40:29','2026-11-12 11:40:29'),(91,1,24,16,'RACK-A-3','2026-11-12 11:40:29','2026-11-12 11:40:29'),(92,2,24,20,'RACK-B-3','2026-11-12 11:40:29','2026-11-12 11:40:29'),(93,3,24,24,'RACK-C-3','2026-11-12 11:40:29','2026-11-12 11:40:29'),(94,1,25,10,'RACK-A-1','2026-11-12 11:40:29','2026-11-12 11:40:29'),(95,2,25,14,'RACK-B-1','2026-11-12 11:40:29','2026-11-12 11:40:29'),(96,3,25,18,'RACK-C-1','2026-11-12 11:40:29','2026-11-12 11:40:29'),(97,1,26,13,'RACK-A-2','2026-11-12 11:40:29','2026-11-12 11:40:29'),(98,2,26,17,'RACK-B-2','2026-11-12 11:40:29','2026-11-12 11:40:29'),(99,3,26,21,'RACK-C-2','2026-11-12 11:40:29','2026-11-12 11:40:29'),(100,1,27,16,'RACK-A-3','2026-11-12 11:40:29','2026-11-12 11:40:29'),(101,2,27,20,'RACK-B-3','2026-11-12 11:40:29','2026-11-12 11:40:29'),(102,3,27,24,'RACK-C-3','2026-11-12 11:40:29','2026-11-12 11:40:29');
/*!40000 ALTER TABLE `outlet_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outlets`
--

DROP TABLE IF EXISTS `outlets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outlets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'store',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_central` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `channel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outlets_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outlets`
--

LOCK TABLES `outlets` WRITE;
/*!40000 ALTER TABLE `outlets` DISABLE KEYS */;
INSERT INTO `outlets` VALUES (1,'Colaba Flagship Boutique','MUM-COL-01','store','Ground Floor, Regal Circle, Colaba Causeway, Mumbai 400001','Mumbai','+91 22 2202 1100','colaba@nexon.com',1,1,'all','2026-11-12 10:57:05','2026-11-12 10:57:05'),(2,'Indiranagar Experience Studio','BLR-IND-02','store','100ft Road, HAL 2nd Stage, Indiranagar, Bengaluru 560038','Bengaluru','+91 80 4112 3344','bengaluru@nexon.com',0,1,'pos','2026-11-12 10:57:05','2026-11-12 10:57:05'),(3,'Bhiwandi Central Fulfillment Hub','BHW-LOG-03','warehouse','Warehouse Bay 14, Mumbai-Nashik Expressway, Bhiwandi 421302','Bhiwandi','+91 2522 667788','warehouse@nexon.com',0,1,'online','2026-11-12 10:57:05','2026-11-12 10:57:05');
/*!40000 ALTER TABLE `outlets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_items`
--

DROP TABLE IF EXISTS `payroll_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `payslip_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Retail Associate',
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Retail Store Sales',
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'HDFC Bank',
  `bank_account_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_ifsc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'HDFC0000123',
  `pan_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uan_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `working_days` smallint unsigned NOT NULL DEFAULT '30',
  `present_days` decimal(4,1) NOT NULL DEFAULT '30.0',
  `leave_days` decimal(4,1) NOT NULL DEFAULT '0.0',
  `basic_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transport_allowance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `performance_bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gross_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pf_deduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `esi_deduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tds_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','approved','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `payment_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_items_payslip_number_unique` (`payslip_number`),
  KEY `payroll_items_user_id_foreign` (`user_id`),
  KEY `payroll_items_payroll_id_user_id_index` (`payroll_id`,`user_id`),
  CONSTRAINT `payroll_items_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_items`
--

LOCK TABLES `payroll_items` WRITE;
/*!40000 ALTER TABLE `payroll_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_month` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_gross` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_allowances` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_net` decimal(12,2) NOT NULL DEFAULT '0.00',
  `employees_count` int unsigned NOT NULL DEFAULT '0',
  `status` enum('draft','reviewed','approved','disbursed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `disbursed_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payrolls_payroll_code_unique` (`payroll_code`),
  KEY `payrolls_approved_by_foreign` (`approved_by`),
  KEY `payrolls_created_by_foreign` (`created_by`),
  KEY `payrolls_period_month_status_index` (`period_month`,`status`),
  CONSTRAINT `payrolls_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payrolls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`),
  KEY `permissions_group_name_index` (`group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'View Inventory & Stock','inventory.view','inventory','2026-11-12 10:57:04','2026-11-12 10:57:04'),(2,'Manage Inventory & Adjust Stock','inventory.manage','inventory','2026-11-12 10:57:04','2026-11-12 10:57:04'),(3,'View Demand Forecasting','inventory.forecasting','inventory','2026-11-12 10:57:04','2026-11-12 10:57:04'),(4,'Manage Batches & FIFO Expiry','inventory.batches','inventory','2026-11-12 10:57:04','2026-11-12 10:57:04'),(5,'Access POS Cashier Terminal','pos.access','pos','2026-11-12 10:57:04','2026-11-12 10:57:04'),(6,'Execute POS Orders & Tender Cash','pos.checkout','pos','2026-11-12 10:57:04','2026-11-12 10:57:04'),(7,'View Orders List & Details','orders.view','orders','2026-11-12 10:57:04','2026-11-12 10:57:04'),(8,'Update Order Status & Tracking','orders.manage','orders','2026-11-12 10:57:04','2026-11-12 10:57:04'),(9,'Export Orders CSV','orders.export','orders','2026-11-12 10:57:04','2026-11-12 10:57:04'),(10,'View Products Catalog','products.view','products','2026-11-12 10:57:04','2026-11-12 10:57:04'),(11,'Create & Edit Products & SKUs','products.manage','products','2026-11-12 10:57:04','2026-11-12 10:57:04'),(12,'View Financial & Tax Reports','reports.view','reports','2026-11-12 10:57:04','2026-11-12 10:57:04'),(13,'Export Reports CSV & PDF','reports.export','reports','2026-11-12 10:57:04','2026-11-12 10:57:04'),(14,'Configure General Store Settings','settings.manage','settings','2026-11-12 10:57:04','2026-11-12 10:57:04'),(15,'Manage Payment Gateways','settings.gateways','settings','2026-11-12 10:57:04','2026-11-12 10:57:04'),(16,'Manage SMS & OTP Gateways','settings.sms','settings','2026-11-12 10:57:04','2026-11-12 10:57:04'),(17,'Manage Staff Roles & Permissions','roles.manage','roles','2026-11-12 10:57:04','2026-11-12 10:57:04');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_otps`
--

DROP TABLE IF EXISTS `phone_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_otps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `phone_otps_phone_index` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_otps`
--

LOCK TABLES `phone_otps` WRITE;
/*!40000 ALTER TABLE `phone_otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pincode_zones`
--

DROP TABLE IF EXISTS `pincode_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pincode_zones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pincode` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod_available` tinyint(1) NOT NULL DEFAULT '1',
  `delivery_days` smallint unsigned NOT NULL DEFAULT '3',
  `is_serviceable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pincode_zones_pincode_unique` (`pincode`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pincode_zones`
--

LOCK TABLES `pincode_zones` WRITE;
/*!40000 ALTER TABLE `pincode_zones` DISABLE KEYS */;
INSERT INTO `pincode_zones` VALUES (1,'400001','Mumbai','Maharashtra',1,2,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(2,'400050','Mumbai','Maharashtra',1,2,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(3,'110001','New Delhi','Delhi',1,2,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(4,'560001','Bengaluru','Karnataka',1,3,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(5,'700001','Kolkata','West Bengal',1,3,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(6,'600001','Chennai','Tamil Nadu',1,3,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(7,'500001','Hyderabad','Telangana',1,3,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(8,'380001','Ahmedabad','Gujarat',1,3,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(9,'302001','Jaipur','Rajasthan',1,3,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(10,'226001','Lucknow','Uttar Pradesh',1,4,1,'2026-11-12 10:57:03','2026-11-12 10:57:03'),(11,'411001','Pune','Maharashtra',1,2,1,'2026-11-12 10:57:03','2026-11-12 10:57:03');
/*!40000 ALTER TABLE `pincode_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_shift_movements`
--

DROP TABLE IF EXISTS `pos_shift_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_shift_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pos_shift_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('cash_in','cash_out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_shift_movements_pos_shift_id_foreign` (`pos_shift_id`),
  KEY `pos_shift_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `pos_shift_movements_pos_shift_id_foreign` FOREIGN KEY (`pos_shift_id`) REFERENCES `pos_shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pos_shift_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_shift_movements`
--

LOCK TABLES `pos_shift_movements` WRITE;
/*!40000 ALTER TABLE `pos_shift_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `pos_shift_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_shifts`
--

DROP TABLE IF EXISTS `pos_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shift_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cashier_id` bigint unsigned NOT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `status` enum('open','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `opening_float` decimal(10,2) NOT NULL DEFAULT '0.00',
  `expected_cash` decimal(10,2) NOT NULL DEFAULT '0.00',
  `counted_cash` decimal(10,2) DEFAULT NULL,
  `cash_difference` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cash_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `card_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `upi_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_orders_count` int unsigned NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_shifts_shift_code_unique` (`shift_code`),
  KEY `pos_shifts_cashier_id_foreign` (`cashier_id`),
  KEY `pos_shifts_status_index` (`status`),
  CONSTRAINT `pos_shifts_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_shifts`
--

LOCK TABLES `pos_shifts` WRITE;
/*!40000 ALTER TABLE `pos_shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `pos_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attribute_values`
--

DROP TABLE IF EXISTS `product_attribute_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attribute_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `attribute_id` bigint unsigned NOT NULL,
  `value_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `value_number` decimal(12,4) DEFAULT NULL,
  `value_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_attribute_values_attribute_id_foreign` (`attribute_id`),
  KEY `product_attribute_values_product_id_attribute_id_index` (`product_id`,`attribute_id`),
  CONSTRAINT `product_attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attribute_values_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attribute_values`
--

LOCK TABLES `product_attribute_values` WRITE;
/*!40000 ALTER TABLE `product_attribute_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attribute_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_batches`
--

DROP TABLE IF EXISTS `product_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_variant_id` bigint unsigned NOT NULL,
  `batch_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lot_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mfg_date` date DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `initial_qty` int unsigned NOT NULL,
  `remaining_qty` int unsigned NOT NULL DEFAULT '0',
  `cost_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_batches_product_variant_id_foreign` (`product_variant_id`),
  KEY `product_batches_batch_number_index` (`batch_number`),
  KEY `product_batches_lot_number_index` (`lot_number`),
  KEY `product_batches_expiry_date_index` (`expiry_date`),
  KEY `product_batches_remaining_qty_index` (`remaining_qty`),
  KEY `product_batches_status_index` (`status`),
  CONSTRAINT `product_batches_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_batches`
--

LOCK TABLES `product_batches` WRITE;
/*!40000 ALTER TABLE `product_batches` DISABLE KEYS */;
INSERT INTO `product_batches` VALUES (23,22,'BATCH-SARE-RATR-QBL7-BLA-S','LOT-2026-NEX-22','2026-10-12','2028-11-12',50,35,1200.00,'active','Passed master inspection & gold foil packaging verification.','2026-11-12 11:40:29','2026-11-12 11:40:29'),(24,23,'BATCH-SARE-RATR-QBL7-BLA-M','LOT-2026-NEX-23','2026-10-12','2028-11-12',50,35,1200.00,'active','Passed master inspection & gold foil packaging verification.','2026-11-12 11:40:29','2026-11-12 11:40:29'),(25,24,'BATCH-SARE-RATR-QBL7-BLA-L','LOT-2026-NEX-24','2026-10-12','2028-11-12',50,35,1200.00,'active','Passed master inspection & gold foil packaging verification.','2026-11-12 11:40:29','2026-11-12 11:40:29'),(26,25,'BATCH-LS-LAV-QINP-PAS-S','LOT-2026-NEX-25','2026-10-12','2028-11-12',50,35,1200.00,'active','Passed master inspection & gold foil packaging verification.','2026-11-12 11:40:29','2026-11-12 11:40:29'),(27,26,'BATCH-LS-LAV-QINP-PAS-M','LOT-2026-NEX-26','2026-10-12','2028-11-12',50,35,1200.00,'active','Passed master inspection & gold foil packaging verification.','2026-11-12 11:40:29','2026-11-12 11:40:29'),(28,27,'BATCH-LS-LAV-QINP-PAS-L','LOT-2026-NEX-27','2026-10-12','2028-11-12',50,35,1200.00,'active','Passed master inspection & gold foil packaging verification.','2026-11-12 11:40:29','2026-11-12 11:40:29');
/*!40000 ALTER TABLE `product_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_color_images`
--

DROP TABLE IF EXISTS `product_color_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_color_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_color_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_color_images_product_color_id_foreign` (`product_color_id`),
  CONSTRAINT `product_color_images_product_color_id_foreign` FOREIGN KEY (`product_color_id`) REFERENCES `product_colors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_color_images`
--

LOCK TABLES `product_color_images` WRITE;
/*!40000 ALTER TABLE `product_color_images` DISABLE KEYS */;
INSERT INTO `product_color_images` VALUES (13,9,'products/women/ratri-saree-sare-ratr-qbl7-m57o1h.avif',0,'2026-11-12 11:32:49','2026-11-12 12:12:15'),(14,9,'products/women/ratri-saree-sare-ratr-qbl7-q6luj5.avif',1,'2026-11-12 11:32:49','2026-11-12 12:12:15'),(15,9,'products/women/ratri-saree-sare-ratr-qbl7-if7gy5.avif',2,'2026-11-12 11:32:50','2026-11-12 12:12:15'),(16,9,'products/women/ratri-saree-sare-ratr-qbl7-llwebu.avif',3,'2026-11-12 11:32:50','2026-11-12 12:12:15'),(17,9,'products/women/ratri-saree-sare-ratr-qbl7-8eziek.avif',4,'2026-11-12 11:32:50','2026-11-12 12:12:15'),(18,9,'products/women/ratri-saree-sare-ratr-qbl7-bixwpd.avif',5,'2026-11-12 11:32:50','2026-11-12 12:12:15'),(19,9,'products/women/ratri-saree-sare-ratr-qbl7-h3segy.avif',6,'2026-11-12 11:32:50','2026-11-12 12:12:15'),(20,10,'products/men/lounge-pajama-set-ls-lav-qinp-wgybrn.avif',0,'2026-11-12 11:33:54','2026-11-12 11:33:54'),(21,10,'products/men/lounge-pajama-set-ls-lav-qinp-lbfglg.avif',1,'2026-11-12 11:33:54','2026-11-12 11:33:54');
/*!40000 ALTER TABLE `product_color_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_colors`
--

DROP TABLE IF EXISTS `product_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_colors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hex` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_colors_product_id_name_unique` (`product_id`,`name`),
  CONSTRAINT `product_colors_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_colors`
--

LOCK TABLES `product_colors` WRITE;
/*!40000 ALTER TABLE `product_colors` DISABLE KEYS */;
INSERT INTO `product_colors` VALUES (9,8,'Black','#000000','products/women/ratri-saree-sare-ratr-qbl7-xv6wwm.avif',0,'2026-11-12 10:57:02','2026-11-12 11:32:49'),(10,9,'Pastel Lavender','#c084fc','products/men/lounge-pajama-set-ls-lav-qinp-w8gb8m.avif',0,'2026-11-12 10:57:02','2026-11-12 11:33:53');
/*!40000 ALTER TABLE `product_colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_foreign` (`product_id`),
  KEY `product_reviews_user_id_foreign` (`user_id`),
  CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (23,8,11,'Priya Sharma','New Delhi',5,'Pure handwoven heirloom','The handwoven tussar-by-cotton fabric carries a royal weight and delicate drape. The jaali embroidery on the pallu was the highlight of our Nobomi reception.',1,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(24,8,NULL,'Sunita Kulkarni','Mumbai',5,'Exquisite craftsmanship & lace detail','The deep black color depth paired with antique zari embroidery is majestic. Arrived in regal packaging with scented satin lining.',1,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(25,8,NULL,'Ananya Patel','Bengaluru',5,'Stunning Nobomi saree','Wore this Ratri Saree for a family gala. Received endless compliments on the handcrafted finish and intricate lace borders.',1,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(26,8,NULL,'Meera Sen','Kolkata',5,'Collector’s edition quality','Breathable, lightweight, and incredibly rich in visual texture. Authentic artisanal craftsmanship from start to finish.',1,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(27,9,NULL,'Dr. Rajesh Menon','Pune',5,'Pure mulberry silk luxury','The softest lavender silk lounge pajama set. Unbelievably comfortable for leisure evenings and sleeps like a dream.',1,'2026-11-12 11:40:29','2026-11-12 11:40:29'),(28,9,NULL,'Rohan Mehta','Mumbai',5,'Flawless tailoring & fit','Tailoring is sharp, fabric feels divine against the skin, and the elastic waistband provides relaxed comfort all night.',1,'2026-11-12 11:40:29','2026-11-12 11:40:29');
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `product_color_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `compare_at_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `stock` int unsigned NOT NULL DEFAULT '0',
  `reorder_threshold` int unsigned NOT NULL DEFAULT '5',
  `reorder_qty` int unsigned NOT NULL DEFAULT '20',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_color_size` (`product_color_id`,`size`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  KEY `product_variants_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `product_variants_product_color_id_foreign` FOREIGN KEY (`product_color_id`) REFERENCES `product_colors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_variants_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (22,8,9,NULL,'S','SARE-RATR-QBL7-BLA-S',3299.00,3999.00,NULL,10,5,20,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(23,8,9,NULL,'M','SARE-RATR-QBL7-BLA-M',3299.00,3999.00,NULL,15,5,20,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(24,8,9,NULL,'L','SARE-RATR-QBL7-BLA-L',3499.00,3999.00,NULL,8,5,20,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(25,9,10,NULL,'S','LS-LAV-QINP-PAS-S',3899.00,4599.00,NULL,12,5,20,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(26,9,10,NULL,'M','LS-LAV-QINP-PAS-M',3899.00,4599.00,NULL,18,5,20,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(27,9,10,NULL,'L','LS-LAV-QINP-PAS-L',3899.00,4599.00,NULL,9,5,20,'2026-11-12 10:57:02','2026-11-12 10:57:02');
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `long_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `badge` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_bestseller` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (8,39,1,'Ratri Saree','ratri-saree','SARE-RATR-QBL7','<p><strong>Fabric:</strong> Handwoven tussar by cotton  |  <strong>Blouse Piece:</strong> No  |  <strong>Colour:</strong> Black</p><p>An ode to the magic of Nobomi night. Crafted in deep black handwoven Tussar-by-Cotton, carrying delicate embroidered motifs across its body, finished with intricate lace edging and a statement embroidered pallu.</p>','<div class=\"product-highlights mb-6\"><ul style=\"list-style: none; padding: 0; margin: 0;\" class=\"space-y-2 text-sm text-gray-700\"><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Works:</span><span style=\"font-weight: 600; color: #111827;\">Embroidery</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Fabric:</span><span style=\"font-weight: 600; color: #111827;\">Handwoven tussar by cotton</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Saree Length:</span><span style=\"font-weight: 600; color: #111827;\">5.5 meters</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Blouse Piece:</span><span style=\"font-weight: 600; color: #111827;\">No</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Colour:</span><span style=\"font-weight: 600; color: #111827;\">Black</span></li></ul></div><div class=\"story-box mb-6\" style=\"margin-top: 1.5rem;\"><h3 style=\"font-family: Georgia, serif; color: #1a1a1a; font-size: 1.5rem; margin-bottom: 1rem; border-left: 4px solid #d4af37; padding-left: 12px; line-height: 1.2;\">Story</h3><div style=\"padding-left: 4px;\"><p style=\"font-weight: 700; color: #990000; font-size: 1.1rem; margin-bottom: 0.75rem;\">RATRI | রাত্রী</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\">There’s something about <strong style=\"color: #990000;\">Nobomi night</strong>—the lights, the quiet after the chaos, the last few hours of Pujo that feel almost magical.</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\"><strong style=\"color: #990000;\">RATRI</strong> is an ode to that night.</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\">Crafted in a beautiful handwoven Tussar-by-Cotton, this deep black saree carries delicate embroidered <span style=\"color: #990000; font-weight: 600;\">motifs</span> across its body, finished with intricate lace edging and a statement embroidered pallu.</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\">Understated yet dramatic, traditional yet effortlessly contemporary—<strong style=\"color: #990000;\">RATRI</strong> is made for the woman who lets the night do the talking.</p><p style=\"margin-top: 1rem; font-style: italic; color: #990000; font-weight: 600;\">RATRI — An ode to the night. A final chapter of Pujo.</p></div></div><div class=\"care-and-disclaimer\" style=\"margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;\"><div style=\"margin-bottom: 1rem;\"><span style=\"font-size: 0.95rem; font-weight: 700; color: #111827; display: block; margin-bottom: 0.25rem;\">Wash &amp; Care:</span><p style=\"font-size: 0.875rem; color: #4b5563; margin: 0; line-height: 1.5;\">Dry clean only recommended to maintain the longevity of the fabric.</p></div><div style=\"margin-bottom: 1.25rem;\"><span style=\"font-size: 0.95rem; font-weight: 700; color: #111827; display: block; margin-bottom: 0.25rem;\">Production Time:</span><p style=\"font-size: 0.875rem; color: #4b5563; margin: 0; line-height: 1.5;\">Handcrafted artisanal saree. Please allow 10-12 days for production.</p></div><div style=\"background-color: #fff5f5; border-radius: 8px; padding: 14px; border-left: 4px solid #e53e3e;\"><span style=\"font-size: 0.9rem; font-weight: 700; display: block; margin-bottom: 6px; color: #c53030;\">Disclaimer:</span><ul style=\"font-size: 0.825rem; color: #4a5568; margin: 0; padding-left: 18px; line-height: 1.6;\"><li>Photos are captured in natural light to avoid discrepancies; minor shade variations may occur due to individual screen calibration.</li><li>For bespoke customizations, please contact our concierge team. Customized items cannot be returned or exchanged.</li></ul></div></div>','HOT',1,1,0,0,'2026-11-12 10:57:02','2026-11-12 12:12:15'),(9,12,NULL,'Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','LS-LAV-QINP','Two-piece tailored lounge set in calming pastel lavender mulberry silk with contrasting white piping.','','PREMIUM',1,0,1,0,'2026-11-12 10:57:02','2026-11-12 11:33:53');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned NOT NULL,
  `ordered_qty` int unsigned NOT NULL,
  `received_qty` int unsigned NOT NULL DEFAULT '0',
  `unit_cost` int unsigned NOT NULL DEFAULT '0',
  `line_total` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `purchase_order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_amount` int unsigned NOT NULL DEFAULT '0',
  `expected_delivery_date` date DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  KEY `purchase_orders_status_index` (`status`),
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_return_items`
--

DROP TABLE IF EXISTS `purchase_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned NOT NULL,
  `product_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  KEY `purchase_return_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `purchase_return_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_return_items`
--

LOCK TABLES `purchase_return_items` WRITE;
/*!40000 ALTER TABLE `purchase_return_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_returns`
--

DROP TABLE IF EXISTS `purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `debit_note_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `return_date` date NOT NULL,
  `reason` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','dispatched','credited','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_returns_debit_note_number_unique` (`debit_note_number`),
  KEY `purchase_returns_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_returns_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_returns_created_by_foreign` (`created_by`),
  KEY `purchase_returns_status_index` (`status`),
  CONSTRAINT `purchase_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_returns_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_returns`
--

LOCK TABLES `purchase_returns` WRITE;
/*!40000 ALTER TABLE `purchase_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_campaigns`
--

DROP TABLE IF EXISTS `push_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_count` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_campaigns`
--

LOCK TABLES `push_campaigns` WRITE;
/*!40000 ALTER TABLE `push_campaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_subscriptions`
--

DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `endpoint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `endpoint_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `p256dh_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `auth_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `device_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'browser',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_hash_unique` (`endpoint_hash`),
  KEY `push_subscriptions_user_id_foreign` (`user_id`),
  KEY `push_subscriptions_is_active_index` (`is_active`),
  CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_subscriptions`
--

LOCK TABLES `push_subscriptions` WRITE;
/*!40000 ALTER TABLE `push_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1),(2,1),(3,1),(5,1),(1,2),(2,2),(5,2),(1,3),(2,3),(5,3),(1,4),(2,4),(5,4),(1,5),(2,5),(4,5),(1,6),(2,6),(4,6),(1,7),(2,7),(3,7),(6,7),(1,8),(2,8),(3,8),(6,8),(1,9),(2,9),(3,9),(1,10),(2,10),(3,10),(4,10),(5,10),(6,10),(1,11),(2,11),(3,11),(1,12),(2,12),(3,12),(1,13),(2,13),(3,13),(1,14),(3,14),(1,15),(1,16),(1,17);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','super-admin','Full unrestricted system access to all store operations, finances, and settings.',1,'2026-11-12 10:57:04','2026-11-12 10:57:04'),(2,'Store Manager','store-manager','Comprehensive management of store items, catalog, inventory, orders, sales billing, POS checkout, and reports.',1,'2026-11-12 10:57:04','2026-11-12 10:57:04'),(3,'eCommerce Manager','ecommerce-manager','Oversees digital storefront operations, online catalog merchandising, web orders fulfillment, and eCommerce analytics.',1,'2026-11-12 10:57:04','2026-11-12 10:57:04'),(4,'POS Cashier','pos-cashier','In-store retail counter operator with access restricted to the POS Terminal.',1,'2026-11-12 10:57:04','2026-11-12 10:57:04'),(5,'Inventory Clerk','inventory-clerk','Warehouse specialist handling receiving, batch tracking, stock counts, and replenishment.',1,'2026-11-12 10:57:04','2026-11-12 10:57:04'),(6,'Customer Support','customer-support','Client assistance representative reviewing orders, shipments, and customer returns.',1,'2026-11-12 10:57:04','2026-11-12 10:57:04');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'free_shipping_threshold','1499','2026-11-12 10:57:02','2026-11-12 10:57:02'),(2,'shipping_cost','99','2026-11-12 10:57:02','2026-11-12 10:57:02'),(3,'cod_charge','40','2026-11-12 10:57:02','2026-11-12 10:57:02'),(4,'tax_percentage','18','2026-11-12 10:57:02','2026-11-12 10:57:02'),(5,'gstin','27AABCS1429B1ZB','2026-11-12 10:57:03','2026-11-12 10:57:03'),(6,'support_phone','+91 98765 43210','2026-11-12 10:57:03','2026-11-12 10:57:03'),(7,'support_whatsapp','+91 98765 43210','2026-11-12 10:57:03','2026-11-12 10:57:03'),(9,'store_address','101 Fashion Boulevard, Bandra West, Mumbai, Maharashtra 400050','2026-11-12 10:57:03','2026-11-12 10:57:03'),(10,'enable_cod','1','2026-11-12 10:57:03','2026-11-12 10:57:03'),(11,'maintenance_mode','0','2026-11-12 10:57:03','2026-11-12 10:57:03'),(12,'announcement_bar','Complimentary Shipping on all orders over ₹1,499 | Use code WELCOME10 for 10% off','2026-11-12 10:57:03','2026-11-12 10:57:03'),(13,'site_logo','branding/logo.png','2026-11-12 12:01:29','2026-11-12 12:01:29'),(14,'store_name','Nex-on','2026-11-12 12:01:29','2026-11-12 12:01:29'),(15,'footer_bio','','2026-11-12 12:01:29','2026-11-12 12:01:29'),(16,'footer_badge1','','2026-11-12 12:01:29','2026-11-12 12:01:29'),(17,'footer_badge2','','2026-11-12 12:01:29','2026-11-12 12:01:29'),(18,'vyra_hero_badge','Drop 03 / Out Now','2026-11-12 12:01:29','2026-11-12 12:01:29'),(19,'vyra_hero_title','Wear Your\r\nPower','2026-11-12 12:01:29','2026-11-12 12:01:29'),(20,'vyra_hero_subtitle','Bold cuts. Mesh, lace, and second-skin fabrics built for a generation that doesn\'t ask permission.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(21,'vyra_hero_btn_text','Shop Drop 03','2026-11-12 12:01:29','2026-11-12 12:01:29'),(22,'vyra_hero_btn_url','#products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(23,'vyra_curated_badge','Curated','2026-11-12 12:01:29','2026-11-12 12:01:29'),(24,'vyra_curated_title','Curated Featured Picks','2026-11-12 12:01:29','2026-11-12 12:01:29'),(25,'vyra_curated_subtitle','Second-skin fabrics, fearless cuts, and midnight noir lingerie engineered for an unfiltered presence.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(26,'vyra_sale_kicker','⚡ Limited Drops','2026-11-12 12:01:29','2026-11-12 12:01:29'),(27,'vyra_sale_title','Special Sale Offers','2026-11-12 12:01:29','2026-11-12 12:01:29'),(28,'vyra_sale_subtitle','High-impact power silhouettes, sculpting mesh, and daring cuts at exclusive promotional drops.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(29,'buttercup_slide_1_badge','New Season Edit','2026-11-12 12:01:29','2026-11-12 12:01:29'),(30,'buttercup_hero_badge','New Season Edit','2026-11-12 12:01:29','2026-11-12 12:01:29'),(31,'buttercup_slide_1_title','Comfort that\r\nFeels Like Home','2026-11-12 12:01:29','2026-11-12 12:01:29'),(32,'buttercup_hero_title','Comfort that\r\nFeels Like Home','2026-11-12 12:01:29','2026-11-12 12:01:29'),(33,'buttercup_slide_1_subtitle','Buttery-soft everyday lingerie, made for real bodies and real days — because comfort should never be a compromise.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(34,'buttercup_hero_subtitle','Buttery-soft everyday lingerie, made for real bodies and real days — because comfort should never be a compromise.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(35,'buttercup_slide_1_btn1_text','Shop Now','2026-11-12 12:01:29','2026-11-12 12:01:29'),(36,'buttercup_slide_1_btn1_url','#products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(37,'buttercup_slide_1_btn2_text','Explore','2026-11-12 12:01:29','2026-11-12 12:01:29'),(38,'buttercup_slide_1_btn2_url','https://nexon.test/search','2026-11-12 12:01:29','2026-11-12 12:01:29'),(39,'buttercup_slide_2_badge','Cloud-Soft Intimates','2026-11-12 12:01:29','2026-11-12 12:01:29'),(40,'buttercup_slide_2_title','Second-Skin Ease,\r\nAll Day Long','2026-11-12 12:01:29','2026-11-12 12:01:29'),(41,'buttercup_slide_2_subtitle','Breathable modal and organic cotton with zero pinch. Gentle, weightless support made to move with you.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(42,'buttercup_slide_2_btn1_text','Shop Intimates','2026-11-12 12:01:29','2026-11-12 12:01:29'),(43,'buttercup_slide_2_btn1_url','#products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(44,'buttercup_slide_2_btn2_text','View Collection','2026-11-12 12:01:29','2026-11-12 12:01:29'),(45,'buttercup_slide_2_btn2_url','https://nexon.test/featured-products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(46,'buttercup_slide_3_badge','Pure Indulgence','2026-11-12 12:01:29','2026-11-12 12:01:29'),(47,'buttercup_slide_3_title','Soft as a Whisper,\r\nCrafted for You','2026-11-12 12:01:29','2026-11-12 12:01:29'),(48,'buttercup_slide_3_subtitle','Delicate lace accents meet featherlight silhouettes. Experience the sublime touch of effortless elegance every day.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(49,'buttercup_slide_3_btn1_text','Explore Bestsellers','2026-11-12 12:01:29','2026-11-12 12:01:29'),(50,'buttercup_slide_3_btn1_url','#products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(51,'buttercup_slide_3_btn2_text','Discover More','2026-11-12 12:01:29','2026-11-12 12:01:29'),(52,'buttercup_slide_3_btn2_url','https://nexon.test/search','2026-11-12 12:01:29','2026-11-12 12:01:29'),(53,'buttercup_slide_4_badge','Wire-Free Freedom','2026-11-12 12:01:29','2026-11-12 12:01:29'),(54,'buttercup_slide_4_title','Zero Pinching,\r\n100% Pure Comfort','2026-11-12 12:01:29','2026-11-12 12:01:29'),(55,'buttercup_slide_4_subtitle','Say goodbye to painful underwires forever. Our contour bands adapt harmoniously to your natural curves.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(56,'buttercup_slide_4_btn1_text','Shop Wire-Free','2026-11-12 12:01:29','2026-11-12 12:01:29'),(57,'buttercup_slide_4_btn1_url','#products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(58,'buttercup_slide_4_btn2_text','Our Fit Guide','2026-11-12 12:01:29','2026-11-12 12:01:29'),(59,'buttercup_slide_4_btn2_url','https://nexon.test/search','2026-11-12 12:01:29','2026-11-12 12:01:29'),(60,'buttercup_slide_5_badge','Lounge & Sleep','2026-11-12 12:01:29','2026-11-12 12:01:29'),(61,'buttercup_slide_5_title','Dreamy Loungewear\r\nfor Cozy Evenings','2026-11-12 12:01:29','2026-11-12 12:01:29'),(62,'buttercup_slide_5_subtitle','From quiet morning coffee to tranquil midnight slumber — wrap yourself in the soothing bliss of buttery loungewear.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(63,'buttercup_slide_5_btn1_text','Shop Loungewear','2026-11-12 12:01:29','2026-11-12 12:01:29'),(64,'buttercup_slide_5_btn1_url','#products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(65,'buttercup_slide_5_btn2_text','Explore All','2026-11-12 12:01:29','2026-11-12 12:01:29'),(66,'buttercup_slide_5_btn2_url','https://nexon.test/featured-products','2026-11-12 12:01:29','2026-11-12 12:01:29'),(67,'buttercup_curated_badge','Curated','2026-11-12 12:01:29','2026-11-12 12:01:29'),(68,'buttercup_curated_title','Curated Featured Picks','2026-11-12 12:01:29','2026-11-12 12:01:29'),(69,'buttercup_curated_subtitle','Everyday cloud-soft intimates, wire-free comfort, and breathable cotton modal sets made for real bodies.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(70,'buttercup_sale_kicker','Limited Time Deals','2026-11-12 12:01:29','2026-11-12 12:01:29'),(71,'buttercup_sale_title','Special Sale Offers','2026-11-12 12:01:29','2026-11-12 12:01:29'),(72,'buttercup_sale_subtitle','Explore cloud-soft comfort sets and everyday silhouettes at celebratory sale prices.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(73,'lumiere_hero_badge','The Festive Edit','2026-11-12 12:01:29','2026-11-12 12:01:29'),(74,'lumiere_hero_title','Silk that\r\nfeels like light.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(75,'lumiere_hero_subtitle','Sustainably made silk lingerie, finished by hand in India. Everyday elegance, no compromise.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(76,'lumiere_curated_badge','Curated','2026-11-12 12:01:29','2026-11-12 12:01:29'),(77,'lumiere_curated_title','Curated Featured Picks','2026-11-12 12:01:29','2026-11-12 12:01:29'),(78,'lumiere_curated_subtitle','Hand-finished pure mulberry silk, delicate French lace, and heirloom silhouettes curated for effortless elegance.','2026-11-12 12:01:29','2026-11-12 12:01:29'),(79,'lumiere_sale_kicker','Limited Time Deals','2026-11-12 12:01:29','2026-11-12 12:01:29'),(80,'lumiere_sale_title','Special Sale Offers','2026-11-12 12:01:29','2026-11-12 12:01:29'),(81,'lumiere_sale_subtitle','Hand-finished French lace, fine silk, and fine nightwear at celebratory promotional prices.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(82,'saaya_hero_badge','Crafted for Her','2026-11-12 12:01:30','2026-11-12 12:01:30'),(83,'saaya_hero_title','Draped in\r\nGold & Grace','2026-11-12 12:01:30','2026-11-12 12:01:30'),(84,'saaya_hero_subtitle','Handcrafted Indian lingerie rooted in tradition, elevated by craft. Each piece is an heirloom waiting to be worn.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(85,'saaya_curated_badge','Curated','2026-11-12 12:01:30','2026-11-12 12:01:30'),(86,'saaya_curated_title','Curated Featured Picks','2026-11-12 12:01:30','2026-11-12 12:01:30'),(87,'saaya_curated_subtitle','Handcrafted heirlooms shaped with pure mulberry silk, delicate jaali embroidery, and timeless silhouettes.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(88,'saaya_sale_kicker','Limited Time Deals','2026-11-12 12:01:30','2026-11-12 12:01:30'),(89,'saaya_sale_title','Special Sale Offers','2026-11-12 12:01:30','2026-11-12 12:01:30'),(90,'saaya_sale_subtitle','Exclusive heirlooms and seasonal lingerie edits at celebratory promotional prices.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(91,'organic_hero_badge','🌱 100% Certified Farm-to-Table','2026-11-12 12:01:30','2026-11-12 12:01:30'),(92,'organic_hero_title','Organic Foods at your Doorsteps','2026-11-12 12:01:30','2026-11-12 12:01:30'),(93,'organic_hero_subtitle','Pure, wholesome produce cultivated by trusted Indian organic farmers. Zero harmful pesticides, zero synthetic chemicals—just real nutrition harvested daily.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(94,'organic_hero_btn1','Start Shopping','2026-11-12 12:01:30','2026-11-12 12:01:30'),(95,'organic_hero_btn2','Explore Featured','2026-11-12 12:01:30','2026-11-12 12:01:30'),(96,'organic_metric1_num','1,200+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(97,'organic_metric1_label','Organic Varieties','2026-11-12 12:01:30','2026-11-12 12:01:30'),(98,'organic_metric2_num','50k+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(99,'organic_metric2_label','Happy Patrons','2026-11-12 12:01:30','2026-11-12 12:01:30'),(100,'organic_metric3_num','100%','2026-11-12 12:01:30','2026-11-12 12:01:30'),(101,'organic_metric3_label','Purity Assured','2026-11-12 12:01:30','2026-11-12 12:01:30'),(102,'organic_prop1_title','Fresh from the Farm','2026-11-12 12:01:30','2026-11-12 12:01:30'),(103,'organic_prop1_desc','Harvested and dispatched within 24 hours for peak nutritional potency.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(104,'organic_prop2_title','100% Certified Organic','2026-11-12 12:01:30','2026-11-12 12:01:30'),(105,'organic_prop2_desc','Verified non-GMO, chemical-free crops certified under NPOP & FSSAI norms.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(106,'organic_prop3_title','Free Express Delivery','2026-11-12 12:01:30','2026-11-12 12:01:30'),(107,'organic_prop3_desc','Complimentary door delivery on orders over ₹1,499 with temperature control.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(108,'organic_ad1_kicker','Limited Batch','2026-11-12 12:01:30','2026-11-12 12:01:30'),(109,'organic_ad1_title','Raw Wild Forest Honey','2026-11-12 12:01:30','2026-11-12 12:01:30'),(110,'organic_ad1_desc','Unprocessed, pure nectar directly from Nilgiri biosphere reserves.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(111,'organic_ad1_btn','Shop Wild Honey','2026-11-12 12:01:30','2026-11-12 12:01:30'),(112,'organic_ad1_url','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(113,'organic_ad2_kicker','Seasonal Deal','2026-11-12 12:01:30','2026-11-12 12:01:30'),(114,'organic_ad2_title','Cold-Pressed Wood Oils','2026-11-12 12:01:30','2026-11-12 12:01:30'),(115,'organic_ad2_desc','Traditional Kolhu pressed mustard, sesame, and groundnut oils.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(116,'organic_ad2_btn','Explore Cold-Pressed','2026-11-12 12:01:30','2026-11-12 12:01:30'),(117,'organic_ad2_url','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(118,'organic_ad3_kicker','Daily Essentials','2026-11-12 12:01:30','2026-11-12 12:01:30'),(119,'organic_ad3_title','A2 Desi Gir Cow Ghee','2026-11-12 12:01:30','2026-11-12 12:01:30'),(120,'organic_ad3_desc','Bilona churned cultured butter from free-grazing indigenous cows.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(121,'organic_ad3_btn','Discover A2 Ghee','2026-11-12 12:01:30','2026-11-12 12:01:30'),(122,'organic_ad3_url','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(123,'organic_news_badge','🌱 Join the Organic Family','2026-11-12 12:01:30','2026-11-12 12:01:30'),(124,'organic_news_title','Get ₹200 Off Your First Fresh Harvest','2026-11-12 12:01:30','2026-11-12 12:01:30'),(125,'organic_news_subtitle','Subscribe to our weekly seasonal harvest alerts, farm updates, and exclusive patron discounts. No spam, just pure organic goodness.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(126,'organic_top_notice','100% Certified Organic • Direct from Local Farmers & Sustainable Harvests','2026-11-12 12:01:30','2026-11-12 12:01:30'),(127,'organic_top_shipping_notice','Delivery to All Major Indian Pincodes','2026-11-12 12:01:30','2026-11-12 12:01:30'),(128,'organic_customer_care','+91 98765 43210','2026-11-12 12:01:30','2026-11-12 12:01:30'),(130,'ticker_enabled','1','2026-11-12 12:01:30','2026-11-12 12:01:30'),(131,'ticker_item_1','Free Delivery on orders over <strong>₹1499</strong>','2026-11-12 12:01:30','2026-11-12 12:01:30'),(132,'ticker_item_2','Upto 500/- off for selected styles.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(133,'ticker_item_3','Use code <strong>WELCOME5</strong> for 5% OFF','2026-11-12 12:01:30','2026-11-12 12:01:30'),(134,'ticker_item_4','Accepting UPI payments','2026-11-12 12:01:30','2026-11-12 12:01:30'),(135,'ticker_item_5','Call us: <strong>+91 98765 43210</strong>','2026-11-12 12:01:30','2026-11-12 12:01:30'),(136,'vyra_story_badge','The Brand','2026-11-12 12:01:30','2026-11-12 12:01:30'),(137,'vyra_about_philosophy_badge','Philosophy 01','2026-11-12 12:01:30','2026-11-12 12:01:30'),(138,'vyra_story_title','Not Made to Blend In.\r\nMade to Rule.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(139,'vyra_about_philosophy_heading','Raw Edge. Zero Compromise.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(140,'vyra_story_p1','Vyra was created for those who treat style as armour. Born in the intersection of high fashion and street energy, every stitch is calibrated for raw confidence.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(141,'vyra_story_p2','Architectural seams, breathable sheer mesh, and unyielding contours. Designed without apologies, crafted for the fearless.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(142,'vyra_stat1_num','0%','2026-11-12 12:01:30','2026-11-12 12:01:30'),(143,'vyra_stat1_label','Compromise','2026-11-12 12:01:30','2026-11-12 12:01:30'),(144,'vyra_stat2_num','50K+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(145,'vyra_stat2_label','Rebels Styled','2026-11-12 12:01:30','2026-11-12 12:01:30'),(146,'vyra_stat3_num','100%','2026-11-12 12:01:30','2026-11-12 12:01:30'),(147,'vyra_stat3_label','Raw Edge','2026-11-12 12:01:30','2026-11-12 12:01:30'),(148,'vyra_pillar1_title','Architectural Seams','2026-11-12 12:01:30','2026-11-12 12:01:30'),(149,'vyra_pillar1_desc','Precision-engineered sheer mesh cut lines that sculpt your silhouette with zero bulk and maximum comfort.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(150,'vyra_pillar2_title','Midnight Noir Palette','2026-11-12 12:01:30','2026-11-12 12:01:30'),(151,'vyra_pillar2_desc','Deep carbon blacks, gunmetal clasps, and electric accents built for nocturnal presence and unfiltered attitude.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(152,'vyra_pillar3_title','Stealth Packaging','2026-11-12 12:01:30','2026-11-12 12:01:30'),(153,'vyra_pillar3_desc','All deliveries arrive in sealed, blacked-out matte boxes with zero exterior brand graphics. Your privacy is non-negotiable.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(154,'buttercup_story_badge','Our Story','2026-11-12 12:01:30','2026-11-12 12:01:30'),(155,'buttercup_story_title','Designed for Real Bodies,\r\nReal Days','2026-11-12 12:01:30','2026-11-12 12:01:30'),(156,'buttercup_about_philosophy_heading','Why Should Comfort and Style Ever Be a Trade-off?','2026-11-12 12:01:30','2026-11-12 12:01:30'),(157,'buttercup_story_p1','Buttercup started with one simple question: why should comfort and style be a trade-off? We design every piece with breathable, skin-friendly fabric — soft enough for everyday, pretty enough to feel special.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(158,'buttercup_story_p2','Zero painful underwires, zero pinch. Just butter-soft modal, organic cotton, and gentle stretch that moves with you from dawn to dusk.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(159,'buttercup_stat1_num','5+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(160,'buttercup_stat1_label','Years of Comfort','2026-11-12 12:01:30','2026-11-12 12:01:30'),(161,'buttercup_stat2_num','2L+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(162,'buttercup_stat2_label','Happy Customers','2026-11-12 12:01:30','2026-11-12 12:01:30'),(163,'buttercup_stat3_num','100%','2026-11-12 12:01:30','2026-11-12 12:01:30'),(164,'buttercup_stat3_label','Skin-Friendly','2026-11-12 12:01:30','2026-11-12 12:01:30'),(165,'buttercup_pillar1_title','Cloud-Soft Modal','2026-11-12 12:01:30','2026-11-12 12:01:30'),(166,'buttercup_pillar1_desc','Micro-modal knit woven from sustainably harvested beechwood, providing a feather-soft second skin feel.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(167,'buttercup_pillar2_title','Wire-Free Ergonomics','2026-11-12 12:01:30','2026-11-12 12:01:30'),(168,'buttercup_pillar2_desc','Sculpted support panels that gently lift without digging metal bones into your ribs. Wearable 24 hours a day.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(169,'buttercup_pillar3_title','Real-Body Inclusive Sizing','2026-11-12 12:01:30','2026-11-12 12:01:30'),(170,'buttercup_pillar3_desc','Engineered across all bust shapes and cup sizes so you never feel squeezed, pinched, or restricted.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(171,'saaya_story_badge','Our Story','2026-11-12 12:01:30','2026-11-12 12:01:30'),(172,'saaya_about_philosophy_badge','Heritage & Mastery','2026-11-12 12:01:30','2026-11-12 12:01:30'),(173,'saaya_story_title','A Love Letter\r\nto Indian Femininity','2026-11-12 12:01:30','2026-11-12 12:01:30'),(174,'saaya_about_philosophy_heading','Where Ancient Zari Craft Meets Modern Contour','2026-11-12 12:01:30','2026-11-12 12:01:30'),(175,'saaya_story_p1','Saaya was born from a belief that every Indian woman deserves lingerie that honours her form and her heritage. We weave together age-old motifs — jaali embroidery, zari threadwork, lotus prints — with modern fits engineered for real bodies.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(176,'saaya_story_p2','Every piece leaves our atelier only after passing through the hands of our master karigars, who carry forward skills passed down through generations.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(177,'saaya_stat1_num','12+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(178,'saaya_stat1_label','Years of Craft','2026-11-12 12:01:30','2026-11-12 12:01:30'),(179,'saaya_stat2_num','80K+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(180,'saaya_stat2_label','Brides Adorned','2026-11-12 12:01:30','2026-11-12 12:01:30'),(181,'saaya_stat3_num','450+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(182,'saaya_stat3_label','Karigars','2026-11-12 12:01:30','2026-11-12 12:01:30'),(183,'saaya_pillar1_title','Pure Mulberry Silk','2026-11-12 12:01:30','2026-11-12 12:01:30'),(184,'saaya_pillar1_desc','Sustainably sourced Grade 6A mulberry silk that breathes against the skin, delivering regal drape and featherlight comfort.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(185,'saaya_pillar2_title','Master Karigar Craft','2026-11-12 12:01:30','2026-11-12 12:01:30'),(186,'saaya_pillar2_desc','Each seam is hand-finished by master craftspeople across Rajasthan and Bengal, preserving centuries-old artisanal zardozi techniques.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(187,'saaya_pillar3_title','Discreet Heirloom Packaging','2026-11-12 12:01:30','2026-11-12 12:01:30'),(188,'saaya_pillar3_desc','Your intimacy is sacred. Every order is encased in unmarked, gold-embossed matte keepsakes with zero external brand indicators.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(189,'lumiere_story_badge','Our Story','2026-11-12 12:01:30','2026-11-12 12:01:30'),(190,'lumiere_about_philosophy_badge','Artisanal Roots','2026-11-12 12:01:30','2026-11-12 12:01:30'),(191,'lumiere_story_title','Everyday elegance,\r\nmade responsibly','2026-11-12 12:01:30','2026-11-12 12:01:30'),(192,'lumiere_about_philosophy_heading','Hand-Finished Pure Mulberry Silk','2026-11-12 12:01:30','2026-11-12 12:01:30'),(193,'lumiere_story_p1','Lumière began with a single loom and a simple belief: the things closest to your skin should be the best you own. Every piece is cut from sustainably sourced silk and finished by artisans who have done this for generations.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(194,'lumiere_story_p2','We work directly with master craftspeople in heritage clusters across India, guaranteeing fair wages and keeping age-old textile traditions alive in contemporary silhouettes.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(195,'lumiere_stat1_num','100%','2026-11-12 12:01:30','2026-11-12 12:01:30'),(196,'lumiere_stat1_label','Pure Silk','2026-11-12 12:01:30','2026-11-12 12:01:30'),(197,'lumiere_stat2_num','300+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(198,'lumiere_stat2_label','Artisans','2026-11-12 12:01:30','2026-11-12 12:01:30'),(199,'lumiere_stat3_num','12','2026-11-12 12:01:30','2026-11-12 12:01:30'),(200,'lumiere_stat3_label','Cities','2026-11-12 12:01:30','2026-11-12 12:01:30'),(201,'lumiere_pillar1_title','Mulberry Silk Quality','2026-11-12 12:01:30','2026-11-12 12:01:30'),(202,'lumiere_pillar1_desc','Featherweight, breathable Grade 6A pure mulberry silk naturally hypoallergenic and temperature-regulating.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(203,'lumiere_pillar2_title','Fair-Wage Handcraft','2026-11-12 12:01:30','2026-11-12 12:01:30'),(204,'lumiere_pillar2_desc','Honouring craftspeople in historic artisan clusters with fair wages, safe ateliers, and sustainable working standards.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(205,'lumiere_pillar3_title','Zero-Plastic Keepsakes','2026-11-12 12:01:30','2026-11-12 12:01:30'),(206,'lumiere_pillar3_desc','Delivered in unmarked, satin-tied reusable presentation boxes. Minimal environmental footprint, absolute discretion.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(207,'organic_about_badge','🌱 Our Farming Manifesto','2026-11-12 12:01:30','2026-11-12 12:01:30'),(208,'organic_story_badge','The Farm-to-Fork Journey','2026-11-12 12:01:30','2026-11-12 12:01:30'),(209,'organic_about_title','Bringing Honest, Pure & Chemical-Free Produce to Indian Homes','2026-11-12 12:01:30','2026-11-12 12:01:30'),(210,'organic_about_subtitle','Nexon Organic was born out of a profound commitment to human wellness and planetary health. We believe food should nourish, heal, and bring us closer to the rhythms of nature.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(211,'organic_story_title','Why We Refuse to Compromise on Soil Health','2026-11-12 12:01:30','2026-11-12 12:01:30'),(212,'organic_story_p1','Modern industrial farming relies on synthetic pesticides, chemical sprays, and artificial ripening agents that strip produce of essential micronutrients while poisoning aquifers.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(213,'organic_story_p2','At Nexon Organic, we cultivate our fields using regenerative multi-cropping, biodiverse cow-based compost (Jeevamrutha), and solar pest traps. Our harvest is packed in breathable eco-crates and dispatched overnight so you receive farm-fresh vitality within 24 hours of cutting.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(214,'organic_stat1_num','100%','2026-11-12 12:01:30','2026-11-12 12:01:30'),(215,'organic_stat1_label','Pesticide & Urea Free','2026-11-12 12:01:30','2026-11-12 12:01:30'),(216,'organic_stat2_num','350+','2026-11-12 12:01:30','2026-11-12 12:01:30'),(217,'organic_stat2_label','Partner Organic Farmers','2026-11-12 12:01:30','2026-11-12 12:01:30'),(218,'organic_pillar1_title','Living Soil First','2026-11-12 12:01:30','2026-11-12 12:01:30'),(219,'organic_pillar1_desc','We nourish the living biome in our soil with cow dung, microbial wash, and mulch instead of petrochemical synthetic urea.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(220,'organic_pillar2_title','24-Hour Farm-to-Doorstep','2026-11-12 12:01:30','2026-11-12 12:01:30'),(221,'organic_pillar2_desc','Harvested in pre-dawn hours, chill-packed without artificial wax or ethylene sprays, and delivered before sunset.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(222,'organic_pillar3_title','Complete QR Traceability','2026-11-12 12:01:30','2026-11-12 12:01:30'),(223,'organic_pillar3_desc','Every vegetable box comes with a farm batch code showing you the soil report, harvest timestamp, and the farmer who tended your crop.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(224,'organic_pillar4_title','Fair Fair-Trade Incomes','2026-11-12 12:01:30','2026-11-12 12:01:30'),(225,'organic_pillar4_desc','We bypass mandis and middlemen, paying our partner farmers guaranteed minimum premiums 35% above conventional market rates.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(226,'organic_story_f1_title','Zero Chemical Residue','2026-11-12 12:01:30','2026-11-12 12:01:30'),(227,'organic_story_f1_desc','Laboratory tested batches with certified zero chemical residue.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(228,'organic_story_f2_title','Eco-Safe Packaging','2026-11-12 12:01:30','2026-11-12 12:01:30'),(229,'organic_story_f2_desc','100% biodegradable cornstarch bags and corrugated kraft crates.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(230,'organic_story_btn_text','Read Our Farming Manifesto','2026-11-12 12:01:30','2026-11-12 12:01:30'),(231,'organic_story_btn_url','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(232,'organic_story_badge2','100% Traceable Harvest','2026-11-12 12:01:30','2026-11-12 12:01:30'),(233,'organic_story_badge2_desc','Scan QR on any crate to see farm origins','2026-11-12 12:01:30','2026-11-12 12:01:30'),(234,'sizeguide_badge','Atelier Fit Perfection','2026-11-12 12:01:30','2026-11-12 12:01:30'),(235,'sizeguide_title','The Precision Size Guide','2026-11-12 12:01:30','2026-11-12 12:01:30'),(236,'sizeguide_subtitle','Over 80% of women wear the incorrect bra size. Use our interactive measurement calculator to find your exact band, cup, and sister size.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(237,'sizeguide_calc_badge','Interactive Calculator','2026-11-12 12:01:30','2026-11-12 12:01:30'),(238,'sizeguide_calc_title','Find Your Bespoke Size','2026-11-12 12:01:30','2026-11-12 12:01:30'),(239,'sizeguide_calc_desc','Grab a flexible measuring tape while standing relaxed in an unpadded, non-pushup bra.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(240,'sizeguide_underbust_hint','Measure snugly directly underneath your breasts.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(241,'sizeguide_bust_hint','Measure loosely across the fullest part of your bust.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(242,'sizeguide_sister_title','Sister Sizing Matrix','2026-11-12 12:01:30','2026-11-12 12:01:30'),(243,'sizeguide_sister_desc','Sister sizes share identical cup volume despite having different band measurements. If your band feels too loose or too snug, navigate horizontally along your sister size group:','2026-11-12 12:01:30','2026-11-12 12:01:30'),(244,'sizeguide_bottoms_title','Panties, Chemises & Robes','2026-11-12 12:01:30','2026-11-12 12:01:30'),(245,'sizeguide_bottoms_desc','For bottoms and sleepwear, measure your waist at the narrowest point and your hips around the fullest curve.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(246,'sizeguide_additional_info','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(247,'shipping_policy_badge','Logistics & Privacy','2026-11-12 12:01:30','2026-11-12 12:01:30'),(248,'shipping_policy_title','Shipping & Discreet Delivery','2026-11-12 12:01:30','2026-11-12 12:01:30'),(249,'shipping_policy_subtitle','Our gold standard delivery experience: fast, trackable, and completely private from our atelier to your doorstep.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(250,'shipping_banner_badge','Our Privacy Promise','2026-11-12 12:01:30','2026-11-12 12:01:30'),(251,'shipping_banner_title','100% Confidential Outer Packaging','2026-11-12 12:01:30','2026-11-12 12:01:30'),(252,'shipping_banner_desc','Every order is dispatched in an unmarked, tamper-evident exterior box or opaque courier flyer. There are zero mentions of lingerie, brassieres, or intimate apparel anywhere on the exterior mailing label. The sender is listed simply as &quot;LS Logistics&quot;.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(253,'shipping_sec1_title','1. Processing & Dispatch Timelines','2026-11-12 12:01:30','2026-11-12 12:01:30'),(254,'shipping_sec1_desc','All in-stock orders are processed within 24 business hours (Monday through Saturday, excluding national holidays). Once dispatched, an SMS and email notification with your live tracking AWB link will be automatically sent.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(255,'shipping_sec2_title','2. Estimated Delivery Transit Times','2026-11-12 12:01:30','2026-11-12 12:01:30'),(256,'shipping_sec2_desc','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(257,'shipping_sec3_title','3. Shipping Rates & Free Delivery','2026-11-12 12:01:30','2026-11-12 12:01:30'),(258,'shipping_sec3_desc','We offer Complimentary Express Shipping on all prepaid orders exceeding ₹1,499 across India. For orders below ₹1,499, a nominal flat express shipping fee of ₹99 is applied at checkout.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(259,'shipping_sec4_title','4. Cash on Delivery (COD)','2026-11-12 12:01:30','2026-11-12 12:01:30'),(260,'shipping_sec4_desc','Cash on Delivery is available for serviceable pincodes with a flat ₹50 handling charge. An OTP or telephonic confirmation may be requested prior to dispatch for high-value COD shipments.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(261,'shipping_sec5_title','5. Courier Partners','2026-11-12 12:01:30','2026-11-12 12:01:30'),(262,'shipping_sec5_desc','We partner exclusively with top-tier courier networks including Bluedart, Delhivery, Shadowfax, and Xpressbees to ensure safe transit and zero tampering.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(263,'shipping_custom_content','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(264,'return_policy_badge','Customer Guarantee','2026-11-12 12:01:30','2026-11-12 12:01:30'),(265,'return_policy_title','Returns & Hygiene Standards','2026-11-12 12:01:30','2026-11-12 12:01:30'),(266,'return_policy_subtitle','Your complete confidence and hygiene are our highest priorities. Review our straightforward intimate return policies below.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(267,'return_banner_title','Intimate Hygiene Regulations','2026-11-12 12:01:30','2026-11-12 12:01:30'),(268,'return_banner_desc','Due to strict Indian health & safety standards for second-skin intimates, items tried directly on the skin without underwear or protective hygiene stickers cannot be restocked or refunded.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(269,'return_sec1_title','1. Eligible Items for 7-Day Return / Exchange','2026-11-12 12:01:30','2026-11-12 12:01:30'),(270,'return_sec1_desc','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(271,'return_sec2_title','2. Final Sale (Non-Returnable) Items','2026-11-12 12:01:30','2026-11-12 12:01:30'),(272,'return_sec2_desc','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(273,'return_sec3_title','3. How to Initiate a Return','2026-11-12 12:01:30','2026-11-12 12:01:30'),(274,'return_sec3_desc','','2026-11-12 12:01:30','2026-11-13 08:45:49'),(275,'return_sec4_title','4. Refund Processing','2026-11-12 12:01:30','2026-11-12 12:01:30'),(276,'return_sec4_desc','Upon receipt and quality inspection at our atelier (usually 2 business days after receipt), refunds are processed directly to your original payment method (Credit card, UPI, Net Banking) within 3 to 5 banking days. COD orders are refunded via instant UPI or NEFT transfer.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(277,'return_custom_content','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(278,'product_delivery_info','Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(279,'product_returns_info','Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(280,'invoice_footer','Thank you for choosing Nex-on!','2026-11-12 12:01:30','2026-11-12 12:01:30'),(281,'order_webhook_urls','','2026-11-12 12:01:30','2026-11-12 12:01:30'),(282,'pwa_prompt_enabled','1','2026-11-12 12:01:30','2026-11-12 12:01:30'),(283,'pwa_prompt_title','Install Nex-on App','2026-11-12 12:01:30','2026-11-12 12:01:30'),(284,'pwa_prompt_description','Get quick access to our collection! Install our app for a faster, better shopping experience on your device.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(285,'organic_delivery_info','Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(286,'organic_returns_info','Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.','2026-11-12 12:01:30','2026-11-12 12:01:30'),(287,'organic_hero_image','banners/u8T9qc68lvSConCNNcOxFsIHYTOb3qQSzBPTI00o.avif','2026-11-13 04:53:16','2026-11-13 04:53:16'),(288,'organic_story_image','banners/CkdBC0695eBihbHx5z6DAVfidT2mArtM7pdE639O.avif','2026-11-13 04:53:16','2026-11-13 04:53:16'),(289,'organic_ad1_image','banners/CD2bAAvcPAuxt3fxrSvHpTDO3ycLghJ2duOkP6jn.avif','2026-11-13 05:25:11','2026-11-13 05:36:51'),(290,'organic_ad2_image','banners/0g0kbHEIGaAIcGykmo1fseM0qd66p0du4ZncJZJG.avif','2026-11-13 05:25:11','2026-11-13 05:25:11'),(291,'organic_ad3_image','banners/WgBpOMzz30kvpkuLczZOYE0YjdxUpZH1s3dx250S.avif','2026-11-13 05:25:11','2026-11-13 05:25:11'),(292,'vyra_hero_image','slider/c6NbzKCFLKVbCzWAefaBeO9WuWYE6XeWfEAGH9TY.mp4','2026-11-13 06:23:47','2026-11-13 06:45:20'),(293,'vyra_hero_btn2_text','Explore','2026-11-13 06:23:47','2026-11-13 06:23:47'),(294,'vyra_hero_btn2_url','https://nexon.test/search','2026-11-13 06:23:47','2026-11-13 06:23:47'),(295,'vyra_story_image','banners/oZLQRJ1nYxJBbk9aeVy0S5HItXc1R7PRBXMOQ0Tk.avif','2026-11-13 06:45:21','2026-11-13 06:45:21'),(296,'saaya_slide_1_badge','Crafted for Her','2026-11-13 06:45:21','2026-11-13 06:45:21'),(297,'saaya_slide_1_title','Draped in\r\nGold & Grace','2026-11-13 06:45:21','2026-11-13 06:45:21'),(298,'saaya_slide_1_subtitle','Handcrafted Indian lingerie rooted in tradition, elevated by craft. Each piece is an heirloom waiting to be worn.','2026-11-13 06:45:21','2026-11-13 06:45:21'),(299,'saaya_slide_1_btn_text','Explore Collection','2026-11-13 06:45:21','2026-11-13 06:45:21'),(300,'saaya_slide_1_btn_url','https://nexon.test/search','2026-11-13 06:45:21','2026-11-13 06:45:21'),(301,'saaya_slide_2_badge','Royal Trousseau Edit','2026-11-13 06:45:21','2026-11-13 06:45:21'),(302,'saaya_slide_2_title','Zari Silk &\r\nVelvet Intimates','2026-11-13 06:45:21','2026-11-13 06:45:21'),(303,'saaya_slide_2_subtitle','Intricate gold wire embroidery and pure mulberry silk, tailored for sacred celebrations and timeless grandeur.','2026-11-13 06:45:21','2026-11-13 06:45:21'),(304,'saaya_slide_2_btn_text','Shop Bridal Silk','2026-11-13 06:45:21','2026-11-13 06:45:21'),(305,'saaya_slide_2_btn_url','https://nexon.test/search','2026-11-13 06:45:21','2026-11-13 06:45:21'),(306,'saaya_slide_3_badge','Artisanal Heritage','2026-11-13 06:45:21','2026-11-13 06:45:21'),(307,'saaya_slide_3_title','The Benarasi\r\nArchive','2026-11-13 06:45:21','2026-11-13 06:45:21'),(308,'saaya_slide_3_subtitle','Handwoven motifs passed through generations of master ateliers. Where heritage craft meets modern form.','2026-11-13 06:45:21','2026-11-13 06:45:21'),(309,'saaya_slide_3_btn_text','Discover The Archive','2026-11-13 06:45:21','2026-11-13 06:45:21'),(310,'saaya_slide_3_btn_url','https://nexon.test/search','2026-11-13 06:45:21','2026-11-13 06:45:21'),(311,'saaya_slide_4_badge','Festive Elegance','2026-11-13 06:45:21','2026-11-13 06:45:21'),(312,'saaya_slide_4_title','Crimson Whisper\r\nCapsule','2026-11-13 06:45:21','2026-11-13 06:45:21'),(313,'saaya_slide_4_subtitle','Weightless tulle drenched in ceremonial crimson, edged with delicate antique gota patti work.','2026-11-13 06:45:21','2026-11-13 06:45:21'),(314,'saaya_slide_4_btn_text','Explore Capsule','2026-11-13 06:45:21','2026-11-13 06:45:21'),(315,'saaya_slide_4_btn_url','https://nexon.test/search','2026-11-13 06:45:21','2026-11-13 06:45:21'),(316,'saaya_slide_5_badge','Bespoke Craft','2026-11-13 06:45:21','2026-11-13 06:45:21'),(317,'saaya_slide_5_title','Pure Heirloom\r\nComfort','2026-11-13 06:45:21','2026-11-13 06:45:21'),(318,'saaya_slide_5_subtitle','Soft uncrushable silk satin crafted to drape seamlessly under royal lehengas and festive silhouettes.','2026-11-13 06:45:21','2026-11-13 06:45:21'),(319,'saaya_slide_5_btn_text','Shop Collection','2026-11-13 06:45:21','2026-11-13 06:45:21'),(320,'saaya_slide_5_btn_url','https://nexon.test/search','2026-11-13 06:45:21','2026-11-13 06:45:21');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_logs`
--

DROP TABLE IF EXISTS `sms_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'log',
  `template_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `order_id` bigint unsigned DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `gateway_response_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_logs_order_id_foreign` (`order_id`),
  KEY `sms_logs_phone_index` (`phone`),
  CONSTRAINT `sms_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_logs`
--

LOCK TABLES `sms_logs` WRITE;
/*!40000 ALTER TABLE `sms_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_appraisals`
--

DROP TABLE IF EXISTS `staff_appraisals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_appraisals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `appraisal_cycle_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `reviewer_id` bigint unsigned DEFAULT NULL,
  `self_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `manager_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `peer_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `final_score` decimal(3,2) NOT NULL DEFAULT '0.00',
  `competency_scores` json DEFAULT NULL,
  `strengths` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `areas_for_improvement` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `recommendation` enum('salary_hike','promotion','bonus','training_pip','retain') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'salary_hike',
  `hike_percentage` decimal(4,1) NOT NULL DEFAULT '0.0',
  `status` enum('draft','submitted','reviewed','finalized') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_appraisals_user_id_foreign` (`user_id`),
  KEY `staff_appraisals_reviewer_id_foreign` (`reviewer_id`),
  KEY `staff_appraisals_appraisal_cycle_id_user_id_index` (`appraisal_cycle_id`,`user_id`),
  CONSTRAINT `staff_appraisals_appraisal_cycle_id_foreign` FOREIGN KEY (`appraisal_cycle_id`) REFERENCES `appraisal_cycles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_appraisals_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_appraisals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_appraisals`
--

LOCK TABLES `staff_appraisals` WRITE;
/*!40000 ALTER TABLE `staff_appraisals` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_appraisals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_attendances`
--

DROP TABLE IF EXISTS `staff_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `shift_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'POS Terminal 1',
  `location_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Boutique Flagship Store',
  `total_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('present','late','half_day','absent','on_leave') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `verification_status` enum('verified','flagged_remote','manual_adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'verified',
  `manager_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `adjusted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_attendances_user_id_date_unique` (`user_id`,`date`),
  KEY `staff_attendances_department_id_foreign` (`department_id`),
  KEY `staff_attendances_shift_id_foreign` (`shift_id`),
  KEY `staff_attendances_adjusted_by_foreign` (`adjusted_by`),
  KEY `staff_attendances_date_status_index` (`date`,`status`),
  CONSTRAINT `staff_attendances_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_attendances_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_attendances_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `staff_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_attendances`
--

LOCK TABLES `staff_attendances` WRITE;
/*!40000 ALTER TABLE `staff_attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_shifts`
--

DROP TABLE IF EXISTS `staff_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `shift_date` date NOT NULL,
  `shift_type` enum('morning','evening','night','full_day') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'morning',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('scheduled','completed','swapped','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_shifts_department_id_foreign` (`department_id`),
  KEY `staff_shifts_created_by_foreign` (`created_by`),
  KEY `staff_shifts_shift_date_department_id_index` (`shift_date`,`department_id`),
  KEY `staff_shifts_user_id_shift_date_index` (`user_id`,`shift_date`),
  CONSTRAINT `staff_shifts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_shifts_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_shifts`
--

LOCK TABLES `staff_shifts` WRITE;
/*!40000 ALTER TABLE `staff_shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_transfer_items`
--

DROP TABLE IF EXISTS `stock_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned NOT NULL,
  `qty_sent` int NOT NULL,
  `qty_received` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfer_items_stock_transfer_id_foreign` (`stock_transfer_id`),
  KEY `stock_transfer_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `stock_transfer_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfer_items_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_transfer_items`
--

LOCK TABLES `stock_transfer_items` WRITE;
/*!40000 ALTER TABLE `stock_transfer_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_transfer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_transfers`
--

DROP TABLE IF EXISTS `stock_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_outlet_id` bigint unsigned NOT NULL,
  `to_outlet_id` bigint unsigned NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dispatched_by` bigint unsigned DEFAULT NULL,
  `dispatched_at` timestamp NULL DEFAULT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_transfer_no_unique` (`transfer_no`),
  KEY `stock_transfers_from_outlet_id_foreign` (`from_outlet_id`),
  KEY `stock_transfers_to_outlet_id_foreign` (`to_outlet_id`),
  KEY `stock_transfers_dispatched_by_foreign` (`dispatched_by`),
  KEY `stock_transfers_received_by_foreign` (`received_by`),
  CONSTRAINT `stock_transfers_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_from_outlet_id_foreign` FOREIGN KEY (`from_outlet_id`) REFERENCES `outlets` (`id`),
  CONSTRAINT `stock_transfers_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_to_outlet_id_foreign` FOREIGN KEY (`to_outlet_id`) REFERENCES `outlets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_transfers`
--

LOCK TABLES `stock_transfers` WRITE;
/*!40000 ALTER TABLE `stock_transfers` DISABLE KEYS */;
INSERT INTO `stock_transfers` VALUES (1,'TRF-20261016-0001',3,1,'received','Pre-festive restock of mulberry silk robes. Tracking: EXP-BHW-COL-9901',18,'2026-11-10 10:57:05',20,'2026-11-11 10:57:05','2026-11-12 10:57:05','2026-11-12 10:57:05'),(2,'TRF-20261016-0002',3,2,'dispatched','New bridal trousseau samples for Bengaluru studio launch. Tracking: BLUEDART-88992211',18,'2026-11-12 04:57:05',NULL,NULL,'2026-11-12 10:57:05','2026-11-12 10:57:05');
/*!40000 ALTER TABLE `stock_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribers`
--

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_invoice_payments`
--

DROP TABLE IF EXISTS `supplier_invoice_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_invoice_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_invoice_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `transaction_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` date NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_invoice_payments_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  CONSTRAINT `supplier_invoice_payments_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_invoice_payments`
--

LOCK TABLES `supplier_invoice_payments` WRITE;
/*!40000 ALTER TABLE `supplier_invoice_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_invoice_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_invoices`
--

DROP TABLE IF EXISTS `supplier_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('unpaid','partial','paid','overdue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `matching_status` enum('matched','variance_detected','under_review') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'matched',
  `po_variance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_invoices_supplier_id_invoice_number_unique` (`supplier_id`,`invoice_number`),
  KEY `supplier_invoices_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `supplier_invoices_payment_status_index` (`payment_status`),
  CONSTRAINT `supplier_invoices_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_invoices`
--

LOCK TABLES `supplier_invoices` WRITE;
/*!40000 ALTER TABLE `supplier_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_time_days` int unsigned NOT NULL DEFAULT '7',
  `gst_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Varanasi Mulberry Silk Mills','SUP-VAR-01','Pandit Rakesh Mishra','orders@varanasi-silk.in','+91 542 250 8899',7,'09AABCU9603R1ZM','Ghat Road, Chowk, Varanasi, UP 221001',1,'2026-11-12 10:57:05','2026-11-12 10:57:05'),(2,'Surat Premium Lace & Weaves','SUP-SUR-02','Bhavesh Shah','sales@suratlaceweaves.com','+91 261 245 1122',5,'24AABCS4455C1ZL','Ring Road Textile Market, Surat, Gujarat 395002',1,'2026-11-12 10:57:05','2026-11-12 10:57:05');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_ticket_messages`
--

DROP TABLE IF EXISTS `support_ticket_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_ticket_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal_note` tinyint(1) NOT NULL DEFAULT '0',
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_messages_user_id_foreign` (`user_id`),
  KEY `support_ticket_messages_ticket_id_created_at_index` (`ticket_id`,`created_at`),
  CONSTRAINT `support_ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_messages`
--

LOCK TABLES `support_ticket_messages` WRITE;
/*!40000 ALTER TABLE `support_ticket_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_ticket_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('order_inquiry','refund_return','delivery_delay','product_defect','billing','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'order_inquiry',
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','waiting_customer','resolved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `sla_due_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_order_id_foreign` (`order_id`),
  KEY `support_tickets_assigned_to_foreign` (`assigned_to`),
  KEY `support_tickets_status_priority_index` (`status`,`priority`),
  KEY `support_tickets_user_id_order_id_index` (`user_id`,`order_id`),
  CONSTRAINT `support_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (2,'TCK-202611-001',11,1,19,'Query regarding blouse piece with Ratri Saree','order_inquiry','medium','in_progress','2026-11-13 11:40:29',NULL,'2026-11-12 11:40:29','2026-11-12 11:40:29');
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `templates_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES (14,'saaya','Saaya',NULL,NULL,0,'2026-11-12 10:57:02','2026-11-13 07:30:36'),(15,'buttercup','Buttercup',NULL,NULL,0,'2026-11-12 10:57:02','2026-11-13 07:30:36'),(16,'vyra','Vyra',NULL,NULL,0,'2026-11-12 10:57:02','2026-11-13 07:30:36'),(17,'lumiere','Lumière',NULL,NULL,1,'2026-11-12 10:57:02','2026-11-13 07:30:36'),(18,'organic','Verdia','Verdia Botanical Living',NULL,0,'2026-11-12 10:57:02','2026-11-13 07:30:36');
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (19,2);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `otp_code` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `role` enum('customer','vendor','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `designation` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `staff_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_staff` tinyint(1) NOT NULL DEFAULT '0',
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'profile.jpg',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deactivated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_employment_status_index` (`employment_status`),
  KEY `users_is_staff_index` (`is_staff`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (11,'Priya Sharma','priya.sharma@example.com',NULL,'2026-11-12 10:57:02','$2y$12$n5212N8EzJVJyO1SFWwd0uVTjw0QOCLU9AFaMUapyy.f8.AquQnZi',NULL,NULL,NULL,NULL,NULL,'customer',NULL,NULL,'active',0.00,0,'profile.jpg',NULL,NULL,'2026-11-12 10:57:02','2026-11-12 10:57:02'),(12,'Robert Hayes','r.hayes@store.com','+1 555-1001',NULL,'$2y$12$vNOdYboKQMfutAHzVduzvOWx/7JzbKwfzIYB/yxxUsfWlsUS0urgK',NULL,NULL,NULL,NULL,NULL,'admin','Warehouse Manager','Operations','active',0.00,1,'profile.jpg',NULL,NULL,'2022-01-15 03:30:00','2026-11-12 10:57:03'),(13,'Jessica Lee','j.lee@store.com','+1 555-1002',NULL,'$2y$12$xUhc//OpiBgHAYEXSUpQQOEmJhHMU8ztXhxr9tz45nBZ5JJG7m7ze',NULL,NULL,NULL,NULL,NULL,'admin','Store Manager','Management','active',0.00,1,'profile.jpg',NULL,NULL,'2021-03-10 03:30:00','2026-11-12 10:57:03'),(14,'Tyler Brooks','t.brooks@store.com','+1 555-1003',NULL,'$2y$12$yLXB7hmuKGyBbHV3oSgAQuTXpzkJhosLUOJWAnx2kZnrCXfyIllFm',NULL,NULL,NULL,NULL,NULL,'admin','Sales Associate','Sales','active',28400.00,1,'profile.jpg',NULL,NULL,'2023-09-01 03:30:00','2026-11-12 10:57:03'),(15,'Nadia Petrov','n.petrov@store.com','+1 555-1004',NULL,'$2y$12$LdtfXzBd4zJ5lbhn1XiQ.u7tMgSHpsv4EKsUcY9TcsBHvGbXyzzye',NULL,NULL,NULL,NULL,NULL,'admin','Cashier','POS','active',14200.00,1,'profile.jpg',NULL,NULL,'2024-02-12 03:30:00','2026-11-12 10:57:03'),(16,'Kevin Park','k.park@store.com','+1 555-1005',NULL,'$2y$12$iTecU0LSjXZwC6m8xPVtEu498oEXalvGrUO7o6E7WB6OWH9nCySs6',NULL,NULL,NULL,NULL,NULL,'admin','Inventory Analyst','Operations','active',0.00,1,'profile.jpg',NULL,NULL,'2023-06-20 03:30:00','2026-11-12 10:57:04'),(17,'Alicia Gomez','a.gomez@store.com','+1 555-1006',NULL,'$2y$12$xJTXNlP.mYu97AXCeViHjuhsbo1XarB2OryqK5cpcQrX4zipj5yJq',NULL,NULL,NULL,NULL,NULL,'admin','Customer Support','Support','on_leave',0.00,1,'profile.jpg',NULL,NULL,'2022-11-05 03:30:00','2026-11-12 10:57:04'),(18,'Super Administrator','admin@nexon.app',NULL,'2026-11-12 10:57:04','$2y$12$zCVJV12xshxP0qk3qGWZLOmQPRkvZERAjYog05VOgX58TinDmFrNK',NULL,NULL,NULL,NULL,NULL,'admin',NULL,NULL,'active',0.00,0,'profile.jpg','EwyhtFO9ASRWJBKwNHbnesaPbroOaYm1PKzFQl2W3PXbEWFnso3riYgY36CT',NULL,'2026-11-12 10:57:04','2026-11-13 04:50:36'),(19,'Store Manager','manager@nexon.app',NULL,'2026-11-12 10:57:04','$2y$12$zfmUK7dqWDVmxyX7LoUG2OXkmQjDPUkPfBj/dtC53aAPiS9CNjjN6',NULL,NULL,NULL,NULL,NULL,'admin','Store Manager','Store Operations','active',0.00,1,'profile.jpg',NULL,NULL,'2026-11-12 10:57:04','2026-11-12 10:57:04'),(20,'Meera Kapoor','staff@nexon.com',NULL,'2026-11-12 10:57:05','$2y$12$tPHdn2/acOhsqgMRk9ENWuSz1bYlACAD4WAUVR6RSBRROWETuvNhm',NULL,NULL,NULL,NULL,NULL,'admin',NULL,NULL,'active',0.00,0,'profile.jpg',NULL,NULL,'2026-11-12 10:57:05','2026-11-12 10:57:05');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-13 17:04:53
