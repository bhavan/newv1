USE master;

SET @migration := 1;

-- check migration number
SELECT CASE migration WHEN @migration THEN 'SELECT ''Performing update...''' ELSE CONCAT('KILL CONNECTION ', connection_id()) END
INTO @stmt FROM Migration;

PREPARE stmt FROM @stmt;
EXECUTE stmt;
-- ////////////////////////////////////////// --

DROP TABLE Address;
DROP TABLE User;

-- ////////////////////////////////////////// --
-- update migration
UPDATE Migration SET migration = migration - 1;

COMMIT;