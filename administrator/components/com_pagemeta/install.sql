DROP TABLE IF EXISTS `#__pagemeta`;
CREATE TABLE IF NOT EXISTS `#__pagemeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `metadesc` text NOT NULL,
  `keywords` text NOT NULL,  
  `extra_meta` text NOT NULL,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `#__pageglobal`;
CREATE TABLE IF NOT EXISTS `#__pageglobal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `googgle_map_api_keys` text NOT NULL,
  `location_code` varchar(255) NOT NULL,
  `beach` varchar(255) NOT NULL,
  `photo_mini_slider_cat` varchar(255) NOT NULL,
  `photo_upload_cat` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `iphone` varchar(255) NOT NULL,
  `android` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
); 
INSERT INTO `#__pageglobal` (`id`, `site_name`, `email`, `googgle_map_api_keys`, `location_code`) VALUES
('', '', '', '', '');