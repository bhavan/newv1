USE master;

SET autocommit=0;

-- users
INSERT INTO User (created, updated, active, email, password, username, first_name, last_name, year, gender, mobile_phone, registration_ip, login_type_id)
VALUES (NOW(), NOW(), true, 'vmazheru@salzinger.com', 'CRYqqpxIcz7EFB9wGndGQTSwvSA0buN10Wk6X/trxl9kZGX9WrHC5um+IXUS9aQG', 'j2vm', 'Vlad', 'Mazheru', 1968, 'M', '917-439-7193', '127.0.0.1', 1);

INSERT INTO Address (user_id, created, updated, active, address1, address2, city, state, postal_code, country)
VALUES ((SELECT MAX(id) FROM User), NOW(), NOW(), true, '324 Nelson Ave', 'Frnt', 'Staten Island', 'NY', '10308', 'USA');

-- contents
INSERT INTO Content(external_id, site_id, type_id, active)
VALUES (48, (SELECT mid FROM master WHERE site_url = 'demo.townwizard.com'), (SELECT id FROM ContentType WHERE name = 'Location'), true);

INSERT INTO Content(external_id, site_id, type_id, active)
VALUES (51, (SELECT mid FROM master WHERE site_url = 'demo.townwizard.com'), (SELECT id FROM ContentType WHERE name = 'Location'), true);

-- ratings
INSERT INTO Rating(user_id, content_id, created, updated, active, value)
VALUES(
    (SELECT id FROM User WHERE email = 'vmazheru@salzinger.com'), 
    (SELECT id FROM Content WHERE external_id = 48 AND 
                                  site_id = (SELECT mid FROM master WHERE site_url = 'demo.townwizard.com') AND 
                                  type_id = (SELECT id FROM ContentType WHERE name = 'Location')),
    NOW(), NOW(), true, 4              
);

INSERT INTO Rating(user_id, content_id, created, updated, active, value)
VALUES(
    (SELECT id FROM User WHERE email = 'vmazheru@salzinger.com'), 
    (SELECT id FROM Content WHERE external_id = 51 AND 
                                  site_id = (SELECT mid FROM master WHERE site_url = 'demo.townwizard.com') AND 
                                  type_id = (SELECT id FROM ContentType WHERE name = 'Location')),
    NOW(), NOW(), true, 4              
);


COMMIT;