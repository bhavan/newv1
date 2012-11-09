USE master;

CREATE TABLE migration (
  migration integer not null
);

INSERT INTO migration VALUES (0);

COMMIT;