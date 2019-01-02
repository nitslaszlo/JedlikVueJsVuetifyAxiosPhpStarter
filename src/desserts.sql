
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
  name VARCHAR(50) DEFAULT NULL,
  calories INT(11) DEFAULT NULL,
  fatPercent FLOAT DEFAULT NULL,
  isPaleo TINYINT(1) DEFAULT NULL,
  created DATETIME DEFAULT NULL,
  edited DATETIME DEFAULT NULL,
  PRIMARY KEY (name)
)
ENGINE = INNODB;

-- 
-- Dumping data for table dessert
--
INSERT INTO dessert VALUES
('Frozen Yogurt', 159, 6.5, FALSE, NOW(), NULL),
('Ice cream sandwich', 237, 9.1, FALSE, NOW(), NULL),
('Eclair', 262, 16.4, FALSE, NOW(), NULL),
('Amaretto Apple Crispetti', 1210, 24, FALSE, NOW(), NULL),
('Paleo Cake', 120, 2.4, TRUE, NOW(), NULL),
('Komáromi Kisleány', 1456, 16.2, FALSE, NOW(), NULL);
