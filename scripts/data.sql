CREATE TABLE `background` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `md5` varchar(255) DEFAULT NULL,
  `weather` varchar(255) DEFAULT NULL,
  `ge_hour` int DEFAULT 0,
  `le_hour` int DEFAULT 24,
  `ge_week` int DEFAULT 0,
  `le_week` int DEFAULT 6,
  `ge_month` int DEFAULT 0,
  `le_month` int DEFAULT 31,
  `user_id` int DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `figure` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `md5` varchar(255) DEFAULT NULL,
  `weather` varchar(255) DEFAULT NULL,
  `ge_hour` int DEFAULT 0,
  `le_hour` int DEFAULT 24,
  `ge_week` int DEFAULT 0,
  `le_week` int DEFAULT 6,
  `ge_month` int DEFAULT 0,
  `le_month` int DEFAULT 31,
  `user_id` int DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `oneword` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `word` text DEFAULT NULL,
  `weather` varchar(255) DEFAULT NULL,
  `ge_hour` int DEFAULT 0,
  `le_hour` int DEFAULT 24,
  `ge_week` int DEFAULT 0,
  `le_week` int DEFAULT 6,
  `ge_month` int DEFAULT 0,
  `le_month` int DEFAULT 31,
  `user_id` int DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) DEFAULT CHARSET=utf8;



