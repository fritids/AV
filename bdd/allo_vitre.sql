--
-- Structure de la table `av_address`
--

CREATE TABLE IF NOT EXISTS `av_address` (
  `id_address` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NOT NULL DEFAULT '0',
  `alias` varchar(32) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `postcode` varchar(12) DEFAULT NULL,
  `country` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_address`),
  KEY `id_customer` (`id_customer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `av_category`
--

CREATE TABLE IF NOT EXISTS `av_category` (
  `id_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `description` text,
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `av_customer`
--

CREATE TABLE IF NOT EXISTS `av_customer` (
  `id_customer` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_gender` int(10) unsigned NOT NULL,
  `company` varchar(64) DEFAULT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `phone_mobile` varchar(32) DEFAULT NULL,
  `secure_key` varchar(32) NOT NULL DEFAULT '-1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_customer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `av_employee`
--

CREATE TABLE IF NOT EXISTS `av_employee` (
  `id_employee` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_profile` int(10) unsigned NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `last_passwd_gen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `av_orders`
--

CREATE TABLE IF NOT EXISTS `av_orders` (
  `id_order` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(9) DEFAULT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `id_address_delivery` int(10) unsigned NOT NULL,
  `id_address_invoice` int(10) unsigned NOT NULL,
  `current_state` int(10) unsigned NOT NULL,
  `total_paid` decimal(17,2) NOT NULL DEFAULT '0.00',
  `invoice_date` datetime NOT NULL,
  `delivery_date` datetime NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_order`),
  KEY `id_customer` (`id_customer`),
  KEY `id_address_delivery` (`id_address_delivery`),
  KEY `id_address_invoice` (`id_address_invoice`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Structure de la table `av_order_detail`
--

CREATE TABLE IF NOT EXISTS `av_order_detail` (
  `id_order_detail` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_attribute_id` int(10) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_quantity` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `product_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `product_shipping` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `attribute_name` varchar(255) NOT NULL,
  `attribute_quantity` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `attribute_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `attribute_shipping` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_price_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_price_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_price_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_price_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`id_order_detail`),
  KEY `id_order` (`id_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `av_product`
--

CREATE TABLE IF NOT EXISTS `av_product` (
  `id_product` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` int(10) unsigned NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_price_ratio` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `reference` varchar(32) DEFAULT NULL,
  `width` decimal(20,6) DEFAULT '0.000000',
  `height` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `depth` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `description_short` text,
  `min_width` int(11) NOT NULL,
  `min_height` int(11) NOT NULL,
  `max_surface` int(11) NOT NULL,
  `max_width` int(11) NOT NULL,
  `max_height` int(11) NOT NULL,
  PRIMARY KEY (`id_product`),
  KEY `id_category` (`id_category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `av_product_attribute`
--

CREATE TABLE IF NOT EXISTS `av_product_attribute` (
  `id_product_attribute` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_price_impact` decimal(17,2) NOT NULL DEFAULT '0.00',
  `default_on` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_product_attribute`),
  KEY `id_product` (`id_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `av_product_caract`
--

CREATE TABLE IF NOT EXISTS `av_product_caract` (
  `id_product_caract` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `caract_name` varchar(120) NOT NULL,
  `caract_value` varchar(210) NOT NULL,
  PRIMARY KEY (`id_product_caract`),
  KEY `id_product` (`id_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `av_range_weight`
--

CREATE TABLE IF NOT EXISTS `av_range_weight` (
  `id_range_weight` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delimiter1` decimal(20,6) NOT NULL,
  `delimiter2` decimal(20,6) NOT NULL,
  `delivery_ratio` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id_range_weight`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `av_order_status` (
  `id_statut` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id_statut`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `av_product_images` (
  `id_image` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `cover` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(250) NOT NULL,
  PRIMARY KEY (`id_image`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Contenu de la table `av_range_weight`
--

--
-- Contraintes pour la table `av_address`
--
ALTER TABLE `av_address`
  ADD CONSTRAINT `av_address_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `av_address` (`id_customer`);

--
-- Contraintes pour la table `av_orders`
--
ALTER TABLE `av_orders`
  ADD CONSTRAINT `av_orders_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `av_customer` (`id_customer`),
  ADD CONSTRAINT `av_orders_ibfk_2` FOREIGN KEY (`id_address_delivery`) REFERENCES `av_address` (`id_address`),
  ADD CONSTRAINT `av_orders_ibfk_3` FOREIGN KEY (`id_address_invoice`) REFERENCES `av_address` (`id_address`);

--
-- Contraintes pour la table `av_order_detail`
--
ALTER TABLE `av_order_detail`
  ADD CONSTRAINT `av_order_detail_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `av_orders` (`id_order`);

--
-- Contraintes pour la table `av_product`
--
ALTER TABLE `av_product`
  ADD CONSTRAINT `av_product_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `av_category` (`id_category`);

--
-- Contraintes pour la table `av_product_attribute`
--
ALTER TABLE `av_product_attribute`
  ADD CONSTRAINT `av_product_attribute_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `av_product` (`id_product`);

--
-- Contraintes pour la table `av_product_caract`
--
ALTER TABLE `av_product_caract`
  ADD CONSTRAINT `av_product_caract_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `av_product` (`id_product`);
