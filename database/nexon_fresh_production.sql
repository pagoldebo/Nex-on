-- -------------------------------------------------------------
-- Nex-on Production Ready Database Dump (Clean / Zero Dummy Data)
-- Generated: 2026-11-23 17:22:28
-- Framework: Laravel 12 / PHP 8.2+ / MySQL 8.0+
-- -------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- -------------------------------------------------------------
-- Table structure for `abandoned_carts`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `abandoned_carts`;
CREATE TABLE `abandoned_carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_data` json NOT NULL,
  `recovery_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
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

-- -------------------------------------------------------------
-- Table structure for `accounting_sync_logs`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `accounting_sync_logs`;
CREATE TABLE `accounting_sync_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sync_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `voucher_number` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `direction` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'export_xml',
  `payload_xml` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_message` text COLLATE utf8mb4_unicode_ci,
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

-- -------------------------------------------------------------
-- Table structure for `addon_product`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `addon_product`;
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

-- -------------------------------------------------------------
-- Table structure for `addons`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `addons`;
CREATE TABLE `addons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `appraisal_cycles`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `appraisal_cycles`;
CREATE TABLE `appraisal_cycles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `cutoff_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('upcoming','active','evaluation','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `appraisal_cycles`
INSERT INTO `appraisal_cycles` (`id`, `title`, `start_date`, `cutoff_date`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Annual Performance Review FY 2026-27', '2026-01-01', '2026-12-31', 'Comprehensive annual evaluation covering sales targets, client hospitality, and teamwork.', 'active', '2026-11-23 17:22:23', '2026-11-23 17:22:23');

-- -------------------------------------------------------------
-- Table structure for `attribute_groups`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `attribute_groups`;
CREATE TABLE `attribute_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `attributes`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `attributes`;
CREATE TABLE `attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_group_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('text','select','multiselect','color','boolean') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
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

-- -------------------------------------------------------------
-- Table structure for `brands`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `brands`
INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `logo`, `website`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Nex-on Signature', 'nexon-signature', 'Flagship couture & signature collection.', NULL, NULL, 1, 1, '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(2, 'Sabyasachi Atelier', 'sabyasachi-atelier', 'Opulent royal bridal lehengas, sarees, and heirloom ensembles.', NULL, NULL, 1, 2, '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(3, 'Raw Mango', 'raw-mango', 'Contemporary handwoven Chanderi, Banarasi, and Mashru silks.', NULL, NULL, 1, 3, '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(4, 'Bespoke Heritage', 'bespoke-heritage', 'Handcrafted pure tussar, linen, and artisanal handloom fabrics.', NULL, NULL, 1, 4, '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(5, 'Vedic Organics', 'vedic-organics', 'Pure organic produce, botanicals, and farm-fresh natural wellness.', NULL, NULL, 1, 5, '2026-11-23 17:22:26', '2026-11-23 17:22:26');

-- -------------------------------------------------------------
-- Table structure for `cache`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `cache_locks`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `categories`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `collection_product`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `collection_product`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `collections`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `collections`;
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

-- Dumping data for table `collections`
INSERT INTO `collections` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Royal Bridal Trousseau', 'royal-bridal-trousseau', 'Handcrafted pure silk and antique zari embroidery heirlooms tailored for sacred wedding celebrations.', NULL, 1, 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(2, 'Festive Velvet Edit', 'festive-velvet-edit', 'Opulent crimson and emerald velvet sets tailored for celebrations and grand evenings.', NULL, 1, 2, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(3, 'Benarasi Heritage Archive', 'benarasi-heritage-archive', 'Timeless handloom motifs woven with pure gold and silver wire by royal artisan clusters.', NULL, 1, 3, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(4, 'Everyday Luxury Silks', 'everyday-luxury-silks', 'Weightless mulberry silk satin essentials crafted for effortless daily comfort and grace.', NULL, 1, 4, '2026-11-23 17:22:27', '2026-11-23 17:22:27');

-- -------------------------------------------------------------
-- Table structure for `commercial_invoice_items`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `commercial_invoice_items`;
CREATE TABLE `commercial_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commercial_invoice_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hsn_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
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

-- -------------------------------------------------------------
-- Table structure for `commercial_invoices`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `commercial_invoices`;
CREATE TABLE `commercial_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('tax_invoice','credit_note','proforma') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tax_invoice',
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `client_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_gstin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_pan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_address` text COLLATE utf8mb4_unicode_ci,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `place_of_supply` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Maharashtra',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cgst_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sgst_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igst_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','issued','paid','partial','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
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

-- -------------------------------------------------------------
-- Table structure for `coupons`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `custom_pages`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `custom_pages`;
CREATE TABLE `custom_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_blocks` json DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `customer_addresses`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `customer_addresses`;
CREATE TABLE `customer_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_user_id_foreign` (`user_id`),
  CONSTRAINT `customer_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `customer_group_user`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `customer_group_user`;
CREATE TABLE `customer_group_user` (
  `customer_group_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_group_id`,`user_id`),
  KEY `customer_group_user_user_id_foreign` (`user_id`),
  CONSTRAINT `customer_group_user_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `customer_groups`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `customer_groups`;
CREATE TABLE `customer_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_groups_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `delivery_personnel`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `delivery_personnel`;
CREATE TABLE `delivery_personnel` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bike',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_personnel_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `department_user`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `department_user`;
CREATE TABLE `department_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_user_department_id_user_id_unique` (`department_id`,`user_id`),
  KEY `department_user_user_id_foreign` (`user_id`),
  CONSTRAINT `department_user_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `departments`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `head_user_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'indigo',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`),
  KEY `departments_head_user_id_foreign` (`head_user_id`),
  CONSTRAINT `departments_head_user_id_foreign` FOREIGN KEY (`head_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `departments`
INSERT INTO `departments` (`id`, `name`, `code`, `head_user_id`, `description`, `color`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Retail Store Sales', 'SALES', NULL, 'Boutique storefront customer styling, floor service, and POS billing.', 'indigo', 1, '2026-11-23 17:22:23', '2026-11-23 17:22:23'),
(2, 'Warehouse & Fulfillment', 'WH', NULL, 'Inventory picking, packing, dispatch, and inter-store stock transfers.', 'blue', 1, '2026-11-23 17:22:23', '2026-11-23 17:22:23'),
(3, 'Atelier & Bespoke Tailoring', 'ATELIER', NULL, 'Garment alterations, custom silhouettes, and bespoke embroidery.', 'rose', 1, '2026-11-23 17:22:23', '2026-11-23 17:22:23'),
(4, 'Customer Care & Concierge', 'CARE', NULL, 'VIP concierge service, order support tickets, and post-purchase follow-up.', 'emerald', 1, '2026-11-23 17:22:23', '2026-11-23 17:22:23'),
(5, 'Store Management & Admin', 'MGMT', NULL, 'General store administration, cash drawer audits, and staff supervision.', 'purple', 1, '2026-11-23 17:22:23', '2026-11-23 17:22:23');

-- -------------------------------------------------------------
-- Table structure for `employee_feedbacks`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `employee_feedbacks`;
CREATE TABLE `employee_feedbacks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` bigint unsigned NOT NULL,
  `to_user_id` bigint unsigned NOT NULL,
  `type` enum('praise','manager_feedback','peer_review') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'praise',
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_feedbacks_from_user_id_foreign` (`from_user_id`),
  KEY `employee_feedbacks_to_user_id_created_at_index` (`to_user_id`,`created_at`),
  CONSTRAINT `employee_feedbacks_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedbacks_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `employee_goals`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `employee_goals`;
CREATE TABLE `employee_goals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `creator_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metric_type` enum('numeric','revenue','percentage','boolean') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `target_value` decimal(12,2) NOT NULL DEFAULT '100.00',
  `current_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '%',
  `progress_percent` tinyint unsigned NOT NULL DEFAULT '0',
  `status` enum('not_started','on_track','at_risk','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `due_date` date NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General Performance',
  `weightage` tinyint unsigned NOT NULL DEFAULT '20',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_goals_creator_id_foreign` (`creator_id`),
  KEY `employee_goals_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `employee_goals_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_goals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `expense_categories`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `expense_categories`;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366f1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `expense_categories`
INSERT INTO `expense_categories` (`id`, `name`, `slug`, `color`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Rent & Store Leases', 'rent-leases', '#3b82f6', 'Commercial showroom, warehouse, and office rent', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21'),
(2, 'Packaging & Bags', 'packaging-bags', '#10b981', 'Boxes, tissue wrap, shopping bags, and branded tags', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21'),
(3, 'Courier & Freight Outward', 'courier-freight', '#f59e0b', 'BlueDart, Delhivery, and express logistics shipping charges', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21'),
(4, 'Salaries, Wages & Stipends', 'salaries-wages', '#8b5cf6', 'Staff payroll, store cashier wages, and commissions', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21'),
(5, 'Marketing & Paid Ads', 'marketing-ads', '#ec4899', 'Meta ads, Google Ads, influencers, and creative campaigns', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21'),
(6, 'Electricity & Utilities', 'utilities', '#06b6d4', 'Store electricity, water, internet, and phone bills', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21'),
(7, 'Maintenance & Fixtures', 'maintenance-fixtures', '#64748b', 'HVAC repairs, lighting, plumbing, and showroom upkeep', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21'),
(8, 'Software & Cloud SaaS', 'software-saas', '#6366f1', 'Hosting, POS licenses, Shopify/ERP subscriptions', 1, '2026-11-23 17:22:21', '2026-11-23 17:22:21');

-- -------------------------------------------------------------
-- Table structure for `expenses`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expense_category_id` bigint unsigned NOT NULL,
  `outlet_id` bigint unsigned DEFAULT NULL,
  `expense_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `expense_date` date NOT NULL,
  `vendor_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_gstin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurring_frequency` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
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

-- -------------------------------------------------------------
-- Table structure for `failed_jobs`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `faqs`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faqs_category_is_active_sort_order_index` (`category`,`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `faqs`
INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'How long does domestic shipping usually take?', 'Domestic courier dispatches take between 2 to 4 business days for major metro cities (Mumbai, Delhi, Bengaluru, Hyderabad). Custom bespoke alterations require an additional 48 hours in our atelier.', 'Shipping & Delivery', 1, 1, '2026-11-23 17:22:25', '2026-11-23 17:22:25'),
(2, 'What is the return and exchange window?', 'We provide a 7-day complimentary return or size exchange window from the date of physical delivery for all unworn garments with original designer tags intact.', 'Returns & Exchanges', 2, 1, '2026-11-23 17:22:25', '2026-11-23 17:22:25'),
(3, 'Can I request custom silhouette alterations before dispatch?', 'Yes, during checkout or immediately after placing your order, you can contact our concierge with your bust, waist, and length measurements for complimentary basic tailoring.', 'Atelier & Customization', 3, 1, '2026-11-23 17:22:25', '2026-11-23 17:22:25'),
(4, 'Are B2B GST tax invoices available for wholesale or corporate purchases?', 'Yes. Simply enter your company name and GSTIN during the checkout step or contact our finance desk to receive an official GST tax invoice with input credit claims.', 'Payments & Billing', 4, 1, '2026-11-23 17:22:25', '2026-11-23 17:22:25');

-- -------------------------------------------------------------
-- Table structure for `inventory_logs`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `inventory_logs`;
CREATE TABLE `inventory_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_variant_id` bigint unsigned NOT NULL,
  `sku` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_qty` int NOT NULL,
  `new_stock` int NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual_adjustment',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

-- -------------------------------------------------------------
-- Table structure for `job_batches`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `jobs`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `kb_articles`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `kb_articles`;
CREATE TABLE `kb_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

-- -------------------------------------------------------------
-- Table structure for `kb_categories`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `kb_categories`;
CREATE TABLE `kb_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 0xF09F939A,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kb_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `kb_categories`
INSERT INTO `kb_categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Shipping & Delivery', 'shipping-delivery', 'Delivery timelines, domestic courier tracking, and white-glove doorstep delivery.', '📦', 1, 1, '2026-11-23 17:22:24', '2026-11-23 17:22:24'),
(2, 'Returns & Exchanges', 'returns-exchanges', '7-day hassle-free returns policy, exchange process, and refund settlement schedules.', '🔄', 2, 1, '2026-11-23 17:22:24', '2026-11-23 17:22:24'),
(3, 'Bespoke Sizing & Fit', 'sizing-fit', 'How to take measurements, international sizing conversions, and custom alterations.', '📏', 3, 1, '2026-11-23 17:22:24', '2026-11-23 17:22:24'),
(4, 'Garment Care & Fabric Maintenance', 'garment-care', 'Dry cleaning recommendations, silk and velvet preservation, and embroidery care.', '✨', 4, 1, '2026-11-23 17:22:24', '2026-11-23 17:22:24'),
(5, 'Payments & Security', 'payments-security', 'Credit card safety, UPI, EMI installment options, and B2B GST tax invoices.', '💳', 5, 1, '2026-11-23 17:22:24', '2026-11-23 17:22:24');

-- -------------------------------------------------------------
-- Table structure for `loyalty_accounts`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `loyalty_accounts`;
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

-- -------------------------------------------------------------
-- Table structure for `loyalty_tiers`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `loyalty_tiers`;
CREATE TABLE `loyalty_tiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `points_multiplier` decimal(3,2) NOT NULL DEFAULT '1.00',
  `perks` text COLLATE utf8mb4_unicode_ci,
  `badge_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#94a3b8',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loyalty_tiers_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `loyalty_tiers`
INSERT INTO `loyalty_tiers` (`id`, `name`, `slug`, `min_spend`, `points_multiplier`, `perks`, `badge_color`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Silver Member', 'silver', 0.00, 1.00, '1 Point per ₹50 spent. Standard checkout redemption. Seasonal member newsletters.', '#94a3b8', 1, '2026-11-23 17:22:22', '2026-11-23 17:22:22'),
(2, 'Gold VIP', 'gold', 15000.00, 1.25, '1.25x point multiplier. Free express courier delivery on all orders. ₹500 Birthday reward coupon.', '#f59e0b', 1, '2026-11-23 17:22:22', '2026-11-23 17:22:22'),
(3, 'Platinum Elite', 'platinum', 40000.00, 1.50, '1.50x point multiplier. Priority same-day atelier dispatch. Private WhatsApp concierge styling.', '#8b5cf6', 1, '2026-11-23 17:22:22', '2026-11-23 17:22:22'),
(4, 'Diamond Patron', 'diamond', 100000.00, 2.00, '2.0x points multiplier. Complimentary satin packaging with every shipment. Exclusive runway previews.', '#06b6d4', 1, '2026-11-23 17:22:22', '2026-11-23 17:22:22');

-- -------------------------------------------------------------
-- Table structure for `loyalty_transactions`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `loyalty_transactions`;
CREATE TABLE `loyalty_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `loyalty_account_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `type` enum('earned','redeemed','adjusted','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'earned',
  `points` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loyalty_transactions_order_id_foreign` (`order_id`),
  KEY `loyalty_transactions_loyalty_account_id_created_at_index` (`loyalty_account_id`,`created_at`),
  CONSTRAINT `loyalty_transactions_loyalty_account_id_foreign` FOREIGN KEY (`loyalty_account_id`) REFERENCES `loyalty_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loyalty_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `migrations`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `migrations`
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000000_create_categories_table', 1),
(5, '2026_01_01_000001_create_products_table', 1),
(6, '2026_01_01_000002_create_product_colors_table', 1),
(7, '2026_01_01_000003_create_product_variants_table', 1),
(8, '2026_01_01_000004_create_templates_table', 1),
(9, '2026_01_01_000005_create_orders_table', 1),
(10, '2026_01_01_000006_create_order_items_table', 1),
(11, '2026_01_01_000007_create_settings_table', 1),
(12, '2026_01_01_000008_create_coupons_table', 1),
(13, '2026_01_01_000009_add_coupon_code_to_orders_table', 1),
(14, '2026_01_01_000010_create_customer_addresses_table', 1),
(15, '2026_01_01_000011_create_order_requests_table', 1),
(16, '2026_01_01_000012_create_pincode_zones_table', 1),
(17, '2026_01_01_000013_create_product_reviews_table', 1),
(18, '2026_01_01_000014_create_subscribers_table', 1),
(19, '2026_01_01_000015_add_sku_to_products_table', 1),
(20, '2026_01_01_000016_create_wishlists_table', 1),
(21, '2026_01_01_000017_create_addons_table', 1),
(22, '2026_01_01_000018_create_inventory_logs_table', 1),
(23, '2026_01_01_000019_add_sku_to_order_items_table', 1),
(24, '2026_01_01_000020_add_deactivated_at_to_users_table', 1),
(25, '2026_01_01_000021_add_pos_fields_to_orders_table', 1),
(26, '2026_01_01_000022_create_suppliers_and_purchase_orders_tables', 1),
(27, '2026_01_01_000023_create_accounting_sync_logs_table', 1),
(28, '2026_01_01_000024_create_sms_logs_and_otp_tables', 1),
(29, '2026_01_01_000025_create_product_batches_and_allocations_tables', 1),
(30, '2026_01_01_000026_create_roles_and_permissions_tables', 1),
(31, '2026_01_01_000027_create_push_subscriptions_and_campaigns_tables', 1),
(32, '2026_01_01_000028_create_outlets_and_stock_transfers_tables', 1),
(33, '2026_01_01_000029_create_custom_pages_table', 1),
(34, '2026_01_01_000030_create_delivery_personnel_and_order_dispatch_tables', 1),
(35, '2026_01_01_000031_add_long_description_to_products_table', 1),
(36, '2026_01_01_000032_add_parent_id_to_categories_table', 1),
(37, '2026_01_01_000033_add_image_to_categories_table', 1),
(38, '2026_01_01_000034_create_attributes_and_product_values_tables', 1),
(39, '2026_01_01_000035_create_customer_groups_and_user_pivot_tables', 1),
(40, '2026_01_01_000036_create_supplier_invoices_and_payments_tables', 1),
(41, '2026_01_01_000037_create_purchase_returns_and_items_tables', 1),
(42, '2026_01_01_000038_create_pos_shifts_and_movements_tables', 1),
(43, '2026_01_01_000039_create_expenses_and_expense_categories_tables', 1),
(44, '2026_01_01_000040_create_commercial_invoices_and_items_tables', 1),
(45, '2026_01_01_000041_create_abandoned_carts_table', 1),
(46, '2026_01_01_000042_create_loyalty_program_tables', 1),
(47, '2026_01_01_000043_create_payroll_tables', 1),
(48, '2026_01_01_000044_create_performance_management_tables', 1),
(49, '2026_01_01_000045_create_departments_and_shifts_tables', 1),
(50, '2026_01_01_000046_create_staff_attendances_table', 1),
(51, '2026_01_01_000047_create_support_tickets_tables', 1),
(52, '2026_01_01_000048_create_knowledge_base_tables', 1),
(53, '2026_01_01_000049_create_faqs_table', 1),
(54, '2026_01_01_000050_add_staff_fields_to_users_table', 1),
(55, '2026_01_01_000051_add_city_to_product_reviews_table', 1),
(56, '2026_01_01_000052_add_otp_fields_to_users_table', 1),
(57, '2026_01_01_000053_create_brands_and_addon_product_tables', 1),
(58, '2026_01_01_000054_add_two_factor_columns_to_users_table', 1),
(59, '2026_11_08_110155_add_cover_image_to_product_colors_table', 1),
(60, '2026_11_14_000001_add_staff_to_users_role_enum', 1),
(61, '2026_11_14_000002_add_tax_amount_to_orders_table', 1),
(62, '2026_11_14_000003_expand_phone_otps_otp_column', 1),
(63, '2026_11_16_000001_expand_users_otp_code_column', 1),
(64, '2026_11_19_000001_create_tags_and_product_tag_tables', 1),
(65, '2026_11_19_000002_create_collections_and_collection_product_tables', 1),
(66, '2026_11_19_170000_add_tax_rate_to_supplier_invoices_table', 1),
(67, '2026_11_21_000001_relativize_internal_urls_in_settings', 1),
(68, '2026_11_23_000001_add_currency_and_exchange_rate_to_orders_table', 1);

-- -------------------------------------------------------------
-- Table structure for `order_item_batches`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `order_item_batches`;
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

-- -------------------------------------------------------------
-- Table structure for `order_items`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_color_id` bigint unsigned DEFAULT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `sku` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `order_requests`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `order_requests`;
CREATE TABLE `order_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_requests_order_id_foreign` (`order_id`),
  KEY `order_requests_user_id_foreign` (`user_id`),
  CONSTRAINT `order_requests_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `orders`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `pos_cashier_id` bigint unsigned DEFAULT NULL,
  `reference` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` int unsigned NOT NULL,
  `shipping_charge` int unsigned NOT NULL DEFAULT '0',
  `cod_charge` int unsigned NOT NULL DEFAULT '0',
  `discount` int unsigned NOT NULL DEFAULT '0',
  `coupon_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` int unsigned NOT NULL DEFAULT '0',
  `total` int unsigned NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `tendered_amount` int unsigned DEFAULT NULL,
  `change_amount` int unsigned DEFAULT NULL,
  `payment_method` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_pincode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_personnel_id` bigint unsigned DEFAULT NULL,
  `delivery_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unassigned',
  `proof_signature` text COLLATE utf8mb4_unicode_ci,
  `proof_notes` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `outlet_stocks`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `outlet_stocks`;
CREATE TABLE `outlet_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `shelf_bin_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outlet_stocks_outlet_id_product_variant_id_unique` (`outlet_id`,`product_variant_id`),
  KEY `outlet_stocks_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `outlet_stocks_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `outlet_stocks_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `outlets`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `outlets`;
CREATE TABLE `outlets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'store',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_central` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outlets_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `password_reset_tokens`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `payroll_items`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `payroll_items`;
CREATE TABLE `payroll_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `payslip_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Retail Associate',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Retail Store Sales',
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'HDFC Bank',
  `bank_account_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_ifsc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'HDFC0000123',
  `pan_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uan_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `status` enum('draft','approved','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `payment_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_items_payslip_number_unique` (`payslip_number`),
  KEY `payroll_items_user_id_foreign` (`user_id`),
  KEY `payroll_items_payroll_id_user_id_index` (`payroll_id`,`user_id`),
  CONSTRAINT `payroll_items_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `payrolls`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `payrolls`;
CREATE TABLE `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_month` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_gross` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_allowances` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_net` decimal(12,2) NOT NULL DEFAULT '0.00',
  `employees_count` int unsigned NOT NULL DEFAULT '0',
  `status` enum('draft','reviewed','approved','disbursed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
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

-- -------------------------------------------------------------
-- Table structure for `permissions`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`),
  KEY `permissions_group_name_index` (`group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `permissions`
INSERT INTO `permissions` (`id`, `name`, `slug`, `group_name`, `created_at`, `updated_at`) VALUES
(1, 'View Administrative Dashboard & Metrics', 'dashboard.view', 'dashboard', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(2, 'View Inventory & Stock', 'inventory.view', 'inventory', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(3, 'Manage Inventory & Adjust Stock', 'inventory.manage', 'inventory', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(4, 'View Demand Forecasting', 'inventory.forecasting', 'inventory', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(5, 'Manage Batches & FIFO Expiry', 'inventory.batches', 'inventory', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(6, 'Access POS Cashier Terminal', 'pos.access', 'pos', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(7, 'Execute POS Orders & Tender Cash', 'pos.checkout', 'pos', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(8, 'View Orders List & Details', 'orders.view', 'orders', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(9, 'Update Order Status & Tracking', 'orders.manage', 'orders', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(10, 'Export Orders CSV', 'orders.export', 'orders', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(11, 'View Products Catalog', 'products.view', 'products', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(12, 'Create & Edit Products & SKUs', 'products.manage', 'products', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(13, 'View Financial & Tax Reports', 'reports.view', 'reports', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(14, 'Export Reports CSV & PDF', 'reports.export', 'reports', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(15, 'Configure General Store Settings', 'settings.manage', 'settings', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(16, 'Manage Payment Gateways', 'settings.gateways', 'settings', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(17, 'Manage SMS & OTP Gateways', 'settings.sms', 'settings', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(18, 'Manage Staff Roles & Permissions', 'roles.manage', 'roles', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(19, 'View Customers Directory & Profiles', 'customers.view', 'customers', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(20, 'Manage Customers & Segments', 'customers.manage', 'customers', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(21, 'Manage Staff Payroll & Payslips', 'payroll.manage', 'payroll', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(22, 'Export Payroll Bank Files', 'payroll.export', 'payroll', '2026-11-23 17:22:27', '2026-11-23 17:22:27');

-- -------------------------------------------------------------
-- Table structure for `phone_otps`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phone_otps`;
CREATE TABLE `phone_otps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `phone_otps_phone_index` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `pincode_zones`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `pincode_zones`;
CREATE TABLE `pincode_zones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pincode` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod_available` tinyint(1) NOT NULL DEFAULT '1',
  `delivery_days` smallint unsigned NOT NULL DEFAULT '3',
  `is_serviceable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pincode_zones_pincode_unique` (`pincode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `pos_shift_movements`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `pos_shift_movements`;
CREATE TABLE `pos_shift_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pos_shift_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('cash_in','cash_out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_shift_movements_pos_shift_id_foreign` (`pos_shift_id`),
  KEY `pos_shift_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `pos_shift_movements_pos_shift_id_foreign` FOREIGN KEY (`pos_shift_id`) REFERENCES `pos_shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pos_shift_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `pos_shifts`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `pos_shifts`;
CREATE TABLE `pos_shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shift_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cashier_id` bigint unsigned NOT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `opening_float` decimal(10,2) NOT NULL DEFAULT '0.00',
  `expected_cash` decimal(10,2) NOT NULL DEFAULT '0.00',
  `counted_cash` decimal(10,2) DEFAULT NULL,
  `cash_difference` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cash_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `card_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `upi_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_sales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_orders_count` int unsigned NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_shifts_shift_code_unique` (`shift_code`),
  KEY `pos_shifts_cashier_id_foreign` (`cashier_id`),
  KEY `pos_shifts_status_index` (`status`),
  CONSTRAINT `pos_shifts_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_attribute_values`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_attribute_values`;
CREATE TABLE `product_attribute_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `attribute_id` bigint unsigned NOT NULL,
  `value_text` text COLLATE utf8mb4_unicode_ci,
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

-- -------------------------------------------------------------
-- Table structure for `product_batches`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_batches`;
CREATE TABLE `product_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_variant_id` bigint unsigned NOT NULL,
  `batch_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lot_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mfg_date` date DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `initial_qty` int unsigned NOT NULL,
  `remaining_qty` int unsigned NOT NULL DEFAULT '0',
  `cost_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_color_images`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_color_images`;
CREATE TABLE `product_color_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_color_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_color_images_product_color_id_foreign` (`product_color_id`),
  CONSTRAINT `product_color_images_product_color_id_foreign` FOREIGN KEY (`product_color_id`) REFERENCES `product_colors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_colors`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_colors`;
CREATE TABLE `product_colors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hex` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_colors_product_id_name_unique` (`product_id`,`name`),
  CONSTRAINT `product_colors_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_reviews`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE `product_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_foreign` (`product_id`),
  KEY `product_reviews_user_id_foreign` (`user_id`),
  CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_tag`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_tag`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_variants`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `product_color_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `products`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `long_description` longtext COLLATE utf8mb4_unicode_ci,
  `badge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `purchase_order_items`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `purchase_order_items`;
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

-- -------------------------------------------------------------
-- Table structure for `purchase_orders`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_amount` int unsigned NOT NULL DEFAULT '0',
  `expected_delivery_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
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

-- -------------------------------------------------------------
-- Table structure for `purchase_return_items`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `purchase_return_items`;
CREATE TABLE `purchase_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned NOT NULL,
  `product_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  KEY `purchase_return_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `purchase_return_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `purchase_returns`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `purchase_returns`;
CREATE TABLE `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `debit_note_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `return_date` date NOT NULL,
  `reason` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','dispatched','credited','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
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

-- -------------------------------------------------------------
-- Table structure for `push_campaigns`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `push_campaigns`;
CREATE TABLE `push_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_count` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `push_subscriptions`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `push_subscriptions`;
CREATE TABLE `push_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `endpoint` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `endpoint_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `p256dh_key` text COLLATE utf8mb4_unicode_ci,
  `auth_token` text COLLATE utf8mb4_unicode_ci,
  `device_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'browser',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_hash_unique` (`endpoint_hash`),
  KEY `push_subscriptions_user_id_foreign` (`user_id`),
  KEY `push_subscriptions_is_active_index` (`is_active`),
  CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `role_permissions`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `role_permissions`
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(1, 2),
(2, 2),
(3, 2),
(5, 2),
(1, 3),
(2, 3),
(5, 3),
(1, 4),
(2, 4),
(5, 4),
(1, 5),
(2, 5),
(5, 5),
(1, 6),
(2, 6),
(4, 6),
(1, 7),
(2, 7),
(4, 7),
(1, 8),
(2, 8),
(3, 8),
(6, 8),
(1, 9),
(2, 9),
(3, 9),
(6, 9),
(1, 10),
(2, 10),
(3, 10),
(1, 11),
(2, 11),
(3, 11),
(4, 11),
(5, 11),
(6, 11),
(1, 12),
(2, 12),
(3, 12),
(1, 13),
(2, 13),
(3, 13),
(1, 14),
(2, 14),
(3, 14),
(1, 15),
(3, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(2, 19),
(3, 19),
(6, 19),
(1, 20),
(2, 20),
(1, 21),
(2, 21),
(1, 22);

-- -------------------------------------------------------------
-- Table structure for `roles`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `roles`
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Full unrestricted system access to all store operations, finances, and settings.', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(2, 'Store Manager', 'store-manager', 'Comprehensive management of store items, catalog, inventory, orders, sales billing, POS checkout, and reports.', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(3, 'eCommerce Manager', 'ecommerce-manager', 'Oversees digital storefront operations, online catalog merchandising, web orders fulfillment, and eCommerce analytics.', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(4, 'POS Cashier', 'pos-cashier', 'In-store retail counter operator with access restricted to the POS Terminal.', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(5, 'Inventory Clerk', 'inventory-clerk', 'Warehouse specialist handling receiving, batch tracking, stock counts, and replenishment.', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(6, 'Customer Support', 'customer-support', 'Client assistance representative reviewing orders, shipments, and customer returns.', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27');

-- -------------------------------------------------------------
-- Table structure for `sessions`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
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

-- -------------------------------------------------------------
-- Table structure for `settings`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'store_name', 'Nex-on', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(2, 'support_email', 'support@nexon.app', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(3, 'support_phone', '+91 98765 43210', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(4, 'store_address', '100 Metro Avenue, Suite 400, Mumbai, Maharashtra 400001', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(5, 'multi_currency_enabled', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(6, 'base_currency', 'INR', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(7, 'enabled_currencies', '[\"INR\",\"USD\",\"EUR\",\"GBP\",\"AED\",\"CAD\",\"AUD\",\"SGD\",\"SAR\",\"JPY\"]', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(8, 'currency_rates', '{\"INR\":1,\"USD\":0.0116,\"EUR\":0.0108,\"GBP\":0.0092,\"AED\":0.0426,\"CAD\":0.016,\"AUD\":0.0178,\"SGD\":0.0156,\"SAR\":0.0435,\"JPY\":1.78}', '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(9, 'free_shipping_threshold', 1499, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(10, 'shipping_cost', 99, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(11, 'enable_cod', 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(12, 'cod_charge', 40, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(13, 'tax_enabled', 0, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(14, 'announcement_bar', 'Free delivery on orders over ₹1,499 | Use code WELCOME10 for 10% off', '2026-11-23 17:22:28', '2026-11-23 17:22:28'),
(15, 'ticker_enabled', 1, '2026-11-23 17:22:28', '2026-11-23 17:22:28');

-- -------------------------------------------------------------
-- Table structure for `sms_logs`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `sms_logs`;
CREATE TABLE `sms_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'log',
  `template_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `order_id` bigint unsigned DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `gateway_response_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_logs_order_id_foreign` (`order_id`),
  KEY `sms_logs_phone_index` (`phone`),
  CONSTRAINT `sms_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `staff_appraisals`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `staff_appraisals`;
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
  `strengths` text COLLATE utf8mb4_unicode_ci,
  `areas_for_improvement` text COLLATE utf8mb4_unicode_ci,
  `recommendation` enum('salary_hike','promotion','bonus','training_pip','retain') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'salary_hike',
  `hike_percentage` decimal(4,1) NOT NULL DEFAULT '0.0',
  `status` enum('draft','submitted','reviewed','finalized') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
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

-- -------------------------------------------------------------
-- Table structure for `staff_attendances`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `staff_attendances`;
CREATE TABLE `staff_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `shift_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'POS Terminal 1',
  `location_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT 'Boutique Flagship Store',
  `total_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('present','late','half_day','absent','on_leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `verification_status` enum('verified','flagged_remote','manual_adjustment') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'verified',
  `manager_note` text COLLATE utf8mb4_unicode_ci,
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

-- -------------------------------------------------------------
-- Table structure for `staff_shifts`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `staff_shifts`;
CREATE TABLE `staff_shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `shift_date` date NOT NULL,
  `shift_type` enum('morning','evening','night','full_day') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'morning',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('scheduled','completed','swapped','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

-- -------------------------------------------------------------
-- Table structure for `stock_transfer_items`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `stock_transfer_items`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `stock_transfers`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_outlet_id` bigint unsigned NOT NULL,
  `to_outlet_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `subscribers`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `supplier_invoice_payments`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `supplier_invoice_payments`;
CREATE TABLE `supplier_invoice_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_invoice_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_invoice_payments_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  CONSTRAINT `supplier_invoice_payments_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `supplier_invoices`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `supplier_invoices`;
CREATE TABLE `supplier_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) DEFAULT '18.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('unpaid','partial','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `matching_status` enum('matched','variance_detected','under_review') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'matched',
  `po_variance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_invoices_supplier_id_invoice_number_unique` (`supplier_id`,`invoice_number`),
  KEY `supplier_invoices_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `supplier_invoices_payment_status_index` (`payment_status`),
  CONSTRAINT `supplier_invoices_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `suppliers`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_time_days` int unsigned NOT NULL DEFAULT '7',
  `gst_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `support_ticket_messages`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `support_ticket_messages`;
CREATE TABLE `support_ticket_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal_note` tinyint(1) NOT NULL DEFAULT '0',
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_messages_user_id_foreign` (`user_id`),
  KEY `support_ticket_messages_ticket_id_created_at_index` (`ticket_id`,`created_at`),
  CONSTRAINT `support_ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `support_tickets`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('order_inquiry','refund_return','delivery_delay','product_defect','billing','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'order_inquiry',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','waiting_customer','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
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

-- -------------------------------------------------------------
-- Table structure for `tags`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `tags`;
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

-- Dumping data for table `tags`
INSERT INTO `tags` (`id`, `name`, `slug`, `description`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Bridal', 'bridal', 'Royal bridal & ceremonial collection', '#E11D48', '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(2, 'Pure Silk', 'pure-silk', 'Pure mulberry & handloom silk pieces', '#7C3AED', '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(3, 'Handcrafted', 'handcrafted', 'Artisanal handwoven & zari embroidered craft', '#D97706', '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(4, 'Festive Edit', 'festive-edit', 'Special occasion & festive celebrations', '#DC2626', '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(5, 'Bestseller', 'bestseller', 'Customer favorite top selling heirloom items', '#059669', '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(6, 'New Arrival', 'new-arrival', 'Freshly released contemporary couture', '#2563EB', '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(7, 'Limited Edition', 'limited-edition', 'Exclusive archival small-batch pieces', '#4338CA', '2026-11-23 17:22:26', '2026-11-23 17:22:26'),
(8, 'Organic Fabric', 'organic-fabric', 'Sustainably sourced botanical & natural yarns', '#16A34A', '2026-11-23 17:22:26', '2026-11-23 17:22:26');

-- -------------------------------------------------------------
-- Table structure for `templates`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `templates`;
CREATE TABLE `templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `templates_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `templates`
INSERT INTO `templates` (`id`, `key`, `name`, `label`, `preview_image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'saaya', 'Saaya', 'Heritage Silk & Bridal Couture', NULL, 1, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(2, 'buttercup', 'Buttercup', 'Pastel Intimates & Sleepwear', NULL, 0, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(3, 'vyra', 'Vyra', 'Modern Noir & Streetwear', NULL, 0, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(4, 'lumiere', 'Lumière', 'Atelier Silk & Minimalist', NULL, 0, '2026-11-23 17:22:27', '2026-11-23 17:22:27'),
(5, 'organic', 'Verdia', 'Verdia Botanical Living', NULL, 0, '2026-11-23 17:22:27', '2026-11-23 17:22:27');

-- -------------------------------------------------------------
-- Table structure for `user_roles`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `user_roles`
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 1);

-- -------------------------------------------------------------
-- Table structure for `users`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `otp_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `role` enum('customer','vendor','admin','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `designation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `staff_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_staff` tinyint(1) NOT NULL DEFAULT '0',
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'profile.jpg',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deactivated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_employment_status_index` (`employment_status`),
  KEY `users_is_staff_index` (`is_staff`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `otp_code`, `otp_expires_at`, `role`, `designation`, `department_name`, `employment_status`, `staff_sales`, `is_staff`, `profile_picture`, `remember_token`, `deactivated_at`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@nexon.app', NULL, '2026-11-23 17:22:27', '$2y$12$n.ACXcT7j1wlBb0PxrIML.vQuo4EIIrtYn6k1uZdQz2LBP9hYtO.O', NULL, NULL, NULL, NULL, NULL, 'admin', NULL, NULL, 'active', 0.00, 0, 'profile.jpg', NULL, NULL, '2026-11-23 17:22:27', '2026-11-23 17:22:27');

-- -------------------------------------------------------------
-- Table structure for `wishlists`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `wishlists`;
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

SET FOREIGN_KEY_CHECKS = 1;
