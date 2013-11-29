ALTER TABLE  `av_order_product_attributes` ADD  `id_order_prd_attr` INT NOT NULL AUTO_INCREMENT FIRST ,
ADD PRIMARY KEY (  `id_order_prd_attr` );

ALTER TABLE  `av_order_product_attributes` CHANGE  `prixttc`  `prixttc` DECIMAL( 10, 2 ) NOT NULL;

ALTER TABLE  `av_orders` ADD  `delivery_comment` VARCHAR( 255 ) NOT NULL;