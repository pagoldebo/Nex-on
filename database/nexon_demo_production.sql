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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abandoned_carts`
--

LOCK TABLES `abandoned_carts` WRITE;
/*!40000 ALTER TABLE `abandoned_carts` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addon_product`
--

LOCK TABLES `addon_product` WRITE;
/*!40000 ALTER TABLE `addon_product` DISABLE KEYS */;
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
INSERT INTO `addons` VALUES (1,'Discreet Gift Box','Plain exterior shipping box with silk tissue wrapping inside for total privacy.',49.00,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(2,'Signature Satin Gift Bag','Reusable champagne gold satin pouch with woven ribbon.',129.00,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(3,'Handwritten Love Note','Custom handwritten greeting card with your personalized message.',29.00,1,'2026-11-18 11:13:54','2026-11-18 11:13:54');
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
INSERT INTO `appraisal_cycles` VALUES (1,'Annual Performance Review FY 2026-27','2026-01-01','2026-12-31','Comprehensive annual evaluation covering sales targets, client hospitality, and teamwork.','active','2026-11-18 11:13:50','2026-11-18 11:13:50');
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
INSERT INTO `brands` VALUES (1,'Nex-on Signature','nexon-signature','Flagship couture & signature collection.',NULL,NULL,1,1,'2026-11-18 11:13:53','2026-11-18 11:13:53'),(2,'Sabyasachi Atelier','sabyasachi-atelier','Opulent royal bridal lehengas, sarees, and heirloom ensembles.',NULL,NULL,1,2,'2026-11-18 11:13:53','2026-11-18 11:13:53'),(3,'Raw Mango','raw-mango','Contemporary handwoven Chanderi, Banarasi, and Mashru silks.',NULL,NULL,1,3,'2026-11-18 11:13:53','2026-11-18 11:13:53'),(4,'Bespoke Heritage','bespoke-heritage','Handcrafted pure tussar, linen, and artisanal handloom fabrics.',NULL,NULL,1,4,'2026-11-18 11:13:53','2026-11-18 11:13:53'),(5,'Vedic Organics','vedic-organics','Pure organic produce, botanicals, and farm-fresh natural wellness.',NULL,NULL,1,5,'2026-11-18 11:13:53','2026-11-18 11:13:53');
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
INSERT INTO `categories` VALUES (1,NULL,'Bralettes','bralettes',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(2,NULL,'Lingerie Sets','lingerie-sets',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(3,NULL,'Bodysuits','bodysuits',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(4,NULL,'Nightwear & Robes','nightwear-robes',NULL,4,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(5,NULL,'Bridal Edit','bridal-edit',NULL,5,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(6,NULL,'Everyday Essentials','everyday-essentials',NULL,6,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(7,NULL,'Men','men',NULL,10,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(8,7,'Shirt','men-shirt',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(9,8,'Formal Shirt','men-shirt-formal-shirt',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(10,8,'Casual Shirt','men-shirt-casual-shirt',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(11,8,'Linen Kurta Shirt','men-shirt-linen-kurta-shirt',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(12,7,'Pants','men-pants',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(13,12,'Formal Trousers','men-pants-formal-trousers',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(14,12,'Chino Pants','men-pants-chino-pants',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(15,12,'Denim Jeans','men-pants-denim-jeans',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(16,7,'Dhuti','men-dhuti',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(17,16,'Silk Dhuti','men-dhuti-silk-dhuti',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(18,16,'Cotton Dhuti','men-dhuti-cotton-dhuti',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(19,7,'Brief','men-brief',NULL,4,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(20,19,'Boxer Briefs','men-brief-boxer-briefs',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(21,19,'Classic Briefs','men-brief-classic-briefs',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(22,19,'Trunks','men-brief-trunks',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(23,NULL,'Women','women',NULL,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(24,23,'T-shirt','women-t-shirt',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(25,24,'Oversized Tees','women-t-shirt-oversized-tees',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(26,24,'Crop Tops','women-t-shirt-crop-tops',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(27,23,'Bra','women-bra',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(28,27,'T-Shirt Bra','women-bra-t-shirt-bra',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(29,27,'Strapless Bra','women-bra-strapless-bra',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(30,27,'Push-Up Bra','women-bra-push-up-bra',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(31,23,'Panties','women-panties',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(32,31,'Seamless Hipsters','women-panties-seamless-hipsters',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(33,31,'Bikini Briefs','women-panties-bikini-briefs',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(34,31,'Lace Thongs','women-panties-lace-thongs',NULL,3,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(35,23,'Bralettes','women-bralettes',NULL,4,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(36,35,'Triangle Lace','women-bralettes-triangle-lace',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(37,35,'Longline Bralette','women-bralettes-longline-bralette',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(38,23,'Saree','women-saree',NULL,5,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(39,38,'Chiffon Saree','women-saree-chiffon-saree',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(40,38,'Kanjivaram Silk','women-saree-kanjivaram-silk',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(41,23,'Blouse','women-blouse',NULL,6,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(42,41,'Padded Blouse','women-blouse-padded-blouse',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(43,41,'Embroidered Blouse','women-blouse-embroidered-blouse',NULL,2,'2026-11-18 11:13:54','2026-11-18 11:13:54');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collection_product`
--

DROP TABLE IF EXISTS `collection_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collection_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `col_prod_unique` (`collection_id`,`product_id`),
  KEY `collection_product_product_id_foreign` (`product_id`),
  CONSTRAINT `collection_product_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collection_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection_product`
--

LOCK TABLES `collection_product` WRITE;
/*!40000 ALTER TABLE `collection_product` DISABLE KEYS */;
INSERT INTO `collection_product` VALUES (1,2,1,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(2,3,2,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(3,4,2,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(4,4,3,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(5,1,4,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(6,2,4,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(7,2,5,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(8,3,6,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(9,4,6,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(10,4,7,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(11,1,8,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(12,2,8,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(13,2,9,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(14,3,10,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(15,4,10,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(16,4,11,'2026-11-19 10:10:05','2026-11-19 10:10:05');
/*!40000 ALTER TABLE `collection_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collections_slug_unique` (`slug`),
  KEY `collections_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collections`
--

LOCK TABLES `collections` WRITE;
/*!40000 ALTER TABLE `collections` DISABLE KEYS */;
INSERT INTO `collections` VALUES (1,'Royal Bridal Trousseau','royal-bridal-trousseau','Handcrafted pure silk and antique zari embroidery heirlooms tailored for sacred wedding celebrations.',NULL,1,1,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(2,'Festive Velvet Edit','festive-velvet-edit','Opulent crimson and emerald velvet sets tailored for celebrations and grand evenings.',NULL,1,2,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(3,'Benarasi Heritage Archive','benarasi-heritage-archive','Timeless handloom motifs woven with pure gold and silver wire by royal artisan clusters.',NULL,1,3,'2026-11-19 10:10:05','2026-11-19 10:10:05'),(4,'Everyday Luxury Silks','everyday-luxury-silks','Weightless mulberry silk satin essentials crafted for effortless daily comfort and grace.',NULL,1,4,'2026-11-19 10:10:05','2026-11-19 10:10:05');
/*!40000 ALTER TABLE `collections` ENABLE KEYS */;
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
INSERT INTO `coupons` VALUES (1,'WELCOME10','percentage',10.00,999.00,1000,0,'2027-05-18',1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(2,'FESTIVE500','fixed',500.00,2499.00,500,0,'2027-02-18',1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(3,'LUXE15','percentage',15.00,1999.00,300,0,'2027-03-18',1,'2026-11-18 11:13:55','2026-11-18 11:13:55');
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
INSERT INTO `custom_pages` VALUES (1,'Royal Bridal Silk Trousseau 2026','bridal-silk-trousseau','[{\"type\": \"hero\", \"cta_link\": \"/#products\", \"cta_text\": \"Explore The Edit\", \"headline\": \"Handcrafted Bridal Grandeur\", \"subheadline\": \"Woven in pure mulberry silk and embellished with hand-carved zardozi motifs for timeless royal elegance.\"}, {\"type\": \"countdown\", \"title\": \"Exclusive Bridal Trunk Show Drops In\", \"target_date\": \"2026-11-23T16:43\"}, {\"type\": \"product_grid\", \"limit\": \"4\", \"title\": \"Iconic Bridal Ensembles\"}, {\"type\": \"testimonials\", \"quote\": \"The craftsmanship of Saaya’s silk robe exceeded all expectations on my wedding morning. Pure royal elegance.\", \"author\": \"Aishwarya Rai Bachchan\", \"location\": \"Mumbai &amp; London\"}, {\"type\": \"faq\", \"answer\": \"Visit our Colaba Flagship or Indiranagar Experience Studio for a one-on-one measurement fitting with our master couturiers.\", \"question\": \"How do I book a private bridal consultation?\"}, {\"type\": \"newsletter\", \"headline\": \"Join The VIP Private Salon\", \"subheadline\": \"Receive private invites to trunk shows and bespoke bridal preview edits.\"}]','Handcrafted Bridal Silk Collection | Haute Couture','Explore the handcrafted bridal trousseau collection in Varanasi pure mulberry silk and French chantilly lace.',1,'2026-11-18 11:13:58','2026-11-18 11:13:58'),(2,'Summer Resort Wear & Loungewear','summer-resort-edit','[{\"type\": \"hero\", \"cta_link\": \"/#products\", \"cta_text\": \"Shop Resort Wear\", \"headline\": \"Sun-Drenched Riviera Chic\", \"subheadline\": \"Ultralight pure mulberry silk designed for Mediterranean sunsets and relaxed private villas.\"}, {\"type\": \"product_grid\", \"limit\": \"4\", \"title\": \"Curated Riviera Looks\"}, {\"type\": \"newsletter\", \"headline\": \"Unlock Complimentary Resort Delivery\", \"subheadline\": \"Subscribe for priority express shipping to over 24 global destinations.\"}]','Breezy Silks & French Lace Loungewear 2026','Lightweight mulberry chemises and French lace sets designed for sun-kissed Riviera getaways.',1,'2026-11-18 11:13:58','2026-11-18 11:13:58');
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
INSERT INTO `delivery_personnel` VALUES (1,'Rahul Sharma','9876543210','rahul.courier@nexon.com','MH 01 AB 1001','bike','active','2026-11-18 11:13:58','2026-11-18 11:13:58'),(2,'Deepak Verma','9876543211','deepak.courier@nexon.com','KA 05 EV 2020','scooter','active','2026-11-18 11:13:58','2026-11-18 11:13:58'),(3,'Sanjay Patil','9876543212','sanjay.fleet@nexon.com','MH 04 VN 8888','van','active','2026-11-18 11:13:58','2026-11-18 11:13:58');
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
INSERT INTO `department_user` VALUES (1,6,2,'Warehouse Manager',1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(2,5,3,'Store Manager',1,'2026-11-18 11:13:56','2026-11-18 11:13:56'),(3,1,4,'Sales Associate',1,'2026-11-18 11:13:56','2026-11-18 11:13:56'),(4,7,5,'Cashier',1,'2026-11-18 11:13:56','2026-11-18 11:13:56'),(5,6,6,'Inventory Analyst',1,'2026-11-18 11:13:56','2026-11-18 11:13:56'),(6,8,7,'Customer Support',1,'2026-11-18 11:13:56','2026-11-18 11:13:56');
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
INSERT INTO `departments` VALUES (1,'Retail Store Sales','SALES',NULL,'Boutique storefront customer styling, floor service, and POS billing.','indigo',1,'2026-11-18 11:13:51','2026-11-18 11:13:51'),(2,'Warehouse & Fulfillment','WH',NULL,'Inventory picking, packing, dispatch, and inter-store stock transfers.','blue',1,'2026-11-18 11:13:51','2026-11-18 11:13:51'),(3,'Atelier & Bespoke Tailoring','ATELIER',NULL,'Garment alterations, custom silhouettes, and bespoke embroidery.','rose',1,'2026-11-18 11:13:51','2026-11-18 11:13:51'),(4,'Customer Care & Concierge','CARE',NULL,'VIP concierge service, order support tickets, and post-purchase follow-up.','emerald',1,'2026-11-18 11:13:51','2026-11-18 11:13:51'),(5,'Store Management & Admin','MGMT',NULL,'General store administration, cash drawer audits, and staff supervision.','purple',1,'2026-11-18 11:13:51','2026-11-18 11:13:51'),(6,'Operations','OPERAT',NULL,NULL,'indigo',1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(7,'POS','POS',NULL,NULL,'indigo',1,'2026-11-18 11:13:56','2026-11-18 11:13:56'),(8,'Support','SUPPOR',NULL,NULL,'indigo',1,'2026-11-18 11:13:56','2026-11-18 11:13:56');
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
INSERT INTO `expense_categories` VALUES (1,'Rent & Store Leases','rent-leases','#3b82f6','Commercial showroom, warehouse, and office rent',1,'2026-11-18 11:13:48','2026-11-18 11:13:48'),(2,'Packaging & Bags','packaging-bags','#10b981','Boxes, tissue wrap, shopping bags, and branded tags',1,'2026-11-18 11:13:48','2026-11-18 11:13:48'),(3,'Courier & Freight Outward','courier-freight','#f59e0b','BlueDart, Delhivery, and express logistics shipping charges',1,'2026-11-18 11:13:48','2026-11-18 11:13:48'),(4,'Salaries, Wages & Stipends','salaries-wages','#8b5cf6','Staff payroll, store cashier wages, and commissions',1,'2026-11-18 11:13:48','2026-11-18 11:13:48'),(5,'Marketing & Paid Ads','marketing-ads','#ec4899','Meta ads, Google Ads, influencers, and creative campaigns',1,'2026-11-18 11:13:48','2026-11-18 11:13:48'),(6,'Electricity & Utilities','utilities','#06b6d4','Store electricity, water, internet, and phone bills',1,'2026-11-18 11:13:48','2026-11-18 11:13:48'),(7,'Maintenance & Fixtures','maintenance-fixtures','#64748b','HVAC repairs, lighting, plumbing, and showroom upkeep',1,'2026-11-18 11:13:48','2026-11-18 11:13:48'),(8,'Software & Cloud SaaS','software-saas','#6366f1','Hosting, POS licenses, Shopify/ERP subscriptions',1,'2026-11-18 11:13:48','2026-11-18 11:13:48');
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
INSERT INTO `faqs` VALUES (1,'How long does domestic shipping usually take?','Domestic courier dispatches take between 2 to 4 business days for major metro cities (Mumbai, Delhi, Bengaluru, Hyderabad). Custom bespoke alterations require an additional 48 hours in our atelier.','Shipping & Delivery',1,1,'2026-11-18 11:13:52','2026-11-18 11:13:52'),(2,'What is the return and exchange window?','We provide a 7-day complimentary return or size exchange window from the date of physical delivery for all unworn garments with original designer tags intact.','Returns & Exchanges',2,1,'2026-11-18 11:13:52','2026-11-18 11:13:52'),(3,'Can I request custom silhouette alterations before dispatch?','Yes, during checkout or immediately after placing your order, you can contact our concierge with your bust, waist, and length measurements for complimentary basic tailoring.','Atelier & Customization',3,1,'2026-11-18 11:13:52','2026-11-18 11:13:52'),(4,'Are B2B GST tax invoices available for wholesale or corporate purchases?','Yes. Simply enter your company name and GSTIN during the checkout step or contact our finance desk to receive an official GST tax invoice with input credit claims.','Payments & Billing',4,1,'2026-11-18 11:13:52','2026-11-18 11:13:52');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_logs`
--

LOCK TABLES `inventory_logs` WRITE;
/*!40000 ALTER TABLE `inventory_logs` DISABLE KEYS */;
INSERT INTO `inventory_logs` VALUES (1,6,'LS-BLU-USOP-BLU-S',-1,13,'order_deduction','POS Walk-in Sale Order #POS-20261119-0001-QCJR1K',8,'2026-11-19 11:32:58','2026-11-19 11:32:58');
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
INSERT INTO `kb_categories` VALUES (1,'Shipping & Delivery','shipping-delivery','Delivery timelines, domestic courier tracking, and white-glove doorstep delivery.','📦',1,1,'2026-11-18 11:13:52','2026-11-18 11:13:52'),(2,'Returns & Exchanges','returns-exchanges','7-day hassle-free returns policy, exchange process, and refund settlement schedules.','🔄',2,1,'2026-11-18 11:13:52','2026-11-18 11:13:52'),(3,'Bespoke Sizing & Fit','sizing-fit','How to take measurements, international sizing conversions, and custom alterations.','📏',3,1,'2026-11-18 11:13:52','2026-11-18 11:13:52'),(4,'Garment Care & Fabric Maintenance','garment-care','Dry cleaning recommendations, silk and velvet preservation, and embroidery care.','✨',4,1,'2026-11-18 11:13:52','2026-11-18 11:13:52'),(5,'Payments & Security','payments-security','Credit card safety, UPI, EMI installment options, and B2B GST tax invoices.','💳',5,1,'2026-11-18 11:13:52','2026-11-18 11:13:52');
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
INSERT INTO `loyalty_tiers` VALUES (1,'Silver Member','silver',0.00,1.00,'1 Point per ₹50 spent. Standard checkout redemption. Seasonal member newsletters.','#94a3b8',1,'2026-11-18 11:13:49','2026-11-18 11:13:49'),(2,'Gold VIP','gold',15000.00,1.25,'1.25x point multiplier. Free express courier delivery on all orders. ₹500 Birthday reward coupon.','#f59e0b',1,'2026-11-18 11:13:49','2026-11-18 11:13:49'),(3,'Platinum Elite','platinum',40000.00,1.50,'1.50x point multiplier. Priority same-day atelier dispatch. Private WhatsApp concierge styling.','#8b5cf6',1,'2026-11-18 11:13:49','2026-11-18 11:13:49'),(4,'Diamond Patron','diamond',100000.00,2.00,'2.0x points multiplier. Complimentary satin packaging with every shipment. Exclusive runway previews.','#06b6d4',1,'2026-11-18 11:13:49','2026-11-18 11:13:49');
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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_01_000000_create_categories_table',1),(5,'2026_01_01_000001_create_products_table',1),(6,'2026_01_01_000002_create_product_colors_table',1),(7,'2026_01_01_000003_create_product_variants_table',1),(8,'2026_01_01_000004_create_templates_table',1),(9,'2026_01_01_000005_create_orders_table',1),(10,'2026_01_01_000006_create_order_items_table',1),(11,'2026_01_01_000007_create_settings_table',1),(12,'2026_01_01_000008_create_coupons_table',1),(13,'2026_01_01_000009_add_coupon_code_to_orders_table',1),(14,'2026_01_01_000010_create_customer_addresses_table',1),(15,'2026_01_01_000011_create_order_requests_table',1),(16,'2026_01_01_000012_create_pincode_zones_table',1),(17,'2026_01_01_000013_create_product_reviews_table',1),(18,'2026_01_01_000014_create_subscribers_table',1),(19,'2026_01_01_000015_add_sku_to_products_table',1),(20,'2026_01_01_000016_create_wishlists_table',1),(21,'2026_01_01_000017_create_addons_table',1),(22,'2026_01_01_000018_create_inventory_logs_table',1),(23,'2026_01_01_000019_add_sku_to_order_items_table',1),(24,'2026_01_01_000020_add_deactivated_at_to_users_table',1),(25,'2026_01_01_000021_add_pos_fields_to_orders_table',1),(26,'2026_01_01_000022_create_suppliers_and_purchase_orders_tables',1),(27,'2026_01_01_000023_create_accounting_sync_logs_table',1),(28,'2026_01_01_000024_create_sms_logs_and_otp_tables',1),(29,'2026_01_01_000025_create_product_batches_and_allocations_tables',1),(30,'2026_01_01_000026_create_roles_and_permissions_tables',1),(31,'2026_01_01_000027_create_push_subscriptions_and_campaigns_tables',1),(32,'2026_01_01_000028_create_outlets_and_stock_transfers_tables',1),(33,'2026_01_01_000029_create_custom_pages_table',1),(34,'2026_01_01_000030_create_delivery_personnel_and_order_dispatch_tables',1),(35,'2026_01_01_000031_add_long_description_to_products_table',1),(36,'2026_01_01_000032_add_parent_id_to_categories_table',1),(37,'2026_01_01_000033_add_image_to_categories_table',1),(38,'2026_01_01_000034_create_attributes_and_product_values_tables',1),(39,'2026_01_01_000035_create_customer_groups_and_user_pivot_tables',1),(40,'2026_01_01_000036_create_supplier_invoices_and_payments_tables',1),(41,'2026_01_01_000037_create_purchase_returns_and_items_tables',1),(42,'2026_01_01_000038_create_pos_shifts_and_movements_tables',1),(43,'2026_01_01_000039_create_expenses_and_expense_categories_tables',1),(44,'2026_01_01_000040_create_commercial_invoices_and_items_tables',1),(45,'2026_01_01_000041_create_abandoned_carts_table',1),(46,'2026_01_01_000042_create_loyalty_program_tables',1),(47,'2026_01_01_000043_create_payroll_tables',1),(48,'2026_01_01_000044_create_performance_management_tables',1),(49,'2026_01_01_000045_create_departments_and_shifts_tables',1),(50,'2026_01_01_000046_create_staff_attendances_table',1),(51,'2026_01_01_000047_create_support_tickets_tables',1),(52,'2026_01_01_000048_create_knowledge_base_tables',1),(53,'2026_01_01_000049_create_faqs_table',1),(54,'2026_01_01_000050_add_staff_fields_to_users_table',1),(55,'2026_01_01_000051_add_city_to_product_reviews_table',1),(56,'2026_01_01_000052_add_otp_fields_to_users_table',1),(57,'2026_01_01_000053_create_brands_and_addon_product_tables',1),(58,'2026_01_01_000054_add_two_factor_columns_to_users_table',1),(59,'2026_11_08_110155_add_cover_image_to_product_colors_table',1),(60,'2026_11_14_000001_add_staff_to_users_role_enum',1),(61,'2026_11_14_000002_add_tax_amount_to_orders_table',1),(62,'2026_11_14_000003_expand_phone_otps_otp_column',1),(63,'2026_11_16_000001_expand_users_otp_code_column',1),(64,'2026_11_19_000001_create_tags_and_product_tag_tables',2),(65,'2026_11_19_000002_create_collections_and_collection_product_tables',3),(66,'2026_11_19_170000_add_tax_rate_to_supplier_invoices_table',4);
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,10,11,28,NULL,'Midnight Chantilly Lace Babydoll','midnight-chantilly-lace-babydoll','Midnight Navy','#1E1B4B','S',NULL,2799,3299,1,2799,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(2,2,1,1,1,NULL,'Zari Bloom Embroidered Bralette','zari-bloom-embroidered-bralette','Crimson Gold','#9B1D20','S',NULL,1899,2499,2,3798,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(3,2,5,6,13,NULL,'Ivory Pearl Bridal Bustier','ivory-pearl-bridal-bustier','Ivory Pearl','#F8F6F0','S',NULL,4999,5999,2,9998,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(4,3,1,1,1,NULL,'Zari Bloom Embroidered Bralette','zari-bloom-embroidered-bralette','Crimson Gold','#9B1D20','S',NULL,1899,2499,1,1899,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(5,3,5,6,13,NULL,'Ivory Pearl Bridal Bustier','ivory-pearl-bridal-bustier','Ivory Pearl','#F8F6F0','S',NULL,4999,5999,2,9998,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(6,4,1,1,1,NULL,'Zari Bloom Embroidered Bralette','zari-bloom-embroidered-bralette','Crimson Gold','#9B1D20','S',NULL,1899,2499,2,3798,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(7,4,5,6,13,NULL,'Ivory Pearl Bridal Bustier','ivory-pearl-bridal-bustier','Ivory Pearl','#F8F6F0','S',NULL,4999,5999,1,4999,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(8,4,8,9,22,NULL,'Ratri Saree','ratri-saree','Black','#000000','S',NULL,3299,3999,2,6598,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(9,5,9,10,25,NULL,'Lavender Pure Silk Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','Pastel Lavender','#C084FC','S',NULL,3899,4599,2,7798,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(10,5,10,11,28,NULL,'Midnight Chantilly Lace Babydoll','midnight-chantilly-lace-babydoll','Midnight Navy','#1E1B4B','S',NULL,2799,3299,1,2799,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(11,6,3,4,9,NULL,'Noir Underwire Contour Bodysuit','noir-underwire-contour-bodysuit','Midnight Black','#111111','XS',NULL,3199,3999,2,6398,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(12,7,4,5,12,NULL,'Champagne Silk Kimono Robe','champagne-silk-kimono-robe','Champagne Gold','#E5D3B3','Free Size',NULL,4499,5499,1,4499,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(13,8,2,3,6,NULL,'Blush Velvet Chantilly Lace Set','blush-velvet-chantilly-lace-set','Blush Rose','#D8A47F','S',NULL,2899,NULL,1,2899,'2026-11-19 11:32:58','2026-11-19 11:32:58');
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
  `tax_amount` int unsigned NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,NULL,'SAA-20261014-0001','online','pending',2799,50,40,0,NULL,0,2889,NULL,NULL,'cod','pending',NULL,'Ananya Kapoor','+91 98201 12345','ananya.k@gmail.com','Flat 402, Sea Green Apts, Juhu Tara Road','Opposite Palm Beach Hotel','Mumbai','Maharashtra','400049',NULL,NULL,NULL,NULL,'Please call before delivery. Ring bell twice.','2026-11-18 09:13:55','2026-11-18 11:13:58',2,'assigned',NULL,NULL),(2,1,NULL,'SAA-20261014-0002','online','shipped',13796,0,0,200,NULL,0,13596,NULL,NULL,'upi','paid','UPI/261014/98127391','Rhea Sen','+91 99302 98765','rhea.sen@outlook.com','Villa 12, Sobha Malabar, Lavelle Road','Near Cubbon Park','Bengaluru','Karnataka','560001',NULL,NULL,'2026-11-18 09:13:58',NULL,'Leave with concierge if not available.','2026-11-18 07:13:55','2026-11-18 11:13:58',1,'out_for_delivery',NULL,NULL),(3,NULL,NULL,'SAA-20261013-0001','online','delivered',11897,0,0,0,NULL,0,11897,NULL,NULL,'card','paid','TXN_VISA_9831411','Kavita Verma','+91 97110 54321','kavita.v@yahoo.com','B-14, Defence Colony',NULL,'New Delhi','Delhi','110024','BD98231049IN','BlueDart Express',NULL,'2026-11-18 07:13:58','Gift packaging requested.','2026-11-17 09:13:55','2026-11-18 11:13:58',3,'delivered','Mrs. Radhika Merchant','Package handed directly to concierge reception with boutique gift wrap intact.'),(4,1,NULL,'SAA-20261012-0001','online','shipped',15395,0,0,300,NULL,0,15095,NULL,NULL,'upi','paid','UPI/261012/11094821','Sneha Mukherjee','+91 98305 77123','sneha.m@gmail.com','44/2 Ballygunge Circular Road','Opposite Ice Skating Rink','Kolkata','West Bengal','700019','DEL192837465','Delhivery Surface','2026-11-16 06:13:55',NULL,NULL,'2026-11-16 05:13:55','2026-11-18 11:13:55',NULL,'unassigned',NULL,NULL),(5,NULL,NULL,'SAA-20261010-0001','online','delivered',10597,0,0,0,NULL,0,10597,NULL,NULL,'card','paid','TXN_MC_49201948','Natasha D\'Souza','+91 98221 44556','natasha.ds@gmail.com','Row House 4, Silver Sands Estate, Candolim','Near Lemon Tree Amarante','North Goa','Goa','403515','BD77441199IN','BlueDart Air','2026-11-14 04:13:55','2026-11-15 11:13:55','Delivered safely at front desk.','2026-11-13 07:13:55','2026-11-18 11:13:55',NULL,'unassigned',NULL,NULL),(6,1,NULL,'SAA-20261008-0001','online','delivered',6398,50,40,0,NULL,0,6488,NULL,NULL,'cod','paid',NULL,'Meera Joshi','+91 94230 66789','meera.joshi@gmail.com','Plot 89, Kalyani Nagar','Lane 4, Near Joggers Park','Pune','Maharashtra','411006','DTDC66554433','DTDC Express','2026-11-12 05:13:55','2026-11-13 09:13:55','Cash collected on delivery.','2026-11-11 10:13:55','2026-11-18 11:13:55',NULL,'unassigned',NULL,NULL),(7,NULL,NULL,'SAA-20261005-0001','online','cancelled',4499,0,0,0,NULL,0,4499,NULL,NULL,'upi','refunded','UPI/261005/77889900','Tara Singhania','+91 98199 00112','tara.singh@gmail.com','101, Golf Links Tower','Near DLF Cyber City','Gurugram','Haryana','122002',NULL,NULL,NULL,NULL,'Customer cancelled prior to dispatch. Refund processed.','2026-11-09 06:13:55','2026-11-18 11:13:55',NULL,'unassigned',NULL,NULL),(8,NULL,8,'POS-20261119-0001-QCJR1K','pos','delivered',2899,0,0,0,NULL,442,2899,2899,0,'cash','paid','CASH-TENDERED','Walk-in Retail Guest','9999999999','walkin@pos.store','Over The Counter POS',NULL,'Storefront','Storefront','560001',NULL,NULL,NULL,'2026-11-19 11:32:58','Walk-in retail POS checkout','2026-11-19 11:32:58','2026-11-19 11:32:58',NULL,'unassigned',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outlet_stocks`
--

LOCK TABLES `outlet_stocks` WRITE;
/*!40000 ALTER TABLE `outlet_stocks` DISABLE KEYS */;
INSERT INTO `outlet_stocks` VALUES (1,1,1,12,'SHOWCASE-A1','2026-11-18 11:13:57','2026-11-18 11:13:57'),(2,2,1,8,'RACK-B1','2026-11-18 11:13:57','2026-11-18 11:13:57'),(3,3,1,45,'PALLET-C1','2026-11-18 11:13:57','2026-11-18 11:13:57'),(4,1,2,13,'SHOWCASE-A2','2026-11-18 11:13:57','2026-11-18 11:13:57'),(5,2,2,9,'RACK-B2','2026-11-18 11:13:57','2026-11-18 11:13:57'),(6,3,2,48,'PALLET-C2','2026-11-18 11:13:57','2026-11-18 11:13:57'),(7,1,3,14,'SHOWCASE-A3','2026-11-18 11:13:57','2026-11-18 11:13:57'),(8,2,3,10,'RACK-B3','2026-11-18 11:13:57','2026-11-18 11:13:57'),(9,3,3,51,'PALLET-C3','2026-11-18 11:13:57','2026-11-18 11:13:57'),(10,1,4,15,'SHOWCASE-A4','2026-11-18 11:13:57','2026-11-18 11:13:57'),(11,2,4,11,'RACK-B4','2026-11-18 11:13:57','2026-11-18 11:13:57'),(12,3,4,54,'PALLET-C4','2026-11-18 11:13:57','2026-11-18 11:13:57'),(13,1,5,16,'SHOWCASE-A5','2026-11-18 11:13:57','2026-11-18 11:13:57'),(14,2,5,8,'RACK-B5','2026-11-18 11:13:57','2026-11-18 11:13:57'),(15,3,5,57,'PALLET-C5','2026-11-18 11:13:57','2026-11-18 11:13:57'),(16,1,6,12,'SHOWCASE-A6','2026-11-18 11:13:57','2026-11-18 11:13:57'),(17,2,6,9,'RACK-B6','2026-11-18 11:13:57','2026-11-18 11:13:57'),(18,3,6,60,'PALLET-C6','2026-11-18 11:13:57','2026-11-18 11:13:57'),(19,1,7,13,'SHOWCASE-A7','2026-11-18 11:13:57','2026-11-18 11:13:57'),(20,2,7,10,'RACK-B7','2026-11-18 11:13:57','2026-11-18 11:13:57'),(21,3,7,63,'PALLET-C7','2026-11-18 11:13:57','2026-11-18 11:13:57'),(22,1,8,14,'SHOWCASE-A8','2026-11-18 11:13:57','2026-11-18 11:13:57'),(23,2,8,11,'RACK-B8','2026-11-18 11:13:57','2026-11-18 11:13:57'),(24,3,8,66,'PALLET-C8','2026-11-18 11:13:57','2026-11-18 11:13:57'),(25,1,9,15,'SHOWCASE-A9','2026-11-18 11:13:57','2026-11-18 11:13:57'),(26,2,9,8,'RACK-B9','2026-11-18 11:13:57','2026-11-18 11:13:57'),(27,3,9,69,'PALLET-C9','2026-11-18 11:13:57','2026-11-18 11:13:57'),(28,1,10,16,'SHOWCASE-A10','2026-11-18 11:13:57','2026-11-18 11:13:57'),(29,2,10,9,'RACK-B10','2026-11-18 11:13:57','2026-11-18 11:13:57'),(30,3,10,72,'PALLET-C10','2026-11-18 11:13:57','2026-11-18 11:13:57');
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
INSERT INTO `outlets` VALUES (1,'Colaba Flagship Boutique','MUM-COL-01','store','Ground Floor, Regal Circle, Colaba Causeway, Mumbai 400001','Mumbai','+91 22 2202 1100','colaba@nexon.com',1,1,'all','2026-11-18 11:13:57','2026-11-18 11:13:57'),(2,'Indiranagar Experience Studio','BLR-IND-02','store','100ft Road, HAL 2nd Stage, Indiranagar, Bengaluru 560038','Bengaluru','+91 80 4112 3344','bengaluru@nexon.com',0,1,'pos','2026-11-18 11:13:57','2026-11-18 11:13:57'),(3,'Bhiwandi Central Fulfillment Hub','BHW-LOG-03','warehouse','Warehouse Bay 14, Mumbai-Nashik Expressway, Bhiwandi 421302','Bhiwandi','+91 2522 667788','warehouse@nexon.com',0,1,'online','2026-11-18 11:13:57','2026-11-18 11:13:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'View Administrative Dashboard & Metrics','dashboard.view','dashboard','2026-11-18 11:13:56','2026-11-18 11:13:56'),(2,'View Inventory & Stock','inventory.view','inventory','2026-11-18 11:13:56','2026-11-18 11:13:56'),(3,'Manage Inventory & Adjust Stock','inventory.manage','inventory','2026-11-18 11:13:56','2026-11-18 11:13:56'),(4,'View Demand Forecasting','inventory.forecasting','inventory','2026-11-18 11:13:56','2026-11-18 11:13:56'),(5,'Manage Batches & FIFO Expiry','inventory.batches','inventory','2026-11-18 11:13:56','2026-11-18 11:13:56'),(6,'Access POS Cashier Terminal','pos.access','pos','2026-11-18 11:13:56','2026-11-18 11:13:56'),(7,'Execute POS Orders & Tender Cash','pos.checkout','pos','2026-11-18 11:13:56','2026-11-18 11:13:56'),(8,'View Orders List & Details','orders.view','orders','2026-11-18 11:13:56','2026-11-18 11:13:56'),(9,'Update Order Status & Tracking','orders.manage','orders','2026-11-18 11:13:56','2026-11-18 11:13:56'),(10,'Export Orders CSV','orders.export','orders','2026-11-18 11:13:56','2026-11-18 11:13:56'),(11,'View Products Catalog','products.view','products','2026-11-18 11:13:56','2026-11-18 11:13:56'),(12,'Create & Edit Products & SKUs','products.manage','products','2026-11-18 11:13:56','2026-11-18 11:13:56'),(13,'View Financial & Tax Reports','reports.view','reports','2026-11-18 11:13:56','2026-11-18 11:13:56'),(14,'Export Reports CSV & PDF','reports.export','reports','2026-11-18 11:13:56','2026-11-18 11:13:56'),(15,'Configure General Store Settings','settings.manage','settings','2026-11-18 11:13:56','2026-11-18 11:13:56'),(16,'Manage Payment Gateways','settings.gateways','settings','2026-11-18 11:13:56','2026-11-18 11:13:56'),(17,'Manage SMS & OTP Gateways','settings.sms','settings','2026-11-18 11:13:56','2026-11-18 11:13:56'),(18,'Manage Staff Roles & Permissions','roles.manage','roles','2026-11-18 11:13:56','2026-11-18 11:13:56'),(19,'View Customers Directory & Profiles','customers.view','customers','2026-11-18 11:13:56','2026-11-18 11:13:56'),(20,'Manage Customers & Segments','customers.manage','customers','2026-11-18 11:13:57','2026-11-18 11:13:57'),(21,'Manage Staff Payroll & Payslips','payroll.manage','payroll','2026-11-18 11:13:57','2026-11-18 11:13:57'),(22,'Export Payroll Bank Files','payroll.export','payroll','2026-11-18 11:13:57','2026-11-18 11:13:57');
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
  `otp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `pincode_zones` VALUES (1,'400001','Mumbai','Maharashtra',1,2,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(2,'400050','Mumbai','Maharashtra',1,2,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(3,'110001','New Delhi','Delhi',1,2,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(4,'560001','Bengaluru','Karnataka',1,3,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(5,'700001','Kolkata','West Bengal',1,3,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(6,'600001','Chennai','Tamil Nadu',1,3,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(7,'500001','Hyderabad','Telangana',1,3,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(8,'380001','Ahmedabad','Gujarat',1,3,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(9,'302001','Jaipur','Rajasthan',1,3,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(10,'226001','Lucknow','Uttar Pradesh',1,4,1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(11,'411001','Pune','Maharashtra',1,2,1,'2026-11-18 11:13:55','2026-11-18 11:13:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_batches`
--

LOCK TABLES `product_batches` WRITE;
/*!40000 ALTER TABLE `product_batches` DISABLE KEYS */;
INSERT INTO `product_batches` VALUES (1,1,'BATCH-LS-ZAR-IW87-CRI-S-01','LOT-2026-A1','2026-09-18','2028-09-18',30,15,854.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(2,2,'BATCH-LS-ZAR-IW87-CRI-M-01','LOT-2026-A2','2026-09-18','2028-09-18',30,20,854.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(3,3,'BATCH-LS-ZAR-IW87-CRI-L-01','LOT-2026-A3','2026-09-18','2028-09-18',30,10,899.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(4,4,'BATCH-LS-ZAR-IW87-NOI-S-01','LOT-2026-A4','2026-09-18','2028-09-18',30,8,854.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(5,5,'BATCH-LS-ZAR-IW87-NOI-M-01','LOT-2026-A5','2026-09-18','2028-09-18',30,12,854.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(6,6,'BATCH-LS-BLU-USOP-BLU-S-01','LOT-2026-A6','2026-09-18','2028-09-18',30,14,1304.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(7,7,'BATCH-LS-BLU-USOP-BLU-M-01','LOT-2026-A7','2026-09-18','2028-09-18',30,18,1304.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(8,8,'BATCH-LS-BLU-USOP-BLU-L-01','LOT-2026-A8','2026-09-18','2028-09-18',30,6,1349.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(9,9,'BATCH-LS-NOI-S9VS-MID-XS-01','LOT-2026-A9','2026-09-18','2028-09-18',30,5,1439.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58'),(10,10,'BATCH-LS-NOI-S9VS-MID-S-01','LOT-2026-A10','2026-09-18','2028-09-18',30,10,1439.00,'active','QC inspected and certified pure mulberry silk batch.','2026-11-18 11:13:58','2026-11-18 11:13:58');
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_color_images`
--

LOCK TABLES `product_color_images` WRITE;
/*!40000 ALTER TABLE `product_color_images` DISABLE KEYS */;
INSERT INTO `product_color_images` VALUES (1,1,'products/zari-bloom-crimson.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(2,2,'products/zari-bloom-noir.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(4,4,'products/noir-contour-bodysuit.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(5,5,'products/champagne-silk-robe.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(6,6,'products/ivory-pearl-bustier.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(7,7,'products/petal-soft-modal.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(8,8,'products/scarlet-corset-top.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(9,9,'products/emerald-satin-chemise.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(10,10,'products/lavender-silk-pajama.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(11,11,'products/midnight-lace-babydoll.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(12,12,'products/pearl-white-slip-dress.jpg',0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(13,3,'products/lingerie-sets/blush-velvet-chantilly-lace-set-ls-blu-usop-wttrbc.avif',1,'2026-11-19 12:09:05','2026-11-19 12:09:33'),(14,3,'products/lingerie-sets/blush-velvet-chantilly-lace-set-ls-blu-usop-iylncl.avif',2,'2026-11-19 12:09:06','2026-11-19 12:09:33'),(15,3,'products/lingerie-sets/blush-velvet-chantilly-lace-set-ls-blu-usop-uw8agl.avif',3,'2026-11-19 12:09:06','2026-11-19 12:09:33'),(16,3,'products/lingerie-sets/blush-velvet-chantilly-lace-set-ls-blu-usop-p1y5we.avif',4,'2026-11-19 12:09:06','2026-11-19 12:09:33'),(17,3,'products/lingerie-sets/blush-velvet-chantilly-lace-set-ls-blu-usop-19oah6.avif',5,'2026-11-19 12:09:06','2026-11-19 12:09:33'),(18,3,'products/lingerie-sets/blush-velvet-chantilly-lace-set-ls-blu-usop-5d6qnl.avif',6,'2026-11-19 12:09:06','2026-11-19 12:09:33');
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
INSERT INTO `product_colors` VALUES (1,1,'Crimson Gold','#9B1D20',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(2,1,'Noir Onyx','#1A1A1A',NULL,1,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(3,2,'Blush Rose','#d8a47f','products/lingerie-sets/blush-velvet-chantilly-lace-set-ls-blu-usop-cxaoq5.avif',0,'2026-11-18 11:13:54','2026-11-19 12:09:05'),(4,3,'Midnight Black','#111111',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(5,4,'Champagne Gold','#E5D3B3',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(6,5,'Ivory Pearl','#F8F6F0',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(7,6,'Dusty Rose','#C48B9F',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(8,7,'Scarlet Red','#B91C1C',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(9,8,'Black','#000000',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(10,9,'Pastel Lavender','#C084FC',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(11,10,'Midnight Navy','#1E1B4B',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(12,11,'Pearl White','#FDFBF7',NULL,0,'2026-11-18 11:13:54','2026-11-18 11:13:54');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (1,1,NULL,'Sunita Kulkarni','Mumbai',5,'Pure & authentic quality','The freshness and quality took me right back to our traditional farm roots. You can feel the purity and meticulous care in every single harvest and stitch.',1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(2,2,NULL,'Dr. Rajesh Menon','Pune',5,'Remarkable standards','We switched to their certified organic essentials 6 months ago. The aroma is heavenly and the authentic quality has genuinely improved our daily lifestyle.',1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(3,3,NULL,'Ananya Patel','Bengaluru',5,'Punctual & eco-friendly','Express door delivery is always punctual and the packaging is completely eco-friendly with zero single-use plastics. Highly recommend to everyone!',1,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(4,4,NULL,'Priya Sharma','New Delhi',5,'Exceptional comfort & fit','Heirloom craftsmanship and breathable, skin-friendly fabrics. Delicate yet remarkably durable for everyday elegance.',1,'2026-11-18 11:13:55','2026-11-18 11:13:55');
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_tag`
--

DROP TABLE IF EXISTS `product_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_tag` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_tag_product_id_tag_id_unique` (`product_id`,`tag_id`),
  KEY `product_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `product_tag_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_tag`
--

LOCK TABLES `product_tag` WRITE;
/*!40000 ALTER TABLE `product_tag` DISABLE KEYS */;
INSERT INTO `product_tag` VALUES (1,1,5,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(2,1,1,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(3,7,1,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(4,7,4,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(5,2,4,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(6,2,3,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(7,10,3,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(8,10,7,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(9,3,7,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(10,3,6,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(11,4,6,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(12,4,8,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(13,8,8,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(14,8,2,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(15,9,2,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(16,5,5,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(17,5,1,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(18,11,1,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(19,11,4,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(20,6,4,'2026-11-19 09:47:01','2026-11-19 09:47:01'),(21,6,3,'2026-11-19 09:47:01','2026-11-19 09:47:01');
/*!40000 ALTER TABLE `product_tag` ENABLE KEYS */;
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
INSERT INTO `product_variants` VALUES (1,1,1,1,'S','LS-ZAR-IW87-CRI-S',1899.00,2499.00,854.00,15,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(2,1,1,1,'M','LS-ZAR-IW87-CRI-M',1899.00,2499.00,854.00,20,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(3,1,1,1,'L','LS-ZAR-IW87-CRI-L',1999.00,2499.00,899.00,10,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(4,1,2,1,'S','LS-ZAR-IW87-NOI-S',1899.00,2499.00,854.00,8,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(5,1,2,1,'M','LS-ZAR-IW87-NOI-M',1899.00,2499.00,854.00,12,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(6,2,3,1,'S','LS-BLU-USOP-BLU-S',2899.00,3499.00,1304.00,13,5,20,'2026-11-18 11:13:54','2026-11-19 11:32:58'),(7,2,3,1,'M','LS-BLU-USOP-BLU-M',2899.00,3499.00,1304.00,18,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(8,2,3,1,'L','LS-BLU-USOP-BLU-L',2999.00,3499.00,1349.00,6,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(9,3,4,1,'XS','LS-NOI-S9VS-MID-XS',3199.00,3999.00,1439.00,5,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(10,3,4,1,'S','LS-NOI-S9VS-MID-S',3199.00,3999.00,1439.00,10,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:58'),(11,3,4,NULL,'M','LS-NOI-S9VS-MID-M',3199.00,3999.00,NULL,8,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(12,4,5,NULL,'Free Size','LS-CHA-FX90-CHA-FREE SIZE',4499.00,5499.00,NULL,25,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(13,5,6,NULL,'S','LS-IVO-TLGU-IVO-S',4999.00,5999.00,NULL,6,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(14,5,6,NULL,'M','LS-IVO-TLGU-IVO-M',4999.00,5999.00,NULL,8,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(15,5,6,NULL,'L','LS-IVO-TLGU-IVO-L',5199.00,5999.00,NULL,4,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(16,6,7,NULL,'S','LS-PET-VXPU-DUS-S',999.00,1299.00,NULL,30,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(17,6,7,NULL,'M','LS-PET-VXPU-DUS-M',999.00,1299.00,NULL,45,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(18,6,7,NULL,'L','LS-PET-VXPU-DUS-L',999.00,1299.00,NULL,20,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(19,7,8,NULL,'XS','LS-SCA-FHC3-SCA-XS',2499.00,2999.00,NULL,12,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(20,7,8,NULL,'S','LS-SCA-FHC3-SCA-S',2499.00,2999.00,NULL,16,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(21,7,8,NULL,'M','LS-SCA-FHC3-SCA-M',2499.00,2999.00,NULL,14,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(22,8,9,NULL,'S','SARE-RATR-QBL7-BLA-S',3299.00,3999.00,NULL,10,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(23,8,9,NULL,'M','SARE-RATR-QBL7-BLA-M',3299.00,3999.00,NULL,15,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(24,8,9,NULL,'L','SARE-RATR-QBL7-BLA-L',3499.00,3999.00,NULL,8,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(25,9,10,NULL,'S','LS-LAV-RVOX-PAS-S',3899.00,4599.00,NULL,12,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(26,9,10,NULL,'M','LS-LAV-RVOX-PAS-M',3899.00,4599.00,NULL,18,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(27,9,10,NULL,'L','LS-LAV-RVOX-PAS-L',3899.00,4599.00,NULL,9,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(28,10,11,NULL,'S','LS-MID-DOVB-MID-S',2799.00,3299.00,NULL,14,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(29,10,11,NULL,'M','LS-MID-DOVB-MID-M',2799.00,3299.00,NULL,11,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(30,11,12,NULL,'XS','LS-PEA-O8M9-PEA-XS',5499.00,6499.00,NULL,7,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(31,11,12,NULL,'S','LS-PEA-O8M9-PEA-S',5499.00,6499.00,NULL,12,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(32,11,12,NULL,'M','LS-PEA-O8M9-PEA-M',5499.00,6499.00,NULL,9,5,20,'2026-11-18 11:13:54','2026-11-18 11:13:54');
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
INSERT INTO `products` VALUES (1,1,NULL,'Zari Bloom Embroidered Bralette','zari-bloom-embroidered-bralette','LS-ZAR-IW87','Delicate floral embroidery on sheer mesh with gold lurex threading and scallop trims.','','NEW',1,1,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(2,2,NULL,'Blush Velvet Chantilly Lace Set','blush-velvet-chantilly-lace-set','LS-BLU-USOP','Plush stretch velvet paired with French chantilly lace, featuring rose gold hardware.','','BESTSELLER',1,1,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(3,3,NULL,'Noir Underwire Contour Bodysuit','noir-underwire-contour-bodysuit','LS-NOI-S9VS','Sculpting sheer mesh bodysuit with architectural underwire boning and thong back.','','EXCLUSIVE',1,1,0,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(4,4,NULL,'Champagne Silk Kimono Robe','champagne-silk-kimono-robe','LS-CHA-FX90','100% Mulberry silk lounge robe featuring draped kimono sleeves and matching tie belt.','','LUXE',1,1,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(5,5,NULL,'Ivory Pearl Bridal Bustier','ivory-pearl-bridal-bustier','LS-IVO-TLGU','Heirloom quality bridal corset handcrafted with freshwater seed pearls and silk satin.','','BRIDAL',1,1,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(6,6,NULL,'Petal Soft Seamless Modal Bralette','petal-soft-seamless-modal-bralette','LS-PET-VXPU','Second-skin ultra-breathable micro modal with wireless support and clean-cut edges.','','DAILY',1,1,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(7,1,NULL,'Scarlet Underwire Satin Corset Top','scarlet-underwire-satin-corset-top','LS-SCA-FHC3','Zara-inspired high-drama sculpted corset top in lustrous scarlet duchess satin with structured underwire cups.','','TRENDING',1,1,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(8,4,NULL,'Ratri Saree','ratri-saree','SARE-RATR-QBL7','<p><strong>Fabric:</strong> Handwoven tussar by cotton  |  <strong>Blouse Piece:</strong> No  |  <strong>Colour:</strong> Black</p><p>An ode to the magic of Nobomi night. Crafted in deep black handwoven Tussar-by-Cotton, carrying delicate embroidered motifs across its body, finished with intricate lace edging and a statement embroidered pallu.</p>','<div class=\"product-highlights mb-6\"><h3 style=\"font-family: Georgia, serif; color: #1a1a1a; font-size: 1.5rem; margin-bottom: 1rem; border-left: 4px solid #d4af37; padding-left: 12px; line-height: 1.2;\">Product Highlights</h3><ul style=\"list-style: none; padding: 0; margin: 0;\" class=\"space-y-2 text-sm text-gray-700\"><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Works:</span><span style=\"font-weight: 600; color: #111827;\">Embroidery</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Fabric:</span><span style=\"font-weight: 600; color: #111827;\">Handwoven tussar by cotton</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Saree Length:</span><span style=\"font-weight: 600; color: #111827;\">5.5 meters</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Blouse Piece:</span><span style=\"font-weight: 600; color: #111827;\">No</span></li><li style=\"display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;\"><span style=\"color: #6b7280; font-weight: 500;\">Colour:</span><span style=\"font-weight: 600; color: #111827;\">Black</span></li></ul></div><div class=\"story-box mb-6\" style=\"margin-top: 1.5rem;\"><h3 style=\"font-family: Georgia, serif; color: #1a1a1a; font-size: 1.5rem; margin-bottom: 1rem; border-left: 4px solid #d4af37; padding-left: 12px; line-height: 1.2;\">Story</h3><div style=\"padding-left: 4px;\"><p style=\"font-weight: 700; color: #990000; font-size: 1.1rem; margin-bottom: 0.75rem;\">RATRI | রাত্রী</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\">There’s something about <strong style=\"color: #990000;\">Nobomi night</strong>—the lights, the quiet after the chaos, the last few hours of Pujo that feel almost magical.</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\"><strong style=\"color: #990000;\">RATRI</strong> is an ode to that night.</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\">Crafted in a beautiful handwoven Tussar-by-Cotton, this deep black saree carries delicate embroidered <span style=\"color: #990000; font-weight: 600;\">motifs</span> across its body, finished with intricate lace edging and a statement embroidered pallu.</p><p style=\"margin-bottom: 0.75rem; line-height: 1.7; color: #374151;\">Understated yet dramatic, traditional yet effortlessly contemporary—<strong style=\"color: #990000;\">RATRI</strong> is made for the woman who lets the night do the talking.</p><p style=\"margin-top: 1rem; font-style: italic; color: #990000; font-weight: 600;\">RATRI — An ode to the night. A final chapter of Pujo.</p></div></div><div class=\"care-and-disclaimer\" style=\"margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;\"><div style=\"margin-bottom: 1rem;\"><span style=\"font-size: 0.95rem; font-weight: 700; color: #111827; display: block; margin-bottom: 0.25rem;\">Wash &amp; Care:</span><p style=\"font-size: 0.875rem; color: #4b5563; margin: 0; line-height: 1.5;\">Dry clean only recommended to maintain the longevity of the fabric.</p></div><div style=\"margin-bottom: 1.25rem;\"><span style=\"font-size: 0.95rem; font-weight: 700; color: #111827; display: block; margin-bottom: 0.25rem;\">Production Time:</span><p style=\"font-size: 0.875rem; color: #4b5563; margin: 0; line-height: 1.5;\">Handcrafted artisanal saree. Please allow 10-12 days for production.</p></div><div style=\"background-color: #fff5f5; border-radius: 8px; padding: 14px; border-left: 4px solid #e53e3e;\"><span style=\"font-size: 0.9rem; font-weight: 700; display: block; margin-bottom: 6px; color: #c53030;\">Disclaimer:</span><ul style=\"font-size: 0.825rem; color: #4a5568; margin: 0; padding-left: 18px; line-height: 1.6;\"><li>Photos are captured in natural light to avoid discrepancies; minor shade variations may occur due to individual screen calibration.</li><li>For bespoke customizations, please contact our concierge team. Customized items cannot be returned or exchanged.</li></ul></div></div>','HOT',1,1,0,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(9,4,NULL,'Lavender Pure Silk Lounge Pajama Set','lavender-pure-silk-lounge-pajama-set','LS-LAV-RVOX','Two-piece tailored lounge set in calming pastel lavender mulberry silk with contrasting white piping.','','PREMIUM',1,0,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(10,2,NULL,'Midnight Chantilly Lace Babydoll','midnight-chantilly-lace-babydoll','LS-MID-DOVB','Sensual floating babydoll in sheer midnight navy French lace with matching silk satin briefs.','','LUXE',1,1,0,0,'2026-11-18 11:13:54','2026-11-18 11:13:54'),(11,5,NULL,'Pearl White Silk Slip Evening Dress','pearl-white-silk-slip-evening-dress','LS-PEA-O8M9','Minimalist modern bridal slip dress in heavyweight mulberry silk with delicate cowl neckline and low back.','','BRIDAL',1,1,1,0,'2026-11-18 11:13:54','2026-11-18 11:13:54');
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
INSERT INTO `role_permissions` VALUES (1,1),(2,1),(3,1),(1,2),(2,2),(3,2),(5,2),(1,3),(2,3),(5,3),(1,4),(2,4),(5,4),(1,5),(2,5),(5,5),(1,6),(2,6),(4,6),(1,7),(2,7),(4,7),(1,8),(2,8),(3,8),(6,8),(1,9),(2,9),(3,9),(6,9),(1,10),(2,10),(3,10),(1,11),(2,11),(3,11),(4,11),(5,11),(6,11),(1,12),(2,12),(3,12),(1,13),(2,13),(3,13),(1,14),(2,14),(3,14),(1,15),(3,15),(1,16),(1,17),(1,18),(1,19),(2,19),(3,19),(6,19),(1,20),(2,20),(1,21),(2,21),(1,22);
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
INSERT INTO `roles` VALUES (1,'Super Admin','super-admin','Full unrestricted system access to all store operations, finances, and settings.',1,'2026-11-18 11:13:57','2026-11-18 11:13:57'),(2,'Store Manager','store-manager','Comprehensive management of store items, catalog, inventory, orders, sales billing, POS checkout, and reports.',1,'2026-11-18 11:13:57','2026-11-18 11:13:57'),(3,'eCommerce Manager','ecommerce-manager','Oversees digital storefront operations, online catalog merchandising, web orders fulfillment, and eCommerce analytics.',1,'2026-11-18 11:13:57','2026-11-18 11:13:57'),(4,'POS Cashier','pos-cashier','In-store retail counter operator with access restricted to the POS Terminal.',1,'2026-11-18 11:13:57','2026-11-18 11:13:57'),(5,'Inventory Clerk','inventory-clerk','Warehouse specialist handling receiving, batch tracking, stock counts, and replenishment.',1,'2026-11-18 11:13:57','2026-11-18 11:13:57'),(6,'Customer Support','customer-support','Client assistance representative reviewing orders, shipments, and customer returns.',1,'2026-11-18 11:13:57','2026-11-18 11:13:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'free_shipping_threshold','1499','2026-11-18 11:13:55','2026-11-18 11:13:55'),(2,'shipping_cost','99','2026-11-18 11:13:55','2026-11-18 11:13:55'),(3,'cod_charge','40','2026-11-18 11:13:55','2026-11-18 11:13:55'),(4,'tax_percentage','18','2026-11-18 11:13:55','2026-11-18 11:13:55'),(5,'gstin','27AABCS1429B1ZB','2026-11-18 11:13:55','2026-11-18 11:13:55'),(6,'support_phone','+91 98765 43210','2026-11-18 11:13:55','2026-11-18 11:13:55'),(7,'support_whatsapp','+91 98765 43210','2026-11-18 11:13:55','2026-11-18 11:13:55'),(8,'support_email','support@nexon.app','2026-11-18 11:13:55','2026-11-18 11:13:55'),(9,'store_address','101 Fashion Boulevard, Bandra West, Mumbai, Maharashtra 400050','2026-11-18 11:13:55','2026-11-18 11:13:55'),(10,'enable_cod','1','2026-11-18 11:13:55','2026-11-18 11:13:55'),(11,'maintenance_mode','0','2026-11-18 11:13:55','2026-11-18 11:13:55'),(12,'announcement_bar','Complimentary Shipping on all orders over ₹1,499 | Use code WELCOME10 for 10% off','2026-11-18 11:13:55','2026-11-18 11:13:55'),(13,'site_logo','branding/logo.png','2026-11-19 09:28:24','2026-11-19 09:28:24'),(14,'store_name','Nex-on','2026-11-19 09:28:24','2026-11-19 09:28:24'),(15,'footer_bio','Bridal & premium lingerie, hand-finished in India for every celebrated moment.','2026-11-19 09:28:24','2026-11-19 09:30:47'),(16,'footer_badge1','','2026-11-19 09:28:24','2026-11-19 09:28:24'),(17,'footer_badge2','','2026-11-19 09:28:24','2026-11-19 09:28:24'),(18,'vyra_hero_badge','Drop 03 / Out Now','2026-11-19 09:28:24','2026-11-19 09:28:24'),(19,'vyra_hero_title','Wear Your\r\nPower','2026-11-19 09:28:24','2026-11-19 09:28:24'),(20,'vyra_hero_subtitle','Bold cuts. Mesh, lace, and second-skin fabrics built for a generation that doesn\'t ask permission.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(21,'vyra_hero_btn_text','Shop Drop 03','2026-11-19 09:28:24','2026-11-19 09:28:24'),(22,'vyra_hero_btn_url','#products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(23,'vyra_hero_btn2_text','Explore','2026-11-19 09:28:24','2026-11-19 09:28:24'),(24,'vyra_hero_btn2_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(25,'vyra_curated_badge','Curated','2026-11-19 09:28:24','2026-11-19 09:28:24'),(26,'vyra_curated_title','Curated Featured Picks','2026-11-19 09:28:24','2026-11-19 09:28:24'),(27,'vyra_curated_subtitle','Second-skin fabrics, fearless cuts, and midnight noir lingerie engineered for an unfiltered presence.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(28,'vyra_sale_kicker','⚡ Limited Drops','2026-11-19 09:28:24','2026-11-19 09:28:24'),(29,'vyra_sale_title','Special Sale Offers','2026-11-19 09:28:24','2026-11-19 09:28:24'),(30,'vyra_sale_subtitle','High-impact power silhouettes, sculpting mesh, and daring cuts at exclusive promotional drops.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(31,'buttercup_slide_1_badge','New Season Edit','2026-11-19 09:28:24','2026-11-19 09:28:24'),(32,'buttercup_hero_badge','New Season Edit','2026-11-19 09:28:24','2026-11-19 09:28:24'),(33,'buttercup_slide_1_title','Comfort that\r\nFeels Like Home','2026-11-19 09:28:24','2026-11-19 09:28:24'),(34,'buttercup_hero_title','Comfort that\r\nFeels Like Home','2026-11-19 09:28:24','2026-11-19 09:28:24'),(35,'buttercup_slide_1_subtitle','Buttery-soft everyday lingerie, made for real bodies and real days — because comfort should never be a compromise.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(36,'buttercup_hero_subtitle','Buttery-soft everyday lingerie, made for real bodies and real days — because comfort should never be a compromise.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(37,'buttercup_slide_1_btn1_text','Shop Now','2026-11-19 09:28:24','2026-11-19 09:28:24'),(38,'buttercup_slide_1_btn1_url','#products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(39,'buttercup_slide_1_btn2_text','Explore','2026-11-19 09:28:24','2026-11-19 09:28:24'),(40,'buttercup_slide_1_btn2_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(41,'buttercup_slide_2_badge','Cloud-Soft Intimates','2026-11-19 09:28:24','2026-11-19 09:28:24'),(42,'buttercup_slide_2_title','Second-Skin Ease,\r\nAll Day Long','2026-11-19 09:28:24','2026-11-19 09:28:24'),(43,'buttercup_slide_2_subtitle','Breathable modal and organic cotton with zero pinch. Gentle, weightless support made to move with you.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(44,'buttercup_slide_2_btn1_text','Shop Intimates','2026-11-19 09:28:24','2026-11-19 09:28:24'),(45,'buttercup_slide_2_btn1_url','#products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(46,'buttercup_slide_2_btn2_text','View Collection','2026-11-19 09:28:24','2026-11-19 09:28:24'),(47,'buttercup_slide_2_btn2_url','https://nexon.test/featured-products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(48,'buttercup_slide_3_badge','Pure Indulgence','2026-11-19 09:28:24','2026-11-19 09:28:24'),(49,'buttercup_slide_3_title','Soft as a Whisper,\r\nCrafted for You','2026-11-19 09:28:24','2026-11-19 09:28:24'),(50,'buttercup_slide_3_subtitle','Delicate lace accents meet featherlight silhouettes. Experience the sublime touch of effortless elegance every day.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(51,'buttercup_slide_3_btn1_text','Explore Bestsellers','2026-11-19 09:28:24','2026-11-19 09:28:24'),(52,'buttercup_slide_3_btn1_url','#products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(53,'buttercup_slide_3_btn2_text','Discover More','2026-11-19 09:28:24','2026-11-19 09:28:24'),(54,'buttercup_slide_3_btn2_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(55,'buttercup_slide_4_badge','Wire-Free Freedom','2026-11-19 09:28:24','2026-11-19 09:28:24'),(56,'buttercup_slide_4_title','Zero Pinching,\r\n100% Pure Comfort','2026-11-19 09:28:24','2026-11-19 09:28:24'),(57,'buttercup_slide_4_subtitle','Say goodbye to painful underwires forever. Our contour bands adapt harmoniously to your natural curves.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(58,'buttercup_slide_4_btn1_text','Shop Wire-Free','2026-11-19 09:28:24','2026-11-19 09:28:24'),(59,'buttercup_slide_4_btn1_url','#products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(60,'buttercup_slide_4_btn2_text','Our Fit Guide','2026-11-19 09:28:24','2026-11-19 09:28:24'),(61,'buttercup_slide_4_btn2_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(62,'buttercup_slide_5_badge','Lounge & Sleep','2026-11-19 09:28:24','2026-11-19 09:28:24'),(63,'buttercup_slide_5_title','Dreamy Loungewear\r\nfor Cozy Evenings','2026-11-19 09:28:24','2026-11-19 09:28:24'),(64,'buttercup_slide_5_subtitle','From quiet morning coffee to tranquil midnight slumber — wrap yourself in the soothing bliss of buttery loungewear.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(65,'buttercup_slide_5_btn1_text','Shop Loungewear','2026-11-19 09:28:24','2026-11-19 09:28:24'),(66,'buttercup_slide_5_btn1_url','#products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(67,'buttercup_slide_5_btn2_text','Explore All','2026-11-19 09:28:24','2026-11-19 09:28:24'),(68,'buttercup_slide_5_btn2_url','https://nexon.test/featured-products','2026-11-19 09:28:24','2026-11-19 09:28:24'),(69,'buttercup_curated_badge','Curated','2026-11-19 09:28:24','2026-11-19 09:28:24'),(70,'buttercup_curated_title','Curated Featured Picks','2026-11-19 09:28:24','2026-11-19 09:28:24'),(71,'buttercup_curated_subtitle','Everyday cloud-soft intimates, wire-free comfort, and breathable cotton modal sets made for real bodies.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(72,'buttercup_sale_kicker','Limited Time Deals','2026-11-19 09:28:24','2026-11-19 09:28:24'),(73,'buttercup_sale_title','Special Sale Offers','2026-11-19 09:28:24','2026-11-19 09:28:24'),(74,'buttercup_sale_subtitle','Explore cloud-soft comfort sets and everyday silhouettes at celebratory sale prices.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(75,'lumiere_hero_badge','The Festive Edit','2026-11-19 09:28:24','2026-11-19 09:28:24'),(76,'lumiere_hero_title','Silk that\r\nfeels like light.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(77,'lumiere_hero_subtitle','Sustainably made silk lingerie, finished by hand in India. Everyday elegance, no compromise.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(78,'lumiere_hero_btn_text','Shop the Edit','2026-11-19 09:28:24','2026-11-19 09:28:24'),(79,'lumiere_hero_btn_url','#curatedPicks','2026-11-19 09:28:24','2026-11-19 09:28:24'),(80,'lumiere_hero_btn2_text','Explore All','2026-11-19 09:28:24','2026-11-19 09:28:24'),(81,'lumiere_hero_btn2_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(82,'lumiere_curated_badge','Curated','2026-11-19 09:28:24','2026-11-19 09:28:24'),(83,'lumiere_curated_title','Curated Featured Picks','2026-11-19 09:28:24','2026-11-19 09:28:24'),(84,'lumiere_curated_subtitle','Hand-finished pure mulberry silk, delicate French lace, and heirloom silhouettes curated for effortless elegance.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(85,'lumiere_sale_kicker','Limited Time Deals','2026-11-19 09:28:24','2026-11-19 09:28:24'),(86,'lumiere_sale_title','Special Sale Offers','2026-11-19 09:28:24','2026-11-19 09:28:24'),(87,'lumiere_sale_subtitle','Hand-finished French lace, fine silk, and fine nightwear at celebratory promotional prices.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(88,'saaya_slide_1_badge','Crafted for Her','2026-11-19 09:28:24','2026-11-19 09:28:24'),(89,'saaya_hero_badge','Crafted for Her','2026-11-19 09:28:24','2026-11-19 09:28:24'),(90,'saaya_slide_1_title','Draped in\r\nGold & Grace','2026-11-19 09:28:24','2026-11-19 09:28:24'),(91,'saaya_hero_title','Draped in\r\nGold & Grace','2026-11-19 09:28:24','2026-11-19 09:28:24'),(92,'saaya_slide_1_subtitle','Handcrafted Indian lingerie rooted in tradition, elevated by craft. Each piece is an heirloom waiting to be worn.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(93,'saaya_hero_subtitle','Handcrafted Indian lingerie rooted in tradition, elevated by craft. Each piece is an heirloom waiting to be worn.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(94,'saaya_slide_1_btn_text','Explore Collection','2026-11-19 09:28:24','2026-11-19 09:28:24'),(95,'saaya_slide_1_btn_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(96,'saaya_slide_2_badge','Royal Trousseau Edit','2026-11-19 09:28:24','2026-11-19 09:28:24'),(97,'saaya_slide_2_title','Zari Silk &\r\nVelvet Intimates','2026-11-19 09:28:24','2026-11-19 09:28:24'),(98,'saaya_slide_2_subtitle','Intricate gold wire embroidery and pure mulberry silk, tailored for sacred celebrations and timeless grandeur.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(99,'saaya_slide_2_btn_text','Shop Bridal Silk','2026-11-19 09:28:24','2026-11-19 09:28:24'),(100,'saaya_slide_2_btn_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(101,'saaya_slide_3_badge','Artisanal Heritage','2026-11-19 09:28:24','2026-11-19 09:28:24'),(102,'saaya_slide_3_title','The Benarasi\r\nArchive','2026-11-19 09:28:24','2026-11-19 09:28:24'),(103,'saaya_slide_3_subtitle','Handwoven motifs passed through generations of master ateliers. Where heritage craft meets modern form.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(104,'saaya_slide_3_btn_text','Discover The Archive','2026-11-19 09:28:24','2026-11-19 09:28:24'),(105,'saaya_slide_3_btn_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(106,'saaya_slide_4_badge','Festive Elegance','2026-11-19 09:28:24','2026-11-19 09:28:24'),(107,'saaya_slide_4_title','Crimson Whisper\r\nCapsule','2026-11-19 09:28:24','2026-11-19 09:28:24'),(108,'saaya_slide_4_subtitle','Weightless tulle drenched in ceremonial crimson, edged with delicate antique gota patti work.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(109,'saaya_slide_4_btn_text','Explore Capsule','2026-11-19 09:28:24','2026-11-19 09:28:24'),(110,'saaya_slide_4_btn_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(111,'saaya_slide_5_badge','Bespoke Craft','2026-11-19 09:28:24','2026-11-19 09:28:24'),(112,'saaya_slide_5_title','Pure Heirloom\r\nComfort','2026-11-19 09:28:24','2026-11-19 09:28:24'),(113,'saaya_slide_5_subtitle','Soft uncrushable silk satin crafted to drape seamlessly under royal lehengas and festive silhouettes.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(114,'saaya_slide_5_btn_text','Shop Collection','2026-11-19 09:28:24','2026-11-19 09:28:24'),(115,'saaya_slide_5_btn_url','https://nexon.test/search','2026-11-19 09:28:24','2026-11-19 09:28:24'),(116,'saaya_curated_badge','Curated','2026-11-19 09:28:24','2026-11-19 09:28:24'),(117,'saaya_curated_title','Curated Featured Picks','2026-11-19 09:28:24','2026-11-19 09:28:24'),(118,'saaya_curated_subtitle','Handcrafted heirlooms shaped with pure mulberry silk, delicate jaali embroidery, and timeless silhouettes.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(119,'saaya_sale_kicker','Limited Time Deals','2026-11-19 09:28:24','2026-11-19 09:28:24'),(120,'saaya_sale_title','Special Sale Offers','2026-11-19 09:28:24','2026-11-19 09:28:24'),(121,'saaya_sale_subtitle','Exclusive heirlooms and seasonal lingerie edits at celebratory promotional prices.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(122,'organic_hero_badge','🌱 100% Certified Farm-to-Table','2026-11-19 09:28:24','2026-11-19 09:28:24'),(123,'organic_hero_title','Organic Foods at your Doorsteps','2026-11-19 09:28:24','2026-11-19 09:28:24'),(124,'organic_hero_subtitle','Pure, wholesome produce cultivated by trusted Indian organic farmers. Zero harmful pesticides, zero synthetic chemicals—just real nutrition harvested daily.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(125,'organic_hero_btn1','Start Shopping','2026-11-19 09:28:24','2026-11-19 09:28:24'),(126,'organic_hero_btn2','Explore Featured','2026-11-19 09:28:24','2026-11-19 09:28:24'),(127,'organic_metric1_num','1,200+','2026-11-19 09:28:24','2026-11-19 09:28:24'),(128,'organic_metric1_label','Organic Varieties','2026-11-19 09:28:24','2026-11-19 09:28:24'),(129,'organic_metric2_num','50k+','2026-11-19 09:28:24','2026-11-19 09:28:24'),(130,'organic_metric2_label','Happy Patrons','2026-11-19 09:28:24','2026-11-19 09:28:24'),(131,'organic_metric3_num','100%','2026-11-19 09:28:24','2026-11-19 09:28:24'),(132,'organic_metric3_label','Purity Assured','2026-11-19 09:28:24','2026-11-19 09:28:24'),(133,'organic_prop1_title','Fresh from the Farm','2026-11-19 09:28:24','2026-11-19 09:28:24'),(134,'organic_prop1_desc','Harvested and dispatched within 24 hours for peak nutritional potency.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(135,'organic_prop2_title','100% Certified Organic','2026-11-19 09:28:24','2026-11-19 09:28:24'),(136,'organic_prop2_desc','Verified non-GMO, chemical-free crops certified under NPOP & FSSAI norms.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(137,'organic_prop3_title','Free Express Delivery','2026-11-19 09:28:24','2026-11-19 09:28:24'),(138,'organic_prop3_desc','Complimentary door delivery on orders over ₹1,499 with temperature control.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(139,'organic_ad1_kicker','Limited Batch','2026-11-19 09:28:24','2026-11-19 09:28:24'),(140,'organic_ad1_title','Raw Wild Forest Honey','2026-11-19 09:28:24','2026-11-19 09:28:24'),(141,'organic_ad1_desc','Unprocessed, pure nectar directly from Nilgiri biosphere reserves.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(142,'organic_ad1_btn','Shop Wild Honey','2026-11-19 09:28:24','2026-11-19 09:28:24'),(143,'organic_ad1_url','','2026-11-19 09:28:24','2026-11-19 09:28:24'),(144,'organic_ad2_kicker','Seasonal Deal','2026-11-19 09:28:24','2026-11-19 09:28:24'),(145,'organic_ad2_title','Cold-Pressed Wood Oils','2026-11-19 09:28:24','2026-11-19 09:28:24'),(146,'organic_ad2_desc','Traditional Kolhu pressed mustard, sesame, and groundnut oils.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(147,'organic_ad2_btn','Explore Cold-Pressed','2026-11-19 09:28:24','2026-11-19 09:28:24'),(148,'organic_ad2_url','','2026-11-19 09:28:24','2026-11-19 09:28:24'),(149,'organic_ad3_kicker','Daily Essentials','2026-11-19 09:28:24','2026-11-19 09:28:24'),(150,'organic_ad3_title','A2 Desi Gir Cow Ghee','2026-11-19 09:28:24','2026-11-19 09:28:24'),(151,'organic_ad3_desc','Bilona churned cultured butter from free-grazing indigenous cows.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(152,'organic_ad3_btn','Discover A2 Ghee','2026-11-19 09:28:24','2026-11-19 09:28:24'),(153,'organic_ad3_url','','2026-11-19 09:28:24','2026-11-19 09:28:24'),(154,'organic_news_badge','🌱 Join the Organic Family','2026-11-19 09:28:24','2026-11-19 09:28:24'),(155,'organic_news_title','Get ₹200 Off Your First Fresh Harvest','2026-11-19 09:28:24','2026-11-19 09:28:24'),(156,'organic_news_subtitle','Subscribe to our weekly seasonal harvest alerts, farm updates, and exclusive patron discounts. No spam, just pure organic goodness.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(157,'organic_top_notice','100% Certified Organic • Direct from Local Farmers & Sustainable Harvests','2026-11-19 09:28:24','2026-11-19 09:28:24'),(158,'organic_top_shipping_notice','Delivery to All Major Indian Pincodes','2026-11-19 09:28:24','2026-11-19 09:28:24'),(159,'organic_customer_care','+91 98765 43210','2026-11-19 09:28:24','2026-11-19 09:28:24'),(160,'organic_support_email','support@nexon.app','2026-11-19 09:28:24','2026-11-19 09:28:24'),(161,'ticker_enabled','1','2026-11-19 09:28:24','2026-11-19 09:28:24'),(162,'ticker_item_1','Free Delivery on orders over <strong>₹1499</strong>','2026-11-19 09:28:24','2026-11-19 09:28:24'),(163,'ticker_item_2','Upto 500/- off for selected styles.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(164,'ticker_item_3','Use code <strong>WELCOME5</strong> for 5% OFF','2026-11-19 09:28:24','2026-11-19 09:28:24'),(165,'ticker_item_4','Accepting UPI payments','2026-11-19 09:28:24','2026-11-19 09:28:24'),(166,'ticker_item_5','Call us: <strong>+91 98765 43210</strong>','2026-11-19 09:28:24','2026-11-19 09:28:24'),(167,'vyra_story_badge','The Brand','2026-11-19 09:28:24','2026-11-19 09:28:24'),(168,'vyra_about_philosophy_badge','Philosophy 01','2026-11-19 09:28:24','2026-11-19 09:28:24'),(169,'vyra_story_title','Not Made to Blend In.\r\nMade to Rule.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(170,'vyra_about_philosophy_heading','Raw Edge. Zero Compromise.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(171,'vyra_story_p1','Vyra was created for those who treat style as armour. Born in the intersection of high fashion and street energy, every stitch is calibrated for raw confidence.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(172,'vyra_story_p2','Architectural seams, breathable sheer mesh, and unyielding contours. Designed without apologies, crafted for the fearless.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(173,'vyra_stat1_num','0%','2026-11-19 09:28:24','2026-11-19 09:28:24'),(174,'vyra_stat1_label','Compromise','2026-11-19 09:28:24','2026-11-19 09:28:24'),(175,'vyra_stat2_num','50K+','2026-11-19 09:28:24','2026-11-19 09:28:24'),(176,'vyra_stat2_label','Rebels Styled','2026-11-19 09:28:24','2026-11-19 09:28:24'),(177,'vyra_stat3_num','100%','2026-11-19 09:28:24','2026-11-19 09:28:24'),(178,'vyra_stat3_label','Raw Edge','2026-11-19 09:28:24','2026-11-19 09:28:24'),(179,'vyra_pillar1_title','Architectural Seams','2026-11-19 09:28:24','2026-11-19 09:28:24'),(180,'vyra_pillar1_desc','Precision-engineered sheer mesh cut lines that sculpt your silhouette with zero bulk and maximum comfort.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(181,'vyra_pillar2_title','Midnight Noir Palette','2026-11-19 09:28:24','2026-11-19 09:28:24'),(182,'vyra_pillar2_desc','Deep carbon blacks, gunmetal clasps, and electric accents built for nocturnal presence and unfiltered attitude.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(183,'vyra_pillar3_title','Stealth Packaging','2026-11-19 09:28:24','2026-11-19 09:28:24'),(184,'vyra_pillar3_desc','All deliveries arrive in sealed, blacked-out matte boxes with zero exterior brand graphics. Your privacy is non-negotiable.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(185,'buttercup_story_badge','Our Story','2026-11-19 09:28:24','2026-11-19 09:28:24'),(186,'buttercup_story_title','Designed for Real Bodies,\r\nReal Days','2026-11-19 09:28:24','2026-11-19 09:28:24'),(187,'buttercup_about_philosophy_heading','Why Should Comfort and Style Ever Be a Trade-off?','2026-11-19 09:28:24','2026-11-19 09:28:24'),(188,'buttercup_story_p1','Buttercup started with one simple question: why should comfort and style be a trade-off? We design every piece with breathable, skin-friendly fabric — soft enough for everyday, pretty enough to feel special.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(189,'buttercup_story_p2','Zero painful underwires, zero pinch. Just butter-soft modal, organic cotton, and gentle stretch that moves with you from dawn to dusk.','2026-11-19 09:28:24','2026-11-19 09:28:24'),(190,'buttercup_stat1_num','5+','2026-11-19 09:28:24','2026-11-19 09:28:24'),(191,'buttercup_stat1_label','Years of Comfort','2026-11-19 09:28:24','2026-11-19 09:28:24'),(192,'buttercup_stat2_num','2L+','2026-11-19 09:28:24','2026-11-19 09:28:24'),(193,'buttercup_stat2_label','Happy Customers','2026-11-19 09:28:24','2026-11-19 09:28:24'),(194,'buttercup_stat3_num','100%','2026-11-19 09:28:24','2026-11-19 09:28:24'),(195,'buttercup_stat3_label','Skin-Friendly','2026-11-19 09:28:24','2026-11-19 09:28:24'),(196,'buttercup_pillar1_title','Cloud-Soft Modal','2026-11-19 09:28:24','2026-11-19 09:28:24'),(197,'buttercup_pillar1_desc','Micro-modal knit woven from sustainably harvested beechwood, providing a feather-soft second skin feel.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(198,'buttercup_pillar2_title','Wire-Free Ergonomics','2026-11-19 09:28:25','2026-11-19 09:28:25'),(199,'buttercup_pillar2_desc','Sculpted support panels that gently lift without digging metal bones into your ribs. Wearable 24 hours a day.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(200,'buttercup_pillar3_title','Real-Body Inclusive Sizing','2026-11-19 09:28:25','2026-11-19 09:28:25'),(201,'buttercup_pillar3_desc','Engineered across all bust shapes and cup sizes so you never feel squeezed, pinched, or restricted.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(202,'saaya_story_badge','Our Story','2026-11-19 09:28:25','2026-11-19 09:28:25'),(203,'saaya_about_philosophy_badge','Heritage & Mastery','2026-11-19 09:28:25','2026-11-19 09:28:25'),(204,'saaya_story_title','A Love Letter\r\nto Indian Femininity','2026-11-19 09:28:25','2026-11-19 09:28:25'),(205,'saaya_about_philosophy_heading','Where Ancient Zari Craft Meets Modern Contour','2026-11-19 09:28:25','2026-11-19 09:28:25'),(206,'saaya_story_p1','Saaya was born from a belief that every Indian woman deserves lingerie that honours her form and her heritage. We weave together age-old motifs — jaali embroidery, zari threadwork, lotus prints — with modern fits engineered for real bodies.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(207,'saaya_story_p2','Every piece leaves our atelier only after passing through the hands of our master karigars, who carry forward skills passed down through generations.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(208,'saaya_stat1_num','12+','2026-11-19 09:28:25','2026-11-19 09:28:25'),(209,'saaya_stat1_label','Years of Craft','2026-11-19 09:28:25','2026-11-19 09:28:25'),(210,'saaya_stat2_num','80K+','2026-11-19 09:28:25','2026-11-19 09:28:25'),(211,'saaya_stat2_label','Brides Adorned','2026-11-19 09:28:25','2026-11-19 09:28:25'),(212,'saaya_stat3_num','450+','2026-11-19 09:28:25','2026-11-19 09:28:25'),(213,'saaya_stat3_label','Karigars','2026-11-19 09:28:25','2026-11-19 09:28:25'),(214,'saaya_pillar1_title','Pure Mulberry Silk','2026-11-19 09:28:25','2026-11-19 09:28:25'),(215,'saaya_pillar1_desc','Sustainably sourced Grade 6A mulberry silk that breathes against the skin, delivering regal drape and featherlight comfort.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(216,'saaya_pillar2_title','Master Karigar Craft','2026-11-19 09:28:25','2026-11-19 09:28:25'),(217,'saaya_pillar2_desc','Each seam is hand-finished by master craftspeople across Rajasthan and Bengal, preserving centuries-old artisanal zardozi techniques.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(218,'saaya_pillar3_title','Discreet Heirloom Packaging','2026-11-19 09:28:25','2026-11-19 09:28:25'),(219,'saaya_pillar3_desc','Your intimacy is sacred. Every order is encased in unmarked, gold-embossed matte keepsakes with zero external brand indicators.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(220,'lumiere_story_badge','Our Story','2026-11-19 09:28:25','2026-11-19 09:28:25'),(221,'lumiere_about_philosophy_badge','Artisanal Roots','2026-11-19 09:28:25','2026-11-19 09:28:25'),(222,'lumiere_story_title','Everyday elegance,\r\nmade responsibly','2026-11-19 09:28:25','2026-11-19 09:28:25'),(223,'lumiere_about_philosophy_heading','Hand-Finished Pure Mulberry Silk','2026-11-19 09:28:25','2026-11-19 09:28:25'),(224,'lumiere_story_p1','Lumière began with a single loom and a simple belief: the things closest to your skin should be the best you own. Every piece is cut from sustainably sourced silk and finished by artisans who have done this for generations.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(225,'lumiere_story_p2','We work directly with master craftspeople in heritage clusters across India, guaranteeing fair wages and keeping age-old textile traditions alive in contemporary silhouettes.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(226,'lumiere_stat1_num','100%','2026-11-19 09:28:25','2026-11-19 09:28:25'),(227,'lumiere_stat1_label','Pure Silk','2026-11-19 09:28:25','2026-11-19 09:28:25'),(228,'lumiere_stat2_num','300+','2026-11-19 09:28:25','2026-11-19 09:28:25'),(229,'lumiere_stat2_label','Artisans','2026-11-19 09:28:25','2026-11-19 09:28:25'),(230,'lumiere_stat3_num','12','2026-11-19 09:28:25','2026-11-19 09:28:25'),(231,'lumiere_stat3_label','Cities','2026-11-19 09:28:25','2026-11-19 09:28:25'),(232,'lumiere_pillar1_title','Mulberry Silk Quality','2026-11-19 09:28:25','2026-11-19 09:28:25'),(233,'lumiere_pillar1_desc','Featherweight, breathable Grade 6A pure mulberry silk naturally hypoallergenic and temperature-regulating.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(234,'lumiere_pillar2_title','Fair-Wage Handcraft','2026-11-19 09:28:25','2026-11-19 09:28:25'),(235,'lumiere_pillar2_desc','Honouring craftspeople in historic artisan clusters with fair wages, safe ateliers, and sustainable working standards.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(236,'lumiere_pillar3_title','Zero-Plastic Keepsakes','2026-11-19 09:28:25','2026-11-19 09:28:25'),(237,'lumiere_pillar3_desc','Delivered in unmarked, satin-tied reusable presentation boxes. Minimal environmental footprint, absolute discretion.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(238,'organic_about_badge','🌱 Our Farming Manifesto','2026-11-19 09:28:25','2026-11-19 09:28:25'),(239,'organic_story_badge','The Farm-to-Fork Journey','2026-11-19 09:28:25','2026-11-19 09:28:25'),(240,'organic_about_title','Bringing Honest, Pure & Chemical-Free Produce to Indian Homes','2026-11-19 09:28:25','2026-11-19 09:28:25'),(241,'organic_about_subtitle','Nexon Organic was born out of a profound commitment to human wellness and planetary health. We believe food should nourish, heal, and bring us closer to the rhythms of nature.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(242,'organic_story_title','Why We Refuse to Compromise on Soil Health','2026-11-19 09:28:25','2026-11-19 09:28:25'),(243,'organic_story_p1','Modern industrial farming relies on synthetic pesticides, chemical sprays, and artificial ripening agents that strip produce of essential micronutrients while poisoning aquifers.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(244,'organic_story_p2','At Nexon Organic, we cultivate our fields using regenerative multi-cropping, biodiverse cow-based compost (Jeevamrutha), and solar pest traps. Our harvest is packed in breathable eco-crates and dispatched overnight so you receive farm-fresh vitality within 24 hours of cutting.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(245,'organic_stat1_num','100%','2026-11-19 09:28:25','2026-11-19 09:28:25'),(246,'organic_stat1_label','Pesticide & Urea Free','2026-11-19 09:28:25','2026-11-19 09:28:25'),(247,'organic_stat2_num','350+','2026-11-19 09:28:25','2026-11-19 09:28:25'),(248,'organic_stat2_label','Partner Organic Farmers','2026-11-19 09:28:25','2026-11-19 09:28:25'),(249,'organic_pillar1_title','Living Soil First','2026-11-19 09:28:25','2026-11-19 09:28:25'),(250,'organic_pillar1_desc','We nourish the living biome in our soil with cow dung, microbial wash, and mulch instead of petrochemical synthetic urea.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(251,'organic_pillar2_title','24-Hour Farm-to-Doorstep','2026-11-19 09:28:25','2026-11-19 09:28:25'),(252,'organic_pillar2_desc','Harvested in pre-dawn hours, chill-packed without artificial wax or ethylene sprays, and delivered before sunset.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(253,'organic_pillar3_title','Complete QR Traceability','2026-11-19 09:28:25','2026-11-19 09:28:25'),(254,'organic_pillar3_desc','Every vegetable box comes with a farm batch code showing you the soil report, harvest timestamp, and the farmer who tended your crop.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(255,'organic_pillar4_title','Fair Fair-Trade Incomes','2026-11-19 09:28:25','2026-11-19 09:28:25'),(256,'organic_pillar4_desc','We bypass mandis and middlemen, paying our partner farmers guaranteed minimum premiums 35% above conventional market rates.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(257,'organic_story_f1_title','Zero Chemical Residue','2026-11-19 09:28:25','2026-11-19 09:28:25'),(258,'organic_story_f1_desc','Laboratory tested batches with certified zero chemical residue.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(259,'organic_story_f2_title','Eco-Safe Packaging','2026-11-19 09:28:25','2026-11-19 09:28:25'),(260,'organic_story_f2_desc','100% biodegradable cornstarch bags and corrugated kraft crates.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(261,'organic_story_btn_text','Read Our Farming Manifesto','2026-11-19 09:28:25','2026-11-19 09:28:25'),(262,'organic_story_btn_url','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(263,'organic_story_badge2','100% Traceable Harvest','2026-11-19 09:28:25','2026-11-19 09:28:25'),(264,'organic_story_badge2_desc','Scan QR on any crate to see farm origins','2026-11-19 09:28:25','2026-11-19 09:28:25'),(265,'sizeguide_badge','Atelier Fit Perfection','2026-11-19 09:28:25','2026-11-19 09:28:25'),(266,'sizeguide_title','The Precision Size Guide','2026-11-19 09:28:25','2026-11-19 09:28:25'),(267,'sizeguide_subtitle','Over 80% of women wear the incorrect bra size. Use our interactive measurement calculator to find your exact band, cup, and sister size.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(268,'sizeguide_calc_badge','Interactive Calculator','2026-11-19 09:28:25','2026-11-19 09:28:25'),(269,'sizeguide_calc_title','Find Your Bespoke Size','2026-11-19 09:28:25','2026-11-19 09:28:25'),(270,'sizeguide_calc_desc','Grab a flexible measuring tape while standing relaxed in an unpadded, non-pushup bra.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(271,'sizeguide_underbust_hint','Measure snugly directly underneath your breasts.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(272,'sizeguide_bust_hint','Measure loosely across the fullest part of your bust.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(273,'sizeguide_sister_title','Sister Sizing Matrix','2026-11-19 09:28:25','2026-11-19 09:28:25'),(274,'sizeguide_sister_desc','Sister sizes share identical cup volume despite having different band measurements. If your band feels too loose or too snug, navigate horizontally along your sister size group:','2026-11-19 09:28:25','2026-11-19 09:28:25'),(275,'sizeguide_bottoms_title','Panties, Chemises & Robes','2026-11-19 09:28:25','2026-11-19 09:28:25'),(276,'sizeguide_bottoms_desc','For bottoms and sleepwear, measure your waist at the narrowest point and your hips around the fullest curve.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(277,'sizeguide_additional_info','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(278,'shipping_policy_badge','Logistics & Privacy','2026-11-19 09:28:25','2026-11-19 09:28:25'),(279,'shipping_policy_title','Shipping & Discreet Delivery','2026-11-19 09:28:25','2026-11-19 09:28:25'),(280,'shipping_policy_subtitle','Our gold standard delivery experience: fast, trackable, and completely private from our atelier to your doorstep.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(281,'shipping_banner_badge','Our Privacy Promise','2026-11-19 09:28:25','2026-11-19 09:28:25'),(282,'shipping_banner_title','100% Confidential Outer Packaging','2026-11-19 09:28:25','2026-11-19 09:28:25'),(283,'shipping_banner_desc','Every order is dispatched in an unmarked, tamper-evident exterior box or opaque courier flyer. There are zero mentions of lingerie, brassieres, or intimate apparel anywhere on the exterior mailing label. The sender is listed simply as &quot;LS Logistics&quot;.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(284,'shipping_sec1_title','1. Processing & Dispatch Timelines','2026-11-19 09:28:25','2026-11-19 09:28:25'),(285,'shipping_sec1_desc','All in-stock orders are processed within 24 business hours (Monday through Saturday, excluding national holidays). Once dispatched, an SMS and email notification with your live tracking AWB link will be automatically sent.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(286,'shipping_sec2_title','2. Estimated Delivery Transit Times','2026-11-19 09:28:25','2026-11-19 09:28:25'),(287,'shipping_sec2_desc','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(288,'shipping_sec3_title','3. Shipping Rates & Free Delivery','2026-11-19 09:28:25','2026-11-19 09:28:25'),(289,'shipping_sec3_desc','We offer Complimentary Express Shipping on all prepaid orders exceeding ₹1,499 across India. For orders below ₹1,499, a nominal flat express shipping fee of ₹99 is applied at checkout.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(290,'shipping_sec4_title','4. Cash on Delivery (COD)','2026-11-19 09:28:25','2026-11-19 09:28:25'),(291,'shipping_sec4_desc','Cash on Delivery is available for serviceable pincodes with a flat ₹50 handling charge. An OTP or telephonic confirmation may be requested prior to dispatch for high-value COD shipments.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(292,'shipping_sec5_title','5. Courier Partners','2026-11-19 09:28:25','2026-11-19 09:28:25'),(293,'shipping_sec5_desc','We partner exclusively with top-tier courier networks including Bluedart, Delhivery, Shadowfax, and Xpressbees to ensure safe transit and zero tampering.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(294,'shipping_custom_content','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(295,'return_policy_badge','Customer Guarantee','2026-11-19 09:28:25','2026-11-19 09:28:25'),(296,'return_policy_title','Returns & Hygiene Standards','2026-11-19 09:28:25','2026-11-19 09:28:25'),(297,'return_policy_subtitle','Your complete confidence and hygiene are our highest priorities. Review our straightforward intimate return policies below.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(298,'return_banner_title','Intimate Hygiene Regulations','2026-11-19 09:28:25','2026-11-19 09:28:25'),(299,'return_banner_desc','Due to strict Indian health & safety standards for second-skin intimates, items tried directly on the skin without underwear or protective hygiene stickers cannot be restocked or refunded.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(300,'return_sec1_title','1. Eligible Items for 7-Day Return / Exchange','2026-11-19 09:28:25','2026-11-19 09:28:25'),(301,'return_sec1_desc','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(302,'return_sec2_title','2. Final Sale (Non-Returnable) Items','2026-11-19 09:28:25','2026-11-19 09:28:25'),(303,'return_sec2_desc','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(304,'return_sec3_title','3. How to Initiate a Return','2026-11-19 09:28:25','2026-11-19 09:28:25'),(305,'return_sec3_desc','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(306,'return_sec4_title','4. Refund Processing','2026-11-19 09:28:25','2026-11-19 09:28:25'),(307,'return_sec4_desc','Upon receipt and quality inspection at our atelier (usually 2 business days after receipt), refunds are processed directly to your original payment method (Credit card, UPI, Net Banking) within 3 to 5 banking days. COD orders are refunded via instant UPI or NEFT transfer.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(308,'return_custom_content','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(309,'product_delivery_info','Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(310,'product_returns_info','Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(311,'invoice_footer','Thank you for choosing Nex-on!','2026-11-19 09:28:25','2026-11-19 09:28:25'),(312,'order_webhook_urls','','2026-11-19 09:28:25','2026-11-19 09:28:25'),(313,'pwa_prompt_enabled','1','2026-11-19 09:28:25','2026-11-19 09:28:25'),(314,'pwa_prompt_title','Install Nex-on App','2026-11-19 09:28:25','2026-11-19 09:28:25'),(315,'pwa_prompt_description','Get quick access to our collection! Install our app for a faster, better shopping experience on your device.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(316,'organic_delivery_info','Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.','2026-11-19 09:28:25','2026-11-19 09:28:25'),(317,'organic_returns_info','Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.','2026-11-19 09:28:25','2026-11-19 09:28:25');
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
INSERT INTO `stock_transfer_items` VALUES (1,1,1,10,10,'2026-11-18 11:13:58','2026-11-18 11:13:58'),(2,2,2,6,0,'2026-11-18 11:13:58','2026-11-18 11:13:58');
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
INSERT INTO `stock_transfers` VALUES (1,'TRF-20261016-0001',3,1,'received','Pre-festive restock of mulberry silk robes. Tracking: EXP-BHW-COL-9901',8,'2026-11-16 11:13:57',10,'2026-11-17 11:13:57','2026-11-18 11:13:58','2026-11-18 11:13:58'),(2,'TRF-20261016-0002',3,2,'dispatched','New bridal trousseau samples for Bengaluru studio launch. Tracking: BLUEDART-88992211',8,'2026-11-18 05:13:58',NULL,NULL,'2026-11-18 11:13:58','2026-11-18 11:13:58');
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
  `tax_rate` decimal(5,2) DEFAULT '18.00',
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
INSERT INTO `suppliers` VALUES (1,'Varanasi Mulberry Silk Mills','SUP-VAR-01','Pandit Rakesh Mishra','orders@varanasi-silk.in','+91 542 250 8899',7,'09AABCU9603R1ZM','Ghat Road, Chowk, Varanasi, UP 221001',1,'2026-11-18 11:13:58','2026-11-18 11:13:58'),(2,'Surat Premium Lace & Weaves','SUP-SUR-02','Bhavesh Shah','sales@suratlaceweaves.com','+91 261 245 1122',5,'24AABCS4455C1ZL','Ring Road Textile Market, Surat, Gujarat 395002',1,'2026-11-18 11:13:58','2026-11-18 11:13:58');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '#4F46E5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'Bridal','bridal','Royal bridal & ceremonial collection','#E11D48','2026-11-19 09:47:01','2026-11-19 09:47:01'),(2,'Pure Silk','pure-silk','Pure mulberry & handloom silk pieces','#7C3AED','2026-11-19 09:47:01','2026-11-19 09:47:01'),(3,'Handcrafted','handcrafted','Artisanal handwoven & zari embroidered craft','#D97706','2026-11-19 09:47:01','2026-11-19 09:47:01'),(4,'Festive Edit','festive-edit','Special occasion & festive celebrations','#DC2626','2026-11-19 09:47:01','2026-11-19 09:47:01'),(5,'Bestseller','bestseller','Customer favorite top selling heirloom items','#059669','2026-11-19 09:47:01','2026-11-19 09:47:01'),(6,'New Arrival','new-arrival','Freshly released contemporary couture','#2563EB','2026-11-19 09:47:01','2026-11-19 09:47:01'),(7,'Limited Edition','limited-edition','Exclusive archival small-batch pieces','#4338CA','2026-11-19 09:47:01','2026-11-19 09:47:01'),(8,'Organic Fabric','organic-fabric','Sustainably sourced botanical & natural yarns','#16A34A','2026-11-19 09:47:01','2026-11-19 09:47:01');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES (1,'saaya','Saaya',NULL,NULL,1,'2026-11-18 11:13:54','2026-11-19 12:04:49'),(2,'buttercup','Buttercup',NULL,NULL,0,'2026-11-18 11:13:54','2026-11-19 12:04:49'),(3,'vyra','Vyra',NULL,NULL,0,'2026-11-18 11:13:54','2026-11-19 12:04:49'),(4,'lumiere','Lumière',NULL,NULL,0,'2026-11-18 11:13:54','2026-11-19 12:04:49'),(5,'organic','Verdia','Verdia Botanical Living',NULL,0,'2026-11-18 11:13:54','2026-11-19 12:04:49');
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
INSERT INTO `user_roles` VALUES (8,1),(9,2);
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
  `otp_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `role` enum('customer','vendor','admin','staff') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Priya Sharma','priya.sharma@example.com',NULL,'2026-11-18 11:13:55','$2y$12$gSgEVt5SHhIJhEoJUXUPR.B4FFALMAd1tAJ9oK2imdr6z9bp5TmGO',NULL,NULL,NULL,NULL,NULL,'customer',NULL,NULL,'active',0.00,0,'profile.jpg',NULL,NULL,'2026-11-18 11:13:55','2026-11-18 11:13:55'),(2,'Robert Hayes','r.hayes@store.com','+1 555-1001',NULL,'$2y$12$7vSvg4NI//pg8LjeWQp6re3Mgdb9QymDIN5/qvZAHHyZ7Ef64Sxmy',NULL,NULL,NULL,NULL,NULL,'staff','Warehouse Manager','Operations','active',0.00,1,'profile.jpg',NULL,NULL,'2022-01-15 03:30:00','2026-11-18 11:13:55'),(3,'Jessica Lee','j.lee@store.com','+1 555-1002',NULL,'$2y$12$xPfEi0W28tEQ35eQ2k5Sx.8nxyDuuJjSZJKDl8pvmo.jjHCTgpu.m',NULL,NULL,NULL,NULL,NULL,'staff','Store Manager','Management','active',0.00,1,'profile.jpg',NULL,NULL,'2021-03-10 03:30:00','2026-11-18 11:13:56'),(4,'Tyler Brooks','t.brooks@store.com','+1 555-1003',NULL,'$2y$12$fIcxOLTZHIGtuQGfKFhIee9CtqWD4tlAw5wgYI3MGx5UKeZn8Kgzq',NULL,NULL,NULL,NULL,NULL,'staff','Sales Associate','Sales','active',28400.00,1,'profile.jpg',NULL,NULL,'2023-09-01 03:30:00','2026-11-18 11:13:56'),(5,'Nadia Petrov','n.petrov@store.com','+1 555-1004',NULL,'$2y$12$bvtph6h40kl1nCvSsUmV8ugtgklrOtoGmmq5Myfghb5lEJq/ySAhm',NULL,NULL,NULL,NULL,NULL,'staff','Cashier','POS','active',14200.00,1,'profile.jpg',NULL,NULL,'2024-02-12 03:30:00','2026-11-18 11:13:56'),(6,'Kevin Park','k.park@store.com','+1 555-1005',NULL,'$2y$12$BYA/QJLLMqhvCuC610SSGugdmT3uCE.VmHikzc07zttMjzB4pexEC',NULL,NULL,NULL,NULL,NULL,'staff','Inventory Analyst','Operations','active',0.00,1,'profile.jpg',NULL,NULL,'2023-06-20 03:30:00','2026-11-18 11:13:56'),(7,'Alicia Gomez','a.gomez@store.com','+1 555-1006',NULL,'$2y$12$GsMjUB0.MiNBKVp2KtnQ5uw.v7iNXthw3PkhWiTb2x2nRMnCYnkli',NULL,NULL,NULL,NULL,NULL,'staff','Customer Support','Support','on_leave',0.00,1,'profile.jpg',NULL,NULL,'2022-11-05 03:30:00','2026-11-18 11:13:56'),(8,'Super Administrator','admin@nexon.app',NULL,'2026-11-18 11:13:57','$2y$12$jHmWnLk1O3svY3Bk5tadeu6JPWUlS8n2K2oAzuHJp4dMJtPbvVu7G',NULL,NULL,NULL,NULL,NULL,'admin',NULL,NULL,'active',0.00,0,'profile.jpg',NULL,NULL,'2026-11-18 11:13:57','2026-11-19 09:28:04'),(9,'Store Manager','manager@nexon.app',NULL,'2026-11-18 11:13:57','$2y$12$bx1q30DTGOA4TrQ9H6A/Q.twwZq3XctaYIWOz5xMQFCHUQDSOG78i',NULL,NULL,NULL,NULL,NULL,'staff','Store Manager','Store Operations','active',0.00,1,'profile.jpg',NULL,NULL,'2026-11-18 11:13:57','2026-11-18 11:13:57'),(10,'Meera Kapoor','staff@nexon.com',NULL,'2026-11-18 11:13:57','$2y$12$FHfL6Y6ajbzqcCBAnx2cTOBZfpbVPp1hb9BJnYMVTDfaPbecBQ2nS',NULL,NULL,NULL,NULL,NULL,'staff',NULL,NULL,'active',0.00,1,'profile.jpg',NULL,NULL,'2026-11-18 11:13:57','2026-11-18 11:13:57');
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

--
-- Dumping events for database 'nexon_db'
--

--
-- Dumping routines for database 'nexon_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-20 10:25:23
