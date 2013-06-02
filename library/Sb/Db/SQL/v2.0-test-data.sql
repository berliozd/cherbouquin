
SET NAMES UTF8;

--
-- Contenu de la table `s1b_groups`
--

INSERT INTO `s1b_groups` (`id`, `name`, `grouptype_id`, `is_validated`) VALUES
(1, 'Groupe libraire', 2, 1),
(2, 'Groupe bloggeur', 6, 1),
(3, 'Groupe groupe de lecteurs', 7, 1);

UPDATE `s1b_groupchronicles` SET `group_id` = '1' WHERE `s1b_groupchronicles`.`id` = 1;
UPDATE `s1b_groupchronicles` SET `group_id` = '2' WHERE `s1b_groupchronicles`.`id` = 2;
UPDATE `s1b_groupchronicles` SET `group_id` = '3' WHERE `s1b_groupchronicles`.`id` = 3;
UPDATE `s1b_groupchronicles` SET `group_id` = '1' WHERE `s1b_groupchronicles`.`id` = 4;
UPDATE `s1b_groupchronicles` SET `group_id` = '2' WHERE `s1b_groupchronicles`.`id` = 5;
UPDATE `s1b_groupchronicles` SET `group_id` = '3' WHERE `s1b_groupchronicles`.`id` = 6;
UPDATE `s1b_groupchronicles` SET `group_id` = '1' WHERE `s1b_groupchronicles`.`id` = 7;
UPDATE `s1b_groupchronicles` SET `group_id` = '2' WHERE `s1b_groupchronicles`.`id` = 8;
UPDATE `s1b_groupchronicles` SET `group_id` = '3' WHERE `s1b_groupchronicles`.`id` = 9;
UPDATE `s1b_groupchronicles` SET `group_id` = '1' WHERE `s1b_groupchronicles`.`id` = 10;
UPDATE `s1b_groupchronicles` SET `group_id` = '2' WHERE `s1b_groupchronicles`.`id` = 11;
UPDATE `s1b_groupchronicles` SET `group_id` = '3' WHERE `s1b_groupchronicles`.`id` = 12;
