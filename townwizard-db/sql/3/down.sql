USE master;

SET @migration := 3;

-- check migration number
SELECT CASE migration WHEN @migration THEN 'SELECT ''Performing update...''' ELSE CONCAT('KILL CONNECTION ', connection_id()) END
INTO @stmt FROM Migration;

PREPARE stmt FROM @stmt;
EXECUTE stmt;
-- ////////////////////////////////////////// --

-- make email and password columns not nullable
ALTER TABLE User
MODIFY email VARCHAR(255) NOT NULL;
  
ALTER TABLE User
MODIFY password VARCHAR(100) NOT NULL;

-- create unique constraint on email
ALTER TABLE User
ADD CONSTRAINT unq_user_email UNIQUE (email);

-- drop unique constrain ont login type and external id
ALTER TABLE User
DROP INDEX unq_user_login;

-- drop loging type and external_id columns
ALTER TABLE User
DROP COLUMN external_id;

ALTER TABLE User
DROP COLUMN login_type_id;

-- drop login type table
DROP TABLE LoginType;


-- ////////////////////////////////////////// --
-- update migration
UPDATE Migration SET migration = migration - 1;

COMMIT;