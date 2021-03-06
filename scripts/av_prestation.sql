CREATE TABLE IF NOT EXISTS `av_partner` (
  `id_partner` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  active int(1) default 1,  
  PRIMARY KEY (`id_partner`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_partner_zone` (
  `id_partner_zone` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_zone` int(10) NOT NULL,
  `id_partner` int(10) NOT NULL,  
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  PRIMARY KEY (`id_partner`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_partner_address` (
  `id_partner_address` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_partner` int(10) NOT NULL,
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
  active int(1) default 1,  
  PRIMARY KEY (`id_partner`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_prestation` (
  `id_prestation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  `category` int(1) NOT NULL,  
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  active int(1) default 1,  
  PRIMARY KEY (`id_prestation`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `av_partner_prestation` (
  `id_partner_prestation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_partner` int(10) NOT NULL,
  `id_prestation` int(10) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,  
  `date_stda` datetime NOT NULL,
  `date_enda` datetime NOT NULL,
  `price_ht` decimal(10,2) NOT NULL,  
  `applicable_vat` decimal(5,2) NOT NULL,    
  active int(1) default 1,  
  PRIMARY KEY (`id_partner_prestation`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
