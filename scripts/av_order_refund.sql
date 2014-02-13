delete from av_order_refund;
delete from av_order_refund_detail;

INSERT INTO  `av_order_status` (`id_statut` ,`id_level` ,`title`) VALUES (NULL ,  '1',  'Remboursé');

ALTER TABLE  `av_order_refund_detail` AUTO_INCREMENT =1;

CREATE TABLE IF NOT EXISTS `av_order_refund` (
  `id_order_refund` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_order` datetime NOT NULL,
  `date_refund` datetime NOT NULL,
  `payment` varchar(255) NOT NULL,
  `total_shipping` decimal(10,2) DEFAULT NULL,  
  `total_refund` decimal(10,2) DEFAULT NULL,  
  `vat_rate` decimal(5,3) DEFAULT NULL,  
  `refund_comment` text NOT NULL,  
  PRIMARY KEY (`id_order_refund`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `av_order_refund_detail` (
  `id_order_refund_detail` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order_refund` int(10) unsigned NOT NULL,
  `id_order_detail` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `id_supplier_warehouse` int(11) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `product_price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `product_width` int(11) DEFAULT NULL,
  `product_height` int(11) DEFAULT NULL,
  `product_weight` decimal(10,2) DEFAULT NULL,
  `total_price_tax_incl` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_price_tax_excl` decimal(20,2) NOT NULL DEFAULT '0.00',
  `is_product_custom` int(1) NOT NULL DEFAULT '0',
  `is_debit_stock` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_order_refund_detail`),
  KEY `id_order_refund` (`id_order_refund`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
