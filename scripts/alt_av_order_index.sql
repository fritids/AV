ALTER TABLE  `av_orders` ADD INDEX (  `current_state` );ALTER TABLE  `changelog` ADD  `id_changelog` INT NOT NULL AUTO_INCREMENT FIRST ,ADD PRIMARY KEY (  `id_changelog` );ALTER TABLE  `av_order_detail` ADD INDEX (  `product_current_state` ) ;ALTER TABLE  `av_orders` ADD INDEX (  `invoice_date` );