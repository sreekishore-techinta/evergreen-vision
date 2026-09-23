-- ============================================================
-- EVERGREENINDUSTRY — Database Schema
-- Import via phpMyAdmin or: mysql -u root -p < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `evergreen_db`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `evergreen_db`;

-- ─── Admin Users ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `username`        VARCHAR(60)      NOT NULL UNIQUE,
  `email`           VARCHAR(120)     NOT NULL UNIQUE,
  `password_hash`   VARCHAR(255)     NOT NULL,
  `full_name`       VARCHAR(120)     NOT NULL DEFAULT '',
  `avatar`          VARCHAR(255)     NOT NULL DEFAULT '',
  `role`            ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'admin',
  `last_login`      DATETIME                  DEFAULT NULL,
  `is_active`       TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: username=admin  password=Admin@1234
-- (bcrypt hash generated with PASSWORD_BCRYPT cost 12)
INSERT INTO `admin_users`
  (`username`, `email`, `password_hash`, `full_name`, `role`)
VALUES (
  'admin',
  'admin@evergreenindustry.com',
  '$2y$12$uYWGjTkToCcdu9KC2TZBIutG7gjaqXqLsLjh18g1kvhRIlzoHy7jK',
  'Super Admin',
  'superadmin'
);

-- ─── Contact Enquiries ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_enquiries` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120)     NOT NULL,
  `company`     VARCHAR(120)     NOT NULL DEFAULT '',
  `email`       VARCHAR(120)     NOT NULL,
  `phone`       VARCHAR(30)      NOT NULL DEFAULT '',
  `message`     TEXT             NOT NULL,
  `status`      ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `ip_address`  VARCHAR(45)      NOT NULL DEFAULT '',
  `user_agent`  VARCHAR(255)     NOT NULL DEFAULT '',
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status`     (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Products ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
  `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(200)     NOT NULL,
  `slug`          VARCHAR(200)     NOT NULL UNIQUE,
  `category`      VARCHAR(100)     NOT NULL DEFAULT '',
  `description`   TEXT,
  `features`      TEXT,
  `applications`  TEXT,
  `image_url`     VARCHAR(255)     NOT NULL DEFAULT '',
  `certifications` VARCHAR(255)    NOT NULL DEFAULT '',
  `is_featured`   TINYINT(1)       NOT NULL DEFAULT 0,
  `is_active`     TINYINT(1)       NOT NULL DEFAULT 1,
  `sort_order`    INT              NOT NULL DEFAULT 0,
  `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_category`   (`category`),
  INDEX `idx_is_active`  (`is_active`),
  INDEX `idx_is_featured`(`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample products
INSERT INTO `products` (`name`,`slug`,`category`,`description`,`is_featured`,`is_active`,`sort_order`) VALUES
('Compostable Carry Bags','compostable-carry-bags','Carry Bags','Certified compostable carry bags made from plant-based polymers. EN 13432 & ASTM D6400 certified.',1,1,1),
('Compostable Waste Bags','compostable-waste-bags','Waste Bags','Heavy-duty compostable waste bags for homes, hotels, and municipalities.',1,1,2),
('Breathable Produce Pouches','breathable-produce-pouches','Produce Packaging','Micro-perforated breathable pouches that extend fresh produce shelf life.',1,1,3),
('Biopolymer Granules','biopolymer-granules','Raw Materials','PBAT & PLA blended granules for manufacturers transitioning to compostable packaging.',0,1,4),
('Eco Lifestyle Bags','eco-lifestyle-bag','Lifestyle','Premium reusable lifestyle bags with compostable inner lining.',0,1,5);

-- ─── Site Settings ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100)  NOT NULL UNIQUE,
  `setting_val` TEXT,
  `label`       VARCHAR(150)  NOT NULL DEFAULT '',
  `group_name`  VARCHAR(60)   NOT NULL DEFAULT 'general',
  `sort_order`  INT           NOT NULL DEFAULT 0,
  `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`setting_key`,`setting_val`,`label`,`group_name`,`sort_order`) VALUES
('company_name',      'EVERGREENINDUSTRY',                  'Company Name',        'general',  1),
('tagline',           'Sustainable Packaging Solutions',     'Tagline',             'general',  2),
('contact_email',     'info@evergreenindustry.com',          'Contact Email',       'contact',  1),
('contact_phone',     '+91 73392 85437',                     'Contact Phone',       'contact',  2),
('contact_address',   'No. 2, Tholilpettai, SIDCO Industrial Estate, N.K. Road, Thanjavur (613006), Tamil Nadu', 'Address', 'contact', 3),
('whatsapp_number',   '+91 73392 85437',                     'WhatsApp Number',     'contact',  4),
('facebook_url',      '',                                    'Facebook URL',        'social',   1),
('instagram_url',     '',                                    'Instagram URL',       'social',   2),
('linkedin_url',      '',                                    'LinkedIn URL',        'social',   3),
('twitter_url',       '',                                    'Twitter/X URL',       'social',   4),
('meta_title',        'EVERGREENINDUSTRY | Sustainable Packaging', 'Meta Title',   'seo',      1),
('meta_description',  'Biodegradable and compostable packaging solutions designed for responsible businesses.', 'Meta Description', 'seo', 2),
('google_analytics',  '',                                    'Google Analytics ID', 'seo',      3),
('enquiry_notify_email','admin@evergreenindustry.com',       'Notify Email (Enquiries)', 'notifications', 1);

-- ─── Admin Activity Log ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `admin_id`    INT UNSIGNED  NOT NULL,
  `action`      VARCHAR(100)  NOT NULL,
  `description` TEXT,
  `ip_address`  VARCHAR(45)   NOT NULL DEFAULT '',
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_admin_id`  (`admin_id`),
  INDEX `idx_created_at`(`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
