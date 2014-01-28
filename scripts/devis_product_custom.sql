ALTER TABLE  `av_devis_detail` ADD  `is_product_custom` INT( 1 ) NOT NULL default 0;


CREATE TABLE IF NOT EXISTS `av_devis_product_custom` (
  `id_devis_product_custom` int(11) NOT NULL AUTO_INCREMENT,
  `id_devis` int(11) NOT NULL,
  `id_devis_detail` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_attribute` int(11) NOT NULL,
  `id_attributes_items` int(11) NOT NULL,
  `id_attributes_items_values` int(11) NOT NULL,
  `custom_value` int(11) NOT NULL,
  PRIMARY KEY (`id_devis_product_custom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
