ALTER TABLE  `av_product_attribute` ADD  `price_pose` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0';
update av_product_attribute set `price_pose` = `price`*2;

ALTER TABLE  `av_product` ADD  `price_pose` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0';
update av_product set `price_pose` = `price`*2;

ALTER TABLE  `av_order_detail` ADD  `is_product_posable` int(1) NOT NULL DEFAULT  '0';


CREATE TABLE IF NOT EXISTS `av_poseur` (
  `id_poseur` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  `date_last_login` datetime NOT NULL,  
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `active` int(1) default 1,  
  PRIMARY KEY (`id_poseur`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_poseur_departement` (
  `id_poseur_dept` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_departement` int(10) NOT NULL,
  `id_poseur` int(10) NOT NULL,  
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,    
  PRIMARY KEY (`id_poseur_dept`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_poseur_address` (
  `id_poseur_address` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_poseur` int(10) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  `alias` varchar(25) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) NOT NULL,
  `postcode` varchar(12) NOT NULL,
  `country` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `phone_mobile` varchar(32) NOT NULL,
  `active` int(1) default 1,  
  PRIMARY KEY (`id_poseur_address`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_prestation` (
  `id_prestation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  `category` int(1) NOT NULL,  
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` int(1) default 1,  
  PRIMARY KEY (`id_prestation`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_poseur_prestation` (
  `id_poseur_prestation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_poseur` int(10) NOT NULL,
  `id_prestation` int(10) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  `date_stda` datetime NOT NULL,
  `date_enda` datetime NOT NULL,
  `price_ht` decimal(10,2) NOT NULL,  
  `applicable_vat` decimal(5,2) NOT NULL,    
  `active` int(1) default 1,  
  PRIMARY KEY (`id_poseur_prestation`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
