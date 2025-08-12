-- SQL script for creating the necessary tables for the Form Builder application.
--
-- It is recommended to run these commands in your MySQL client.
-- --------------------------------------------------------

-- 1. Create the database (if it doesn't exist)
CREATE DATABASE IF NOT EXISTS `form_builder_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `form_builder_db`;

-- --------------------------------------------------------

-- 2. Create the `forms` table
-- This table stores the overall structure of each form.
CREATE TABLE `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `structure` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `forms_chk_structure` CHECK (json_valid(`structure`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- 3. Create the `submissions` table
-- This table stores the data submitted through each form.
CREATE TABLE `submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `data` longtext NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `form_id_idx` (`form_id`),
  CONSTRAINT `submissions_fk_form_id` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `submissions_chk_data` CHECK (json_valid(`data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- 4. Create a dedicated database user (recommended for security)
-- Replace 'db_user' and 'db_password' with a secure username and password.
-- CREATE USER 'db_user'@'localhost' IDENTIFIED BY 'db_password';
-- GRANT ALL PRIVILEGES ON `form_builder_db`.* TO 'db_user'@'localhost';
-- FLUSH PRIVILEGES;

-- After running this script, make sure to update the credentials in `api/db.php`.
