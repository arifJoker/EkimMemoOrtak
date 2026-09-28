-- TAMBASKI.COM.TR VERİTABANI ŞEMASI
-- Gelişmiş E-Ticaret, Hazır Paket & Özel Adet / m² Fiyatlama Motoru

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `parent_id` INT DEFAULT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(160) NOT NULL UNIQUE,
  `icon` VARCHAR(80) DEFAULT 'bi-box-seam',
  `description` TEXT DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `short_desc` VARCHAR(500) DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `pricing_type` ENUM('package_only', 'package_and_custom', 'sqm_calculator') DEFAULT 'package_and_custom',
  `min_quantity` INT DEFAULT 1,
  `base_setup_fee` DECIMAL(10,2) DEFAULT 0.00,
  `custom_unit_multiplier` DECIMAL(10,4) DEFAULT 1.0000,
  `base_sqm_price` DECIMAL(10,2) DEFAULT 0.00,
  `image` VARCHAR(255) DEFAULT 'assets/img/default_product.webp',
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_urgent_available` TINYINT(1) DEFAULT 1,
  `has_template` TINYINT(1) DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`category_id`),
  INDEX (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_packages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `specs` VARCHAR(500) DEFAULT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `dealer_price` DECIMAL(10,2) DEFAULT NULL,
  `is_popular` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  INDEX (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_options` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('select', 'radio', 'number') DEFAULT 'select',
  `sort_order` INT DEFAULT 0,
  INDEX (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_option_values` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `option_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `price_extra` DECIMAL(10,2) DEFAULT 0.00,
  `multiplier` DECIMAL(10,4) DEFAULT 1.0000,
  `is_default` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  INDEX (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `company_name` VARCHAR(200) DEFAULT NULL,
  `tax_no` VARCHAR(50) DEFAULT NULL,
  `tax_office` VARCHAR(100) DEFAULT NULL,
  `user_type` ENUM('customer', 'dealer', 'admin') DEFAULT 'customer',
  `dealer_discount_rate` DECIMAL(5,2) DEFAULT 25.00,
  `is_approved` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_no` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `invoice_type` ENUM('individual', 'corporate') DEFAULT 'individual',
  `invoice_info` TEXT DEFAULT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `dealer_discount` DECIMAL(10,2) DEFAULT 0.00,
  `shipping_fee` DECIMAL(10,2) DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('credit_card', 'bank_transfer') DEFAULT 'credit_card',
  `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
  `order_status` ENUM('pending', 'approved', 'printing', 'processing', 'packaging', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
  `cargo_company` VARCHAR(100) DEFAULT NULL,
  `tracking_number` VARCHAR(100) DEFAULT NULL,
  `order_notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`user_id`),
  INDEX (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `package_id` INT DEFAULT NULL,
  `item_title` VARCHAR(255) NOT NULL,
  `specifications` TEXT DEFAULT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `design_source` ENUM('upload', 'online_editor', 'graphic_support') DEFAULT 'upload',
  `uploaded_design_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `uploaded_designs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(100) NOT NULL,
  `user_id` INT DEFAULT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL,
  `file_ext` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
