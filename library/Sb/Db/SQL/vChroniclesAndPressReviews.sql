--
-- Update chronicles tables
--

ALTER TABLE `s1b_groupchronicles`  ADD `keywords` VARCHAR(250) NULL,  ADD `tag_id` INT(11) NULL DEFAULT NULL,  ADD `is_validated` TINYINT NOT NULL DEFAULT '0',  ADD `nb_views` INT(10) NOT NULL DEFAULT '0',  ADD `image` VARCHAR(250) NULL;



--
-- Structure de la table `s1b_pressreviews`
--

CREATE TABLE IF NOT EXISTS `s1b_pressreviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `text` varchar(2000) NOT NULL,
  `link` varchar(255) NOT NULL,
  `media_id` int(11) NOT NULL,
  `author` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Update press reviews table
--

ALTER TABLE `s1b_pressreviews`  ADD `keywords` VARCHAR(250) NULL,  ADD `tag_id` INT(11) NULL DEFAULT NULL,  ADD `is_validated` TINYINT NOT NULL DEFAULT '0',  ADD `type` TINYINT NOT NULL DEFAULT '0' COMMENT '0 = article, 1 = video';


--
-- Update users table
--

ALTER TABLE `s1b_users` ADD ` is_partner` TINYINT NULL DEFAULT '0';


--
-- Create group types table
--

CREATE TABLE `s1b_grouptypes` (
`id` INT NOT NULL AUTO_INCREMENT ,
`label` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `s1b_grouptypes` (`id`, `label`) VALUES (NULL, 'Auteur'), (NULL, 'Libraire'), (NULL, 'Editeur'), (NULL, 'Bibliothèque'), (NULL, 'Comité d''entreprise'), (NULL, 'Bloggeur'), (NULL, 'Groupe de lecteurs'), (NULL, 'Presse');


--
-- Create groups table
--

CREATE TABLE `s1b_groups` (
`id` INT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 150 ) NOT NULL ,
`grouptype_id` INT NOT NULL ,
`is_validated` TINYINT NOT NULL DEFAULT '0',
PRIMARY KEY ( `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;


--
-- Create group users table
--

CREATE TABLE `s1b_group_users` (
`id` INT NOT NULL AUTO_INCREMENT ,
`group_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`is_superadmin` TINYINT NOT NULL DEFAULT '0',
`is_importation_activated` TINYINT NOT NULL DEFAULT '0' COMMENT 'Tell if group active member userbooks will be added automatically to the group books',
`is_anonymous` TINYINT NOT NULL DEFAULT '0',
PRIMARY KEY ( `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;


--
-- Create press reviews subscribers table
--

CREATE TABLE `s1b_pressreviews_subscribers` (
`id` INT NOT NULL AUTO_INCREMENT ,
`email` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
