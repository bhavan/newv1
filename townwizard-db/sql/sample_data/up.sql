USE master;

SET autocommit=0;

-- users
INSERT INTO User (created, updated, active, email, password, username, first_name, last_name, year, gender, mobile_phone, registration_ip, login_type_id)
VALUES (NOW(), NOW(), true, 'vmazheru@salzinger.com', 'CRYqqpxIcz7EFB9wGndGQTSwvSA0buN10Wk6X/trxl9kZGX9WrHC5um+IXUS9aQG', 'j2vm', 'Vlad', 'Mazheru', 1968, 'M', '917-439-7193', '127.0.0.1', 1);

INSERT INTO Address (user_id, created, updated, active, address1, address2, city, state, postal_code, country)
VALUES ((SELECT MAX(id) FROM User), NOW(), NOW(), true, '324 Nelson Ave', 'Frnt', 'Staten Island', 'NY', '10308', 'USA');

COMMIT;