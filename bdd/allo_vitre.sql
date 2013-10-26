________________________________________________________________________________________________________________________________________________________________________________________________vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv________________________________________________________________________________________________vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv--
-- Structure de la table `av_address`
--

CREATE TABLE IF NOT EXISTS `admin_user` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(320) CHARACTER SET ascii NOT NULL,
  `mdp` char(32) CHARACTER SET ascii NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  UNIQUE KEY `id_admin` (`id_admin`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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
  `id_product` int(10) unsigned NOT NULL,
  `product_attribute_id` int(10) unsigned NOT NULL,
  `product_current_state` int(10) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_quantity` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `product_price` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `product_shipping` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `attribute_name` varchar(255) NOT NULL,
  `attribute_quantity` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `attribute_price` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `attribute_shipping` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `total_price_tax_incl` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `total_price_tax_excl` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `unit_price_tax_incl` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `unit_price_tax_excl` decimal(20,2) NOT NULL DEFAULT '0.000000',
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
  `price` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `unit_price_ratio` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `reference` varchar(32) DEFAULT NULL,
  `width` decimal(20,2) DEFAULT '0.000000',
  `height` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `depth` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `weight` decimal(20,2) NOT NULL DEFAULT '0.000000',
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


ALTER TABLE  `av_product` ADD  `video` VARCHAR( 255 ) NULL


--
-- Structure de la table `av_product_attribute`
--

CREATE TABLE IF NOT EXISTS `av_product_attribute` (
  `id_product_attribute` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `price` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `weight` decimal(20,2) NOT NULL DEFAULT '0.000000',
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
  `delimiter1` decimal(20,2) NOT NULL,
  `delimiter2` decimal(20,2) NOT NULL,
  `delivery_ratio` decimal(20,2) NOT NULL,
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



CREATE TABLE IF NOT EXISTS `av_product_images` (
  `id_image` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `cover` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(250) NOT NULL,
  PRIMARY KEY (`id_image`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `av_cms_lang` (
  `id_cms` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meta_title` varchar(128) NOT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `content` longtext , 
  PRIMARY KEY (`id_cms`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `av_truck` (
  `id_truck` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `imma` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_actif` int(1) NOT NULL DEFAULT '1',
  `capacity` int(10) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id_truck`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `av_truck_planning` (
  `id_planning_truck` int(11) NOT NULL AUTO_INCREMENT,
  `id_truck` int(11) unsigned NOT NULL,
  `delivery_date` date NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id_planning_truck`),
  UNIQUE KEY `id_truck` (`id_truck`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `av_tournee` (
  `id_tournee` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_truck` int(11) unsigned NOT NULL,
  `id_order_detail` int(11) unsigned NOT NULL,
  `status` int(11) NOT NULL,  
  `date_livraison` date NOT NULL,
  `horaire_livraison` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tournee`),
  UNIQUE KEY `id_truck` ( `id_order_detail`),
  KEY `	id_order_detail` (`id_order_detail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `av_zone` (
  `id_zone` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_zone`)
);

CREATE TABLE `av_regions` (
  `id_region` varchar(2) NOT NULL,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_region`)
);


ALTER TABLE  `av_tournee` ADD  `comment1` VARCHAR( 255 ) NOT NULL ,
ADD  `comment2` VARCHAR( 255 ) NOT NULL ,
ADD  `comment3` VARCHAR( 255 ) NOT NULL ,
ADD  `nb_product_delivered` INT NOT NULL


CREATE TABLE IF NOT EXISTS `av_supplier` (
  `id_supplier` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `av_regions` VALUES ('1', 'Alsace');
INSERT INTO `av_regions` VALUES ('2', 'Aquitaine');
INSERT INTO `av_regions` VALUES ('3', 'Auvergne');
INSERT INTO `av_regions` VALUES ('4', 'Basse Normandie');
INSERT INTO `av_regions` VALUES ('5', 'Bourgogne');
INSERT INTO `av_regions` VALUES ('6', 'Bretagne');
INSERT INTO `av_regions` VALUES ('7', 'Centre');
INSERT INTO `av_regions` VALUES ('8', 'Champagne Ardenne');
INSERT INTO `av_regions` VALUES ('9', 'Corse');
INSERT INTO `av_regions` VALUES ('10', 'Franche Comte');
INSERT INTO `av_regions` VALUES ('11', 'Haute Normandie');
INSERT INTO `av_regions` VALUES ('12', 'Ile de France');
INSERT INTO `av_regions` VALUES ('13', 'Languedoc Roussillon');
INSERT INTO `av_regions` VALUES ('14', 'Limousin');
INSERT INTO `av_regions` VALUES ('15', 'Lorraine');
INSERT INTO `av_regions` VALUES ('16', 'Midi-Pyrénées');
INSERT INTO `av_regions` VALUES ('17', 'Nord Pas de Calais');
INSERT INTO `av_regions` VALUES ('18', 'Provence Alpes Côte d\'Azur');
INSERT INTO `av_regions` VALUES ('19', 'Pays de la Loire');
INSERT INTO `av_regions` VALUES ('20', 'Picardie');
INSERT INTO `av_regions` VALUES ('21', 'Poitou Charente');
INSERT INTO `av_regions` VALUES ('22', 'Rhone Alpes');


CREATE TABLE `av_departements` (
  `id_departement` varchar(2) NOT NULL,
  `id_region` varchar(2) NOT NULL,
  `id_zone` int(10) UNSIGNED,
  `nom` char(32) NOT NULL,
  PRIMARY KEY  (`id_departement`),
  KEY `FK_DEPARTEMENT_REGION` (`id_region`)
);


INSERT INTO `av_departements` VALUES ('1', '22', 'Ain');
INSERT INTO `av_departements` VALUES ('2', '20', 'Aisne');
INSERT INTO `av_departements` VALUES ('3', '3', 'Allier');
INSERT INTO `av_departements` VALUES ('4', '18', 'Alpes de haute provence');
INSERT INTO `av_departements` VALUES ('5', '18', 'Hautes alpes');
INSERT INTO `av_departements` VALUES ('6', '18', 'Alpes maritimes');
INSERT INTO `av_departements` VALUES ('7', '22', 'Ardèche');
INSERT INTO `av_departements` VALUES ('8', '8', 'Ardennes');
INSERT INTO `av_departements` VALUES ('9', '16', 'Ariège');
INSERT INTO `av_departements` VALUES ('10', '8', 'Aube');
INSERT INTO `av_departements` VALUES ('11', '13', 'Aude');
INSERT INTO `av_departements` VALUES ('12', '16', 'Aveyron');
INSERT INTO `av_departements` VALUES ('13', '18', 'Bouches du rhône');
INSERT INTO `av_departements` VALUES ('14', '4', 'Calvados');
INSERT INTO `av_departements` VALUES ('15', '3', 'Cantal');
INSERT INTO `av_departements` VALUES ('16', '21', 'Charente');
INSERT INTO `av_departements` VALUES ('17', '21', 'Charente maritime');
INSERT INTO `av_departements` VALUES ('18', '7', 'Cher');
INSERT INTO `av_departements` VALUES ('19', '14', 'Corrèze');
INSERT INTO `av_departements` VALUES ('21', '5', 'Côte d\'or');
INSERT INTO `av_departements` VALUES ('22', '6', 'Côtes d\'Armor');
INSERT INTO `av_departements` VALUES ('23', '14', 'Creuse');
INSERT INTO `av_departements` VALUES ('24', '2', 'Dordogne');
INSERT INTO `av_departements` VALUES ('25', '10', 'Doubs');
INSERT INTO `av_departements` VALUES ('26', '22', 'Drôme');
INSERT INTO `av_departements` VALUES ('27', '11', 'Eure');
INSERT INTO `av_departements` VALUES ('28', '7', 'Eure et Loir');
INSERT INTO `av_departements` VALUES ('29', '6', 'Finistère');
INSERT INTO `av_departements` VALUES ('30', '13', 'Gard');
INSERT INTO `av_departements` VALUES ('31', '16', 'Haute garonne');
INSERT INTO `av_departements` VALUES ('32', '16', 'Gers');
INSERT INTO `av_departements` VALUES ('33', '2', 'Gironde');
INSERT INTO `av_departements` VALUES ('34', '13', 'Hérault');
INSERT INTO `av_departements` VALUES ('35', '6', 'Ile et Vilaine');
INSERT INTO `av_departements` VALUES ('36', '7', 'Indre');
INSERT INTO `av_departements` VALUES ('37', '7', 'Indre et Loire');
INSERT INTO `av_departements` VALUES ('38', '22', 'Isère');
INSERT INTO `av_departements` VALUES ('39', '10', 'Jura');
INSERT INTO `av_departements` VALUES ('40', '2', 'Landes');
INSERT INTO `av_departements` VALUES ('41', '7', 'Loir et Cher');
INSERT INTO `av_departements` VALUES ('42', '22', 'Loire');
INSERT INTO `av_departements` VALUES ('43', '3', 'Haute loire');
INSERT INTO `av_departements` VALUES ('44', '19', 'Loire Atlantique');
INSERT INTO `av_departements` VALUES ('45', '7', 'Loiret');
INSERT INTO `av_departements` VALUES ('46', '16', 'Lot');
INSERT INTO `av_departements` VALUES ('47', '2', 'Lot et Garonne');
INSERT INTO `av_departements` VALUES ('48', '13', 'Lozère');
INSERT INTO `av_departements` VALUES ('49', '19', 'Maine et Loire');
INSERT INTO `av_departements` VALUES ('50', '4', 'Manche');
INSERT INTO `av_departements` VALUES ('51', '8', 'Marne');
INSERT INTO `av_departements` VALUES ('52', '8', 'Haute Marne');
INSERT INTO `av_departements` VALUES ('53', '19', 'Mayenne');
INSERT INTO `av_departements` VALUES ('54', '15', 'Meurthe et Moselle');
INSERT INTO `av_departements` VALUES ('55', '15', 'Meuse');
INSERT INTO `av_departements` VALUES ('56', '6', 'Morbihan');
INSERT INTO `av_departements` VALUES ('57', '15', 'Moselle');
INSERT INTO `av_departements` VALUES ('58', '5', 'Nièvre');
INSERT INTO `av_departements` VALUES ('59', '17', 'Nord');
INSERT INTO `av_departements` VALUES ('60', '20', 'Oise');
INSERT INTO `av_departements` VALUES ('61', '4', 'Orne');
INSERT INTO `av_departements` VALUES ('62', '17', 'Pas de Calais');
INSERT INTO `av_departements` VALUES ('63', '3', 'Puy de Dôme');
INSERT INTO `av_departements` VALUES ('64', '2', 'Pyrénées Atlantiques');
INSERT INTO `av_departements` VALUES ('65', '16', 'Hautes Pyrénées');
INSERT INTO `av_departements` VALUES ('66', '13', 'Pyrénées Orientales');
INSERT INTO `av_departements` VALUES ('67', '1', 'Bas Rhin');
INSERT INTO `av_departements` VALUES ('68', '1', 'Haut Rhin');
INSERT INTO `av_departements` VALUES ('69', '22', 'Rhône');
INSERT INTO `av_departements` VALUES ('70', '10', 'Haute Saône');
INSERT INTO `av_departements` VALUES ('71', '5', 'Saône et Loire');
INSERT INTO `av_departements` VALUES ('72', '19', 'Sarthe');
INSERT INTO `av_departements` VALUES ('73', '22', 'Savoie');
INSERT INTO `av_departements` VALUES ('74', '22', 'Haute Savoie');
INSERT INTO `av_departements` VALUES ('75', '12', 'Paris');
INSERT INTO `av_departements` VALUES ('76', '11', 'Seine Maritime');
INSERT INTO `av_departements` VALUES ('77', '12', 'Seine et Marne');
INSERT INTO `av_departements` VALUES ('78', '12', 'Yvelines');
INSERT INTO `av_departements` VALUES ('79', '21', 'Deux Sèvres');
INSERT INTO `av_departements` VALUES ('80', '20', 'Somme');
INSERT INTO `av_departements` VALUES ('81', '16', 'Tarn');
INSERT INTO `av_departements` VALUES ('82', '16', 'Tarn et Garonne');
INSERT INTO `av_departements` VALUES ('83', '18', 'Var');
INSERT INTO `av_departements` VALUES ('84', '18', 'Vaucluse');
INSERT INTO `av_departements` VALUES ('85', '19', 'Vendée');
INSERT INTO `av_departements` VALUES ('86', '21', 'Vienne');
INSERT INTO `av_departements` VALUES ('87', '14', 'Haute Vienne');
INSERT INTO `av_departements` VALUES ('88', '15', 'Vosge');
INSERT INTO `av_departements` VALUES ('89', '5', 'Yonne');
INSERT INTO `av_departements` VALUES ('90', '10', 'Territoire de Belfort');
INSERT INTO `av_departements` VALUES ('91', '12', 'Essonne');
INSERT INTO `av_departements` VALUES ('92', '12', 'Haut de seine');
INSERT INTO `av_departements` VALUES ('93', '12', 'Seine Saint Denis');
INSERT INTO `av_departements` VALUES ('94', '12', 'Val de Marne');
INSERT INTO `av_departements` VALUES ('95', '12', 'Val d\'Oise');
INSERT INTO `av_departements` VALUES ('2a', '9', 'Corse du Sud');
INSERT INTO `av_departements` VALUES ('2b', '9', 'Haute Corse');

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

ALTER TABLE  `av_order_detail` ADD  `product_width` INT NULL AFTER  `produit_surface` ,
ADD  `product_height` INT NULL AFTER  `product_width` ,
ADD  `product_depth` INT NULL AFTER  `product_height`

ALTER TABLE  `av_order_detail` ADD  `product_weight` INT NULL AFTER  `product_depth`

ALTER TABLE  `av_order_detail` CHANGE  `product_quantity`  `product_quantity` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0.00'

ALTER TABLE  `av_order_detail` ADD  `id_supplier` INT UNSIGNED NOT NULL AFTER  `id_order`

ALTER TABLE  `av_order_detail` ADD  `supplier_date_delivery` DATE NOT NULL ,
ADD  `nb_product_delivered` INT NOT NULL

