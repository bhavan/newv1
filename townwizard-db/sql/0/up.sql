USE master;

CREATE TABLE Migration (
  migration integer not null
);

INSERT INTO Migration VALUES (0);

COMMIT;