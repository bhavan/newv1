USE master;

SET @migration := 3;

-- check migration number
SELECT CASE migration WHEN @migration - 1 THEN 'SELECT ''Performing update...''' ELSE CONCAT('KILL CONNECTION ', connection_id()) END
INTO @stmt FROM migration;

PREPARE stmt FROM @stmt;
EXECUTE stmt;
-- ////////////////////////////////////////// --

-- create table for login types
CREATE TABLE LoginType (
  id INT NOT NULL,
  name VARCHAR(20),
  CONSTRAINT pk_login_type PRIMARY KEY (id),
  CONSTRAINT unq_login_type_name UNIQUE(name)
) ENGINE = InnoDB;

SET autocommit=0;

INSERT INTO LoginType VALUES (1, 'Townwizard');
INSERT INTO LoginType VALUES (2, 'Facebook');
INSERT INTO LoginType VALUES (3, 'Twitter');

COMMIT;

-- add two columns to user table: for login type and external id
ALTER TABLE User
ADD login_type_id INT NOT NULL;

ALTER TABLE User
ADD external_id BIGINT;

-- add a unique constraint on email/login_type/external_id
ALTER TABLE User
ADD CONSTRAINT unq_user_login UNIQUE (email, login_type_id, external_id, active);

-- remove unique constraint on email
ALTER TABLE User
DROP INDEX unq_user_email;

-- make email and password columns nullable
ALTER TABLE User
MODIFY email VARCHAR(255) NULL;
  
ALTER TABLE User
MODIFY password VARCHAR(100) NULL;

-- ////////////////////////////////////////// --
-- update migration
UPDATE Migration SET migration = migration + 1;

COMMIT;