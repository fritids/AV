ALTER TABLE  `av_orders` ADD INDEX (  `current_state` );ALTER TABLE  `changelog` ADD  `id_changelog` INT NOT NULL AUTO_INCREMENT FIRST ,ADD PRIMARY KEY (  `id_changelog` );