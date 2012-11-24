USE master;

SET @migration := 1;

-- check migration number
SELECT CASE migration WHEN @migration - 1 THEN 'SELECT ''Performing update...''' ELSE CONCAT('KILL CONNECTION ', connection_id()) END
INTO @stmt FROM migration;

PREPARE stmt FROM @stmt;
EXECUTE stmt;
-- ////////////////////////////////////////// --

CREATE TABLE User(
  id BIGINT NOT NULL AUTO_INCREMENT,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  active BIT NOT NULL,  
  email VARCHAR(255) NOT NULL,
  password VARCHAR(100) NOT NULL,
  username VARCHAR(100),
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  year INTEGER,
  gender CHAR(1),
  mobile_phone VARCHAR(20),
  registration_ip VARCHAR(15),
  CONSTRAINT pk_user PRIMARY KEY (id),
  CONSTRAINT unq_user_email UNIQUE(email)
) ENGINE = InnoDB;

CREATE TABLE Address(
  id BIGINT NOT NULL AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  active BIT NOT NULL,
  address1 VARCHAR(255) NOT NULL,
  address2 VARCHAR(30),
  city VARCHAR(50) NOT NULL,
  state VARCHAR(50) NOT NULL,
  postal_code VARCHAR(30) NOT NULL,
  country VARCHAR(50),
  CONSTRAINT pk_address PRIMARY KEY (id),
  CONSTRAINT fk_address_user FOREIGN KEY(user_id) REFERENCES User(id)
) ENGINE = InnoDB;

-- ////////////////////////////////////////// --
-- update migration
UPDATE Migration SET migration = migration + 1;

COMMIT;