USE master;

SET @migration := 1;

-- check migration number
SELECT CASE migration WHEN @migration - 1 THEN 'SELECT ''Performing update...''' ELSE CONCAT('KILL CONNECTION ', connection_id()) END
INTO @stmt FROM migration;

PREPARE stmt FROM @stmt;
EXECUTE stmt;
-- ////////////////////////////////////////// --

CREATE TABLE user(
  id BIGINT NOT NULL AUTO_INCREMENT,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  active BIT NOT NULL,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  first_name VARCHAR(100),
  last_name VARCHAR(100),  
  CONSTRAINT pk_user PRIMARY KEY (id),
  CONSTRAINT unq_user_email UNIQUE(email)
);

CREATE TABLE address(
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
  province VARCHAR(50),
  CONSTRAINT pk_address PRIMARY KEY (id),
  CONSTRAINT fk_address_user FOREIGN KEY(user_id) REFERENCES user(id)
);

-- ////////////////////////////////////////// --
-- update migration
UPDATE migration SET migration = migration + 1;

COMMIT;