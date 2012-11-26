USE master;

SET @migration := 2;

-- check migration number
SELECT CASE migration WHEN @migration - 1 THEN 'SELECT ''Performing update...''' ELSE CONCAT('KILL CONNECTION ', connection_id()) END
INTO @stmt FROM migration;

PREPARE stmt FROM @stmt;
EXECUTE stmt;
-- ////////////////////////////////////////// --

CREATE TABLE ContentType (
  id INT NOT NULL,
  name VARCHAR(20),
  CONSTRAINT pk_content_type PRIMARY KEY (id),
  CONSTRAINT unq_content_type_name UNIQUE(name)
) ENGINE = InnoDB;

CREATE TABLE Content (
  id BIGINT NOT NULL AUTO_INCREMENT,
  external_id BIGINT NOT NULL,
  site_id INT NOT NULL,
  type_id INT NOT NULL,
  active BIT NOT NULL,
  CONSTRAINT pk_content PRIMARY KEY (id),
  CONSTRAINT fk_content_site FOREIGN KEY(site_id) REFERENCES master(mid),
  CONSTRAINT fk_content_content_type FOREIGN KEY(type_id) REFERENCES ContentType(id),
  CONSTRAINT unq_content UNIQUE(external_id, site_id, type_id)
) ENGINE = InnoDB;

CREATE TABLE Rating (
  id BIGINT NOT NULL AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  content_id BIGINT NOT NULL,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  active BIT NOT NULL,
  value FLOAT NOT NULL,
  CONSTRAINT pk_favorite PRIMARY KEY(id),
  CONSTRAINT fk_favorite_user FOREIGN KEY(user_id) REFERENCES User(id),
  CONSTRAINT fk_favorite_content FOREIGN KEY(content_id) REFERENCES Content(id)
) ENGINE = InnoDB;


SET autocommit=0;

INSERT INTO ContentType VALUES (1, 'Location');
INSERT INTO ContentType VALUES (2, 'Event');

-- ////////////////////////////////////////// --
-- update migration
UPDATE Migration SET migration = migration + 1;

COMMIT;