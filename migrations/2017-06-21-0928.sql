ALTER TABLE `content_page` ADD `parent_id` int(11) NULL;

ALTER TABLE `content_page` ADD FOREIGN KEY (`parent_id`) REFERENCES `content_page` (`id`) ON DELETE SET NULL;