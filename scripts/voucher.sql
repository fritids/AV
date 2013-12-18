

CREATE TABLE IF NOT EXISTS `av_voucher` (
  `id_voucher` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `quantity` int(10) DEFAULT NULL,
  `reduction_percent` decimal(5,2) DEFAULT NULL,
  `reduction_amount` decimal(5,2) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_voucher`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `av_voucher` (`id_voucher`, `id_customer`, `code`, `title`, `description`, `start_date`, `end_date`, `date_add`, `quantity`, `reduction_percent`, `reduction_amount`, `active`) VALUES
(1, 0, 'CODE1', 'TEST', 'TEST DESC', '2013-12-17 00:00:00', '2013-12-17 00:00:00', '2013-12-17 00:00:00', 10, NULL, '10.00', 1);


ALTER TABLE  `av_order_detail` ADD  `discount` DECIMAL( 10, 2 ) NOT NULL AFTER  `attribut_surface` ,
ADD  `voucher_code` VARCHAR( 20 ) NOT NULL AFTER  `discount`;

ALTER TABLE  `av_order_detail` CHANGE  `discount`  `discount` DECIMAL( 10, 2 ) NULL ,
CHANGE  `voucher_code`  `voucher_code` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

ALTER TABLE  `av_orders` ADD  `order_voucher` VARCHAR( 20 )  NULL AFTER  `payment`;
ALTER TABLE  `av_orders` ADD  `total_discount` DECIMAL( 10, 2 ) NULL AFTER  `payment`;
