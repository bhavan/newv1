USE master;

SET @migration := 1;

-- check migration number
SELECT CASE migration WHEN @migration THEN 'SELECT ''Performing update...''' ELSE CONCAT('KILL CONNECTION ', connection_id()) END
INTO @stmt FROM migration;

PREPARE stmt FROM @stmt;
EXECUTE stmt;
-- ////////////////////////////////////////// --

DROP TABLE address;
DROP TABLE user;

-- ////////////////////////////////////////// --
-- update migration
UPDATE migration SET migration = migration - 1;

COMMIT;