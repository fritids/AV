
-- --------------------------------------------------------

--
-- Structure de la table `av_attributes`
--

CREATE TABLE IF NOT EXISTS `av_attributes` (
  `id_attribute` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` int(1) NOT NULL,
  `is_duplicable` int(1) NOT NULL,
  PRIMARY KEY (`id_attribute`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `av_attributes`
--

INSERT INTO `av_attributes` (`id_attribute`, `name`, `type`, `is_duplicable`) VALUES
(1, 'Epaisseur', 0, 0),
(2, 'Façonnage', 0, 0),
(3, 'Couleur', 0, 0),
(4, 'Nature du verre', 0, 0),
(5, 'Finition', 0, 0),
(6, 'Forme', 1, 0),
(7, 'Traitement du verre', 0, 0),
(8, 'Perçage', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `av_attributes_items`
--

CREATE TABLE IF NOT EXISTS `av_attributes_items` (
  `id_attributes_items` int(11) NOT NULL AUTO_INCREMENT,
  `id_attribute` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `picture` varchar(128) DEFAULT NULL,
  `min_area_invoiced` decimal(10,2) DEFAULT NULL,
  `max_area_invoiced` decimal(10,2) DEFAULT NULL,
  `price_impact_percentage` decimal(5,2) DEFAULT NULL,
  `price_impact_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_attributes_items`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `av_attributes_items`
--

INSERT INTO `av_attributes_items` (`id_attributes_items`, `id_attribute`, `name`, `picture`, `min_area_invoiced`, `max_area_invoiced`, `price_impact_percentage`, `price_impact_amount`) VALUES
(1, 6, 'triangle', '_01.png', '0.35', '3.50', '1.75', '0.00'),
(2, 6, 'Rectangle biseauté', '_02.png', '0.30', '3.50', '1.50', '0.00'),
(3, 8, 'Perçage simple', NULL, NULL, NULL, NULL, '14.00'),
(4, 8, 'Encoche pour prise', NULL, NULL, NULL, NULL, '30.00');

-- --------------------------------------------------------

--
-- Structure de la table `av_attributes_items_values`
--

CREATE TABLE IF NOT EXISTS `av_attributes_items_values` (
  `id_attributes_items_values` int(11) NOT NULL AUTO_INCREMENT,
  `id_attributes_items` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `min_width` int(11) NOT NULL,
  `max_width` int(11) NOT NULL,
  `picture` varchar(128) DEFAULT NULL,
  `min_area_invoiced` decimal(10,2) DEFAULT NULL,
  `max_area_invoiced` decimal(10,2) DEFAULT NULL,
  `price_impact_percentage` decimal(5,2) DEFAULT NULL,
  `is_width` int(1) DEFAULT '0',
  `is_height` int(1) NOT NULL,
  PRIMARY KEY (`id_attributes_items_values`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `av_attributes_items_values`
--

INSERT INTO `av_attributes_items_values` (`id_attributes_items_values`, `id_attributes_items`, `name`, `min_width`, `max_width`, `picture`, `min_area_invoiced`, `max_area_invoiced`, `price_impact_percentage`, `is_width`, `is_height`) VALUES
(1, 1, 'A', 300, 3500, NULL, NULL, NULL, NULL, 1, 0),
(2, 1, 'B', 300, 3500, NULL, NULL, NULL, NULL, 0, 1),
(3, 2, 'A', 300, 3500, NULL, NULL, NULL, NULL, 1, 0),
(4, 2, 'B', 300, 3500, NULL, NULL, NULL, NULL, 0, 1),
(5, 2, 'C', 300, 3500, NULL, NULL, NULL, NULL, 0, 0),
(6, 3, 'Taille', 0, 0, NULL, NULL, NULL, NULL, 0, 0),
(7, 3, 'Dist X', 0, 0, NULL, NULL, NULL, NULL, 0, 0),
(8, 3, 'Dist.Y', 0, 0, NULL, NULL, NULL, NULL, 0, 0),
(9, 4, 'Hauteur', 0, 0, NULL, NULL, NULL, NULL, 0, 0),
(10, 4, 'Largeur', 0, 0, NULL, NULL, NULL, NULL, 0, 0),
(11, 4, 'Dist.X', 0, 0, NULL, NULL, NULL, NULL, 0, 0),
(12, 4, 'Dist Y', 0, 0, NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `av_product_custom`
--

CREATE TABLE IF NOT EXISTS `av_product_custom` (
  `id_product_custom` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `id_attribute` int(11) NOT NULL,
  PRIMARY KEY (`id_product_custom`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `av_product_custom`
--

INSERT INTO `av_product_custom` (`id_product_custom`, `id_product`, `id_attribute`) VALUES
(1, 49, 8),
(2, 49, 6);


CREATE TABLE IF NOT EXISTS `av_order_product_custom` (
  `id_order_product_custom` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) NOT NULL,
  `id_order_detail` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_attribute` int(11) NOT NULL,
  `id_attributes_items` int(11) NOT NULL,
  `id_attributes_items_values` int(11) NOT NULL,
  `custom_value` int(11) NOT NULL,
  PRIMARY KEY (`id_order_product_custom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


ALTER TABLE  `av_order_detail` ADD  `is_product_custom` INT( 1 ) NOT NULL;

ALTER TABLE  `av_orders` ADD  `nb_product` INT( 3 ) NULL ,
ADD  `nb_custom_product` INT( 3 ) NULL;


ALTER TABLE  `av_attributes_items` ADD  `position` INT( 2 ) NOT NULL AFTER  `id_attribute`;

update av_attributes_items set `position` =`id_attributes_items`
