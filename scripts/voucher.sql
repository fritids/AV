
CREATE TABLE IF NOT EXISTS `av_voucher` (
  `id_voucher` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  PRIMARY KEY (`id_voucher`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

drop table av_voucher_rule;
CREATE TABLE IF NOT EXISTS `av_voucher_rule` (
  `id_voucher_rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_voucher` int(10) NOT NULL ,
  `group` varchar(20) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id_voucher_rule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO  `allovitres`.`av_voucher_rule` (
`id_voucher_rule` ,
`id_voucher` ,
`group` ,
`value`
)
VALUES (
NULL ,  '1',  'category',  '12'
);


ALTER TABLE  `av_order_detail` ADD  `discount` DECIMAL( 10, 2 ) NOT NULL AFTER  `attribut_surface` ,
ADD  `voucher_code` VARCHAR( 20 ) NOT NULL AFTER  `discount`;

ALTER TABLE  `av_order_detail` CHANGE  `discount`  `discount` DECIMAL( 10, 2 ) NULL ,
CHANGE  `voucher_code`  `voucher_code` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;