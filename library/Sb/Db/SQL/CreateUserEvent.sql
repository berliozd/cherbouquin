CREATE TABLE IF NOT EXISTS `s1b_userevents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `old_value` varchar(5000) DEFAULT NULL,
  `new_value` varchar(5000) DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
