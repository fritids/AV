
CREATE TABLE IF NOT EXISTS `av_tax` (
  `id_tax` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,  
  `rate` decimal(10,3) DEFAULT NULL,    
  PRIMARY KEY (`id_tax`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO  `av_tax` (`id_tax` ,`name` ,`rate`)
VALUES (NULL ,  'TVA 19,6%',  '19.6'), ( NULL ,  'TVA 20%',  '20'), (NULL ,  'TVA POSE 10%',  '10') ;

ALTER TABLE  `av_range_weight` CHANGE  `delivery_ratio`  `price` DECIMAL( 10, 2 ) NOT NULL;
ALTER TABLE  `av_range_weight` ADD  `id_tax` INT( 3 ) NOT NULL;

INSERT INTO  `av_range_weight` (`id_range_weight` ,`delimiter1` ,`delimiter2` ,`price` ,`id_tax`)
VALUES (NULL ,  '0',  '100',  '20.83',  '2');

-- produit

ALTER TABLE  `av_product` DROP unit_price_ratio;
ALTER TABLE  `av_product` ADD  `id_tax_standard` INT( 3 ) NOT NULL AFTER  `quantity`;
ALTER TABLE  `av_product` ADD  `id_tax_pose` INT( 3 ) NOT NULL AFTER `id_tax_standard`;

update av_product set id_tax_standard = 2;
update av_product set id_tax_pose = 3;

-- orders
/*ALTER TABLE  `av_orders` ADD shipping_tax_incl decimal(10,2) DEFAULT NULL,
						 ADD shipping_tax_excl decimal(10,2) DEFAULT NULL,
						 ADD shipping_id_tax int(3) DEFAULT NULL;
						 
update `av_orders` set shipping_tax_incl = 25, shipping_tax_excl = 25/1.196 , shipping_id_tax = 1 where date(date_add) < '2014-01-01';
update `av_orders` set shipping_tax_incl = 25, shipping_tax_excl = 25/1.20 , shipping_id_tax = 2 where date(date_add) >= '2014-01-01';
*/

-- order detail
ALTER TABLE `av_order_detail`
  DROP `product_shipping`,
  DROP `id_supplier`,
  DROP `product_attribute_id`,
  DROP `product_depth`;
  
ALTER TABLE `av_order_detail` ADD `product_id_tax` INT( 3 ) NOT NULL AFTER  `voucher_code`;

update av_order_detail set product_id_tax = 1 where id_order in (select id_order from av_orders where date(date_add) < '2014-01-01');
update av_order_detail set product_id_tax = 2 where id_order in (select id_order from av_orders where date(date_add) >= '2014-01-01');

update av_order_detail a
join av_tax b
on product_id_tax = id_tax
set total_price_tax_excl = total_price_tax_incl / (rate/100 + 1);


-- devis
ALTER TABLE `av_devis_detail`
  DROP `product_shipping`,
  DROP `product_depth`,
  DROP `unit_price_tax_incl`,
  DROP `unit_price_tax_excl`,
  DROP `attribute_name`,
  DROP `attribute_quantity`,
  DROP `attribute_price`,
  DROP `attribute_shipping`,
  DROP `attribut_surface`;
  
ALTER TABLE `av_devis_detail` ADD `product_id_tax` INT( 3 ) NOT NULL AFTER  `product_weight`;
update av_devis_detail set product_id_tax = 1 where id_devis in (select id_devis from av_devis where date(date_add) < '2014-01-01');
update av_devis_detail set product_id_tax = 2 where id_devis in (select id_devis from av_devis where date(date_add) >= '2014-01-01');


--> upddate mv_form
