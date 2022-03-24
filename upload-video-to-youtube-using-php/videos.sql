CREATE TABLE `videos` (
 `video_id` int(11) NOT NULL AUTO_INCREMENT,
 `video_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `video_description` text COLLATE utf8_unicode_ci NOT NULL,
 `video_tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `video_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `youtube_video_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `status` tinyint(1) NOT NULL DEFAULT '1',
 PRIMARY KEY (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
