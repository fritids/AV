ALTER TABLE  `av_order_detail` ADD  `is_debit_stock` INT( 1 ) NOT NULL DEFAULT  '0';


CREATE TABLE IF NOT EXISTS `av_product_warehouse` (
  `id_product_warehouse` int(11) NOT NULL AUTO_INCREMENT,
  `id_warehouse` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id_product_warehouse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_product_warehouse_hist` (
  `id_prd_warehouse_hist` int(11) NOT NULL AUTO_INCREMENT,
  `id_product_warehouse` int(11) NOT NULL,
  `id_order_detail` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `old_quantity` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id_prd_warehouse_hist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `av_product_warehouse` ADD UNIQUE (
`id_warehouse` ,
`id_product`
);
