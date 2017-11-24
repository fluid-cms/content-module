DROP TABLE IF EXISTS `content_page`;
CREATE TABLE IF NOT EXISTS `content_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8_czech_ci NULL,
  `description` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `keywords` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `content_page` (`id`, `title`, `slug`, `description`, `keywords`, `content`) VALUES
(1, 'Welcome', 'welcome', 'welcome', 'welcome', '<p>This is sample CMS page, you can change it in administration.</p>\n');
