-- Лаба 6

CREATE DATABASE IF NOT EXISTS `users_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `users_db`;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`       INT          NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50)  NOT NULL,
  `email`    VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT 'password_hash(md5(пароль))',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
