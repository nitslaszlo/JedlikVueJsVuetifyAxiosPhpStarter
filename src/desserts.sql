
-- 
-- Set character set the client will use to send SQL statements to the server
--
SET NAMES 'utf8';

DROP DATABASE IF EXISTS desserts;

CREATE DATABASE desserts
	CHARACTER SET utf8
	COLLATE utf8_hungarian_ci;
-- 
-- Set default database
--
USE desserts;

--
-- Definition for table dessert
--
DROP TABLE IF EXISTS dessert;

CREATE TABLE dessert (
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  calories INT DEFAULT NULL,
  fatPercent FLOAT DEFAULT NULL,
  isPaleo TINYINT(1) DEFAULT NULL,
  created DATETIME DEFAULT NULL,
  edited DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB;

-- 
-- Dumping data for table dessert
--
INSERT INTO dessert VALUES
(1, 'Frozen Yogurt', 159, 6.5, FALSE, NOW(), NULL),
(2, 'Ice cream sandwich', 237, 9.1, FALSE, NOW(), NULL),
(3, 'Eclair', 262, 16.4, FALSE, NOW(), NULL),
(4, 'Amaretto Apple Crispetti', 1210, 24, FALSE, NOW(), NULL),
(5, 'Paleo Cake', 120, 2.4, TRUE, NOW(), NULL),
(6, 'Komáromi Kisleány', 1456, 16.2, FALSE, NOW(), NULL);
