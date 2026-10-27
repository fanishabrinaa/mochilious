-- MochiLicious: struktur database + data awal
-- Cara pakai: phpMyAdmin -> pilih database mochilicious -> tab Import -> pilih file ini
--
-- Akun bawaan (GANTI password-nya kalau situs dipublikasikan):
--   Admin    : username "admin" / password "admin123"
--   Pelanggan: username "demo"  / password "demo123"

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `price` INT UNSIGNED NOT NULL,
  `short_desc` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `badge` VARCHAR(30) NOT NULL DEFAULT '',
  `category` VARCHAR(50) NOT NULL DEFAULT 'Classic',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_product` (`user_id`,`product_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(30) NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `total` INT UNSIGNED NOT NULL,
  `status` ENUM('pending','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_order_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `product_name` VARCHAR(100) NOT NULL,
  `price` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_order` (`order_id`),
  CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`username`,`email`,`password`,`full_name`,`role`,`is_active`) VALUES
('admin','admin@mochilicious.test','$2y$10$8.z6TyJmK2PLxR.nWv.O6eRb5Vy/G93ugp8RLS0B2wC/lezbpYw1a','Admin MochiLicious','admin',1),
('demo','demo@mochilicious.test','$2y$10$QrPjoBwm4ZDtwhJxwWiOmOe3xHYO1RKAZqQybYtoDU778EqqbaBKC','Pengguna Demo','customer',1);

INSERT INTO `products` (`id`,`name`,`price`,`short_desc`,`description`,`image`,`badge`,`category`,`is_active`) VALUES
(1,'Mochi Strawberry',35000,'Mochi dengan isian selai stroberi segar, dilapisi tepung ketan premium.','Mochi dengan isian selai stroberi segar, dilapisi tepung ketan premium. Rasanya manis dan segar dengan tekstur mochi yang lembut.','images/mochi_strawberry.png','Best Seller','Fruit',1),
(2,'Mochi Chocolate',38000,'Mochi dengan coklat premium dan kacang almond, kombinasi sempurna.','Mochi dengan coklat premium dan kacang almond, kombinasi sempurna antara manisnya coklat dan renyahnya kacang.','images/mochi_chocolate.png','','Classic',1),
(3,'Mochi Matcha',42000,'Mochi dengan matcha premium dari Jepang, cita rasa autentik.','Mochi dengan matcha premium dari Jepang, cita rasa autentik dengan aroma teh hijau yang khas dan manis alami.','images/mochi_matcha.png','','Classic',1),
(4,'Mochi Mango',42000,'Mochi dengan mangga manis segar, rasa tropis yang menyegarkan.','Mochi dengan mangga manis segar, rasa tropis yang menyegarkan dengan kombinasi manis dan sedikit asam.','images/mochi_mango.png','','Fruit',1),
(5,'Mochi Red Bean',40000,'Mochi tradisional dengan kacang merah manis, cita rasa klasik.','Mochi tradisional dengan kacang merah manis, cita rasa klasik Jepang yang legit dan tidak terlalu manis.','images/mochi_redbean.png','Promo','Classic',1),
(6,'Mochi Vanilla',36000,'Mochi dengan vanilla bean premium, lembut dan harum.','Mochi dengan vanilla bean premium, lembut dan harum dengan aroma vanilla yang alami dan manis.','images/mochi_vanilla.png','','Classic',1);
