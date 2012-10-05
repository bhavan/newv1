-- townwizard_container
-- Copyright © 2012 - All rights reserved.
-- License: GNU/GPL
--
-- townwizard_container table(s) definition
--
--

--  `android_app_id` varchar(120) NOT NULL DEFAULT ''

CREATE TABLE IF NOT EXISTS `#__townwizard_partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL DEFAULT '',
  `creator_id` int(11) NOT NULL,
  `itunes_app_id` varchar(120) NOT NULL DEFAULT '',
  `android_app_id` varchar(120) NOT NULL DEFAULT '',
  `facebook_app_id` varchar(50) NOT NULL DEFAULT '',
  `partner_category_id` int(11) NOT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `website_url` varchar(50) NOT NULL DEFAULT '',
  `image` varchar(120) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(1) DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `featured_partner` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `creator_foreign_key` (`creator_id`),
  KEY `partner_category_foreign_key` (`partner_category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__townwizard_partner_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__townwizard_partner_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `street` varchar(120) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `latitude` decimal(21,15) NOT NULL DEFAULT '0.000000000000000',
  `longitude` decimal(21,15) NOT NULL DEFAULT '0.000000000000000',
  `map_zoom` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `partner_foreign_key` (`partner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__townwizard_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `default_image` varchar(120) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `default_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__townwizard_partner_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `image` varchar(120) DEFAULT NULL,
  `partner_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `ordering` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `partner_foreign_key` (`partner_id`),
  KEY `section_foreign_key` (`section_id`),
  KEY `parent_foreign_key` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- ALTER TABLE `#__townwizard_section` CHANGE `default_url` `default_url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
-- ALTER TABLE `#__townwizard_partner_section` CHANGE `url` `url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `#__townwizard_partner_section` ADD `ui_type` TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__townwizard_partner_section` ADD `json_api_url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `#__townwizard_section` ADD `default_json_api_url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- ALTER IGNORE TABLE `jos_townwizard_partner` ADD `android_app_id` VARCHAR(120) NOT NULL DEFAULT '' AFTER `itunes_app_id`;


INSERT IGNORE INTO `#__townwizard_partner` (`id`, `name`, `creator_id`, `itunes_app_id`, `partner_category_id`, `phone_number`, `website_url`, `image`, `published`, `priority`, `ordering`, `featured_partner`, `facebook_app_id`) VALUES
(1, 'Destin Shines', 62, '', 1, '', 'http://www.destinshines.com', '4f354a026e89d.png', 1, 0, 18, 0, '107352225952816'),
(2, 'Almaden Life', 62, '', 1, '', 'http://www.almadenlife.com', '4f354a0baa7ca.png', 1, 0, 20, 0, ''),
(3, 'Fort Morgan', 62, '443047888', 1, '', 'http://www.morgannow.com', '', 1, 0, 21, 0, ''),
(4, 'Decatur', 62, '427573910', 1, '', 'http://www.decaturhappenings.com', '', 1, 0, 22, 0, ''),
(5, 'MyLBILIfe', 62, '', 1, '', 'http://www.mylbilife.com', '', 1, 0, 23, 0, ''),
(6, '30A', 62, '307706936', 1, '', 'http://www.30a.com', '', 1, 0, 24, 0, ''),
(7, 'Sandestin', 62, '442837637', 2, '', 'http://www.sandestin.com', '', 1, 0, 25, 0, ''),
(8, 'Forsyth Shines', 62, '434841107', 1, '', 'http://www.forsythshines.com', '', 1, 0, 19, 0, ''),
(9, 'Mobile Bay', 62, '408400747', 1, '', 'http://www.alabamacoasting.com', '', 0, 0, 17, 0, ''),
(10, 'HighCountry365', 62, '424859417', 1, '', 'http://www.highcountry365.com', '', 1, 0, 16, 0, ''),
(11, 'MemphisPop', 62, '434831921', 1, '', 'http://www.memphispop.com', '', 1, 0, 15, 0, ''),
(12, 'TownWizardApps', 62, '', 1, '', 'http://www.townwizardapps.com', '4f3bc2f4bedd9.png', 1, 0, 14, 0, '107352225952816'),
(13, 'Test Partner', 62, '', 1, '', 'http://www.townwizardapps.com', '4f3c1a2cd17e7.png', 1, 0, 13, 0, ''),
(14, 'Cullman Life', 62, '', 1, '', 'http://www.cullmanlife.com', '4f3c23b924513.png', 1, 0, 12, 0, '200098370015860'),
(15, 'Camden County Cool', 62, '', 1, '', 'http://www.camdencountycool.com', '', 1, 0, 11, 0, ''),
(16, 'Hello St. Albert', 62, '', 1, '', 'http://www.hellostalbert.com', '', 1, 0, 10, 0, '316998761658640'),
(17, 'Hello Sherwood Park', 62, '', 1, '', 'http://www.hellosherwoodpark.com', '', 1, 0, 9, 0, ''),
(18, 'SETX Connect', 62, '', 1, '', 'http://www.setxconnect.com/', '4f4269d98332d.png', 1, 0, 8, 0, ''),
(19, 'Buffalo', 62, '', 1, '', 'http://www.everythingwny.com', '', 1, 0, 7, 0, ''),
(20, 'Pensacola', 62, '435815844', 1, '', 'http://www.pcola.com', '', 1, 0, 6, 0, ''),
(21, 'Panama City Beach', 62, '369901387', 1, '', 'http://www.panamacity.com', '', 1, 0, 5, 0, ''),
(22, 'Lafayette Venue', 62, '435760905', 1, '', 'http://www.lafayettevenue.com', '', 1, 0, 4, 0, ''),
(23, 'VB Compass', 62, '437820388', 1, '', 'http://www.vbcompass.com', '', 1, 0, 3, 0, ''),
(24, 'Baton Rouge Venue', 62, '389348641', 1, '', 'http://www.batonrougevenue.com', '', 1, 0, 2, 0, ''),
(25, 'Holland Scout', 62, '499258415', 2, '', 'http://www.hollandscout.com', '', 1, 0, 1, 0, '');


INSERT IGNORE INTO `#__townwizard_partner_category` (`id`, `title`) VALUES
(1, 'Towns'),
(2, 'Resorts');


INSERT IGNORE INTO `#__townwizard_partner_location` (`id`, `partner_id`, `street`, `city`, `state`, `country`, `zip`, `latitude`, `longitude`, `map_zoom`) VALUES
(1, 1, '981 Highway 98', 'Destin', 'Florida', 'USA', '32541', '30.389959000000001', '-86.477659000000017', 11),
(2, 2, '', 'Almaden', 'California', 'USA', '95120', '37.188990199999999', '-121.844874500000003', 8),
(3, 3, '', 'Fort Morgan', 'CO', 'USA', '80701', '40.250258199999998', '-103.799950899999999', 8),
(4, 4, '', 'Decatur', 'Alabama', 'USA', '35601', '34.627813000000003', '-87.042439300000012', 8),
(5, 5, 'streeet', 'Barnegat', 'NJ', 'USA', '08005', '39.766997799999999', '-74.250579000000016', 8),
(6, 6, '2236 East County Hwy 30a', 'Santa Rosa Beach', 'Florida', 'USA', '32459', '30.319581599999999', '-86.138119700000004', 8),
(7, 7, '', 'Miramar Beach', 'Florida', 'USA', '32550', '30.374366999999999', '-86.358557799999971', 8),
(30, 25, '', 'Holland', 'Michigan', 'USA', '49423', '42.787523499999999', '-86.108930100000009', 8),
(9, 8, '', 'Cumming', 'GA', 'USA', '30040', '34.207319599999998', '-84.140192599999978', 8),
(11, 9, '', 'Mobile', 'Alabama', 'USA', '36693', '30.618242299999999', '-88.138898900000015', 8),
(12, 10, '', 'Boone', 'North Carolina', 'USA', '28607', '36.216794999999998', '-81.674551699999995', 8),
(13, 11, '', 'Memphis', 'TN', 'USA', '38103', '35.154363199999999', '-90.060663699999964', 8),
(26, 21, '', 'Panama City Beach', 'Florida', 'USA', '', '30.176591400000000', '-85.805487900000003', 8),
(18, 12, '', 'Charlotte', 'North Carolina', 'USA', '28206', '35.255715899999998', '-80.826706399999978', 8),
(19, 14, '', 'Cullman', 'Alabama', 'USA', '35055', '34.174820799999999', '-86.843612399999984', 8),
(20, 15, '', 'Collingswood', 'New Jersey', 'USA', '08108', '-25.363882000000000', '131.044922000000042', 8),
(21, 16, '', 'St Albert', '', 'Canada', 'T8N', '53.630475300000001', '-113.625641999999971', 8),
(22, 17, '', 'Sherwood Park', '', 'Canada', '', '53.523319000000008', '-113.308756000000017', 8),
(23, 18, '', 'Orange', 'Texas', 'USA', '77630', '30.079801400000001', '-93.845173100000011', 8),
(24, 19, '', 'Buffalo', 'New York', 'USA', '14202', '42.894316099999998', '-78.873649300000011', 8),
(25, 20, '', 'Pensacola', 'Florida', 'USA', '', '30.421308999999990', '-87.216914900000006', 8),
(27, 22, '', 'Lafayette', 'Louisiana', 'USA', '', '30.224089700000000', '-92.019842700000027', 8),
(28, 23, '', 'Virginia Beach', 'Virginia', 'USA', '23454', '36.852926300000000', '-75.977984999999990', 8),
(29, 24, '', 'Baton Rouge', 'Louisiana', 'USA', '70801', '30.448377900000001', '-91.188708000000020', 8);


INSERT IGNORE INTO `#__townwizard_partner_section` (`id`, `name`, `image`, `partner_id`, `section_id`, `parent_id`, `url`, `ordering`) VALUES
(1, 'News', '4f3bcee1f1bc1.png', 1, 1, 0, 'components/com_shines/iphone-30a-today.php', 4),
(3, 'Photos', '4f3bcf0b4937a.png', 1, 2, 0, 'android/galleries.php', 6),
(4, '', '', 1, 3, 0, 'components/com_shines/iAllUserPhotos.php', 7),
(5, 'Videoz', '4f3bd02587126.png', 1, 4, 0, '/android/videos.php', 8),
(23, '', '4f425b84f3895.png', 1, 5, 0, '', 2),
(7, '', '4f3bcf9db0c68.png', 1, 6, 0, 'components/com_shines/events.php', 5),
(47, '', '4f425b59a8d39.jpg', 1, 7, 0, '', 3),
(9, 'Restaurants', '', 1, 8, 0, 'components/com_shines/restaurants.php', 9),
(10, 'News', '', 2, 1, 0, 'components/com_shines/iphone-30a-today.php', 2),
(11, 'Events', '', 2, 6, 0, 'components/com_shines/events.php', 3),
(12, 'Weather', '', 2, 5, 0, 'weather.php', 4),
(13, 'Photos', '', 2, 2, 0, 'android/galleries.php', 5),
(24, '', '', 12, 5, 0, '', 2),
(21, 'Videos', '', 12, 4, 0, '', 4),
(20, '', '', 12, 1, 0, '', 3),
(19, NULL, NULL, 0, 8, 0, NULL, 0),
(25, '', '', 12, 2, 0, '', 1),
(26, NULL, NULL, 0, 8, 0, NULL, 0),
(27, NULL, NULL, 14, 8, 0, NULL, 3),
(28, '', '', 14, 2, 0, '', 4),
(29, NULL, NULL, 0, 8, 0, NULL, 0),
(30, '', '', 15, 1, 0, '', 5),
(31, '', '', 15, 2, 0, '', 4),
(32, '', '', 15, 8, 0, '', 3),
(33, '', '', 15, 7, 0, '', 2),
(34, '', '', 15, 6, 0, '', 1),
(35, '', '', 14, 6, 0, '', 2),
(36, NULL, NULL, 0, 8, 0, NULL, 0),
(37, NULL, NULL, 16, 8, 0, NULL, 3),
(38, '', '', 16, 1, 0, '', 4),
(39, '', '', 16, 6, 0, '', 2),
(40, '', '', 16, 7, 0, '', 1),
(41, NULL, NULL, 0, 8, 0, NULL, 0),
(42, '', '', 17, 6, 0, '', 3),
(46, '', '', 14, 1, 0, '', 1),
(44, '', '', 17, 7, 0, '', 1),
(48, NULL, NULL, 0, 8, 0, NULL, 0),
(49, '', '', 18, 1, 0, '', 3),
(50, '', '', 18, 8, 0, '', 2),
(51, '', '', 18, 6, 0, '', 1),
(52, NULL, NULL, 0, 8, 0, NULL, 0),
(53, '', '', 19, 1, 0, '', 4),
(54, '', '', 19, 6, 0, '', 3),
(55, '', '', 19, 8, 0, '', 2),
(56, '', '', 19, 2, 0, '', 1),
(57, NULL, NULL, 18, 7, 0, NULL, 0),
(58, NULL, NULL, 0, 8, 0, NULL, 0),
(59, NULL, NULL, 0, 8, 0, NULL, 0),
(60, NULL, NULL, 0, 8, 0, NULL, 0),
(61, NULL, NULL, 0, 8, 0, NULL, 0),
(62, NULL, NULL, 0, 8, 0, NULL, 0),
(64, '', '', 2, 7, 0, '', 1),
(65, '', '', 1, 10, 0, '', 1),
(66, NULL, NULL, 0, 8, 0, NULL, 0);


INSERT IGNORE INTO `#__townwizard_section` (`id`, `name`, `default_image`, `default_url`, `is_default`) VALUES
(1, 'News', '4f3c1f1404c98.png', 'components/com_shines/iphone-30a-today.php', 1),
(2, 'Photos', '4f3c169479081.png', 'components/com_shines/iAllUserPhotos.php', 1),
(4, 'Videos', '4f3c1d1c68c1d.png', 'android/videos.php', 1),
(5, 'Weather', '4f4263205206a.png', 'weather.php', 0),
(6, 'Events', '4f4262fc970b3.png', 'components/com_shines/events.php', 1),
(7, 'Places', '4f426331a23e1.jpg', 'components/com_shines/places.php', 1),
(8, 'Restaurants', '4f3c16cbf085e.png', 'components/com_shines/restaurants.php', 1),
(9, 'Not a default partner section', '4f3c1ac6e3804.png', 'default.php', 0),
(10, 'Attractions', '4f42839db7a15.png', 'components/com_shines/attractions.php', 0);


