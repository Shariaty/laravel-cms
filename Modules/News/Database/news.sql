CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `img` varchar(50) DEFAULT NULL,
  `is_published` enum('Y','N') NOT NULL DEFAULT 'Y',
  `views` mediumint(9) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

ALTER TABLE `news` DISABLE KEYS;
REPLACE INTO `news` (`id`, `title`, `slug`, `body`, `img`, `is_published`, `views`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'dsada', 'dsada', '<p>dsadsad</p>', '1505813067.png', 'N', 1, '2017-09-19 13:54:28', '2017-09-19 13:56:03', '2017-09-19 13:56:03');
ALTER TABLE `news` ENABLE KEYS;

CREATE TABLE IF NOT EXISTS `news_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `desc` text,
  `is_published` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

ALTER TABLE `news_categories` DISABLE KEYS ;
REPLACE INTO `news_categories` (`id`, `title`, `slug`, `desc`, `is_published`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'dsad', 'dasda', NULL, 'Y', '2017-09-19 13:53:58', '2017-09-19 13:54:11', NULL);
ALTER TABLE `news_categories` ENABLE KEYS ;

CREATE TABLE IF NOT EXISTS `news_newscategories` (
  `news_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  KEY `FK_news_newscategories_news` (`news_id`),
  KEY `FK_news_newscategories_news_categories` (`category_id`),
  CONSTRAINT `FK_news_newscategories_news` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`),
  CONSTRAINT `FK_news_newscategories_news_categories` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `news_newscategories` DISABLE KEYS ;
REPLACE INTO `news_newscategories` (`news_id`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, '2017-09-19 13:54:28', '2017-09-19 13:54:28', NULL);
ALTER TABLE `news_newscategories` ENABLE KEYS ;

