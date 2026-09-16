-- ============================================================
-- Local XAMPP migration: create categories, fix products schema
-- Run once: mysql -u root ever_bio < migrate_local.sql
-- ============================================================

SET NAMES utf8mb4 COLLATE utf8mb4_general_ci;

-- 1. Create categories table
CREATE TABLE IF NOT EXISTS categories (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL COLLATE utf8mb4_general_ci,
  slug        VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  sort_order  INT NOT NULL DEFAULT 0,
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  created_at  DATETIME NOT NULL DEFAULT current_timestamp(),
  updated_at  DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Add missing columns to products (ALTER TABLE is safe to re-run with IF NOT EXISTS)
ALTER TABLE products
  ADD COLUMN IF NOT EXISTS category_id INT UNSIGNED NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS image_path  VARCHAR(255) NOT NULL DEFAULT '';

-- 3. Seed categories from existing product category strings
INSERT IGNORE INTO categories (name, slug, sort_order)
SELECT DISTINCT
  CONVERT(category USING utf8mb4) COLLATE utf8mb4_general_ci,
  LOWER(REPLACE(REPLACE(CONVERT(category USING utf8mb4), ' ', '-'), '/', '-')),
  0
FROM products
WHERE TRIM(category) != '';

-- 4. Link products to their categories
UPDATE products p
JOIN categories c
  ON c.name = CONVERT(p.category USING utf8mb4) COLLATE utf8mb4_general_ci
SET p.category_id = c.id;

SELECT 'Migration complete' AS status;
SELECT id, name, slug FROM categories;
