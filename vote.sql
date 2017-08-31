CREATE TABLE IF NOT EXISTS `bt_demvote` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content_id` VARCHAR(100) NOT NULL,
  `voteup` INT(11) NOT NULL,
  `votedown` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
);