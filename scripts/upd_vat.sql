update av_product set price = price / 1.196;
update av_product_attribute set price = price / 1.196;
update av_order_detail set 	total_price_tax_excl = total_price_tax_incl/1.196;
update `av_orders` set vat_rate = '19.6';

ALTER TABLE  `av_orders` ADD  `vat_rate` DECIMAL( 5, 3 ) NOT NULL;


ALTER TABLE `av_order_detail`
  DROP `attribute_name`,
  DROP `attribute_quantity`,
  DROP `attribute_price`,
  DROP `attribute_shipping`,
  DROP `attribut_surface`;

ALTER TABLE `av_order_detail`
  DROP `unit_price_tax_incl`,
  DROP `unit_price_tax_excl`;