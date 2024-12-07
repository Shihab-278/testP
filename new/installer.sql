
    CREATE DATABASE IF NOT EXISTS domhoste_test2;
    USE domhoste_test2;

    CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP
    );
    --
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);
    