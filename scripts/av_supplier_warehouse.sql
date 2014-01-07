CREATE TABLE IF NOT EXISTS `av_supplier_warehouse` (
  `id_supplier_warehouse` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) NOT NULL,
  `id_warehouse` int(11) NOT NULL  ,
  PRIMARY KEY (`id_supplier_warehouse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_warehouse` (
  `id_warehouse` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id_warehouse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `av_order_detail` ADD  `id_supplier_warehouse` INT NULL AFTER  `id_supplier`
