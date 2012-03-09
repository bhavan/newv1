CREATE TABLE IF NOT EXISTS `#__ak_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `configuration` longtext,
  `filters` longtext,
  PRIMARY KEY  (`id`)
);
INSERT IGNORE INTO `#__ak_profiles` (`id`,`description`, `configuration`, `filters`) VALUES (1,'Default Backup Profile','','');

CREATE TABLE IF NOT EXISTS `#__ak_stats` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `description` varchar(255) NOT NULL,
  `comment` longtext,
  `backupstart` timestamp NOT NULL default '0000-00-00 00:00:00',
  `backupend` timestamp NOT NULL default '0000-00-00 00:00:00',
  `status` enum('run','fail','complete') NOT NULL default 'run',
  `origin` enum('backend','frontend','cli') NOT NULL default 'backend',
  `type` ENUM('full','dbonly','extradbonly','alldb','fileonly') NOT NULL DEFAULT 'full',
  `profile_id` bigint(20) NOT NULL default '1',
  `archivename` longtext,
  `absolute_path` longtext,
  `multipart` INT NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
);

ALTER TABLE `#__ak_profiles` CONVERT TO CHARACTER SET utf8;
ALTER TABLE `#__ak_stats` CONVERT TO CHARACTER SET utf8;
