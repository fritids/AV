ALTER TABLE  `av_product` CHANGE  `max_surface`  `max_surface_facture` DECIMAL ( 5, 2 ) NOT NULL;

ALTER TABLE  `av_product` ADD  `min_surface_facture` DECIMAL( 5, 2 ) NOT NULL AFTER  `min_height`;

ALTER TABLE  `av_product` CHANGE  `min_surface_facture`  `min_area_invoiced` DECIMAL( 5, 2 ) NOT NULL;

ALTER TABLE  `av_product` CHANGE  `max_surface_facture`  `max_area_invoiced` DECIMAL( 5, 2 ) NOT NULL;


update av_product set `min_area_invoiced` = 0.35, `max_area_invoiced` =3.5;
update av_product set `min_area_invoiced` = 0.50, `max_area_invoiced` =3.5 and id_category= 12;

update av_supplier set email = 'stephane.alamichel@gmail.com';



CREATE TABLE `av_message` (
  `id_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned DEFAULT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `private` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_message`)  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

--

SELECT concat("'update av_product set`link_rewrite` ='",`link_rewrite`,"',`meta_description`='",`meta_description`,"',`meta_keywords` ='",`meta_keywords`,"', `meta_title` ='",`meta_title`,"' where `id_cms` = ",`id_cms`, ";") qsql
FROM  `ps_product_lang` 
where id_lang=2;

SELECT concat("'update av_cms_lang set`link_rewrite` ='",`link_rewrite`,"where `id_cms` = ",`id_cms`, ";") qsql
FROM  `ps_cms_lang` 
where id_lang=2;

SELECT concat("'update av_category set`link_rewrite` ='",`link_rewrite`,"',`meta_description`='",`meta_description`,"',`meta_keywords` ='",`meta_keywords`,"', `meta_title` ='",`meta_title`,"' where `id_category` = ",`id_category`, ";") qsql
FROM  `ps_category_lang` 
where id_lang=2;

INSERT INTO `av_attributes` (`id_attribute`, `name`) VALUES (NULL, 'Traitement du verre');

INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '58', '2', 'Coupe Brute', '0.00', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '58', '2', 'biseaux 10 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '58', '2', 'Biseaux 25 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '58', '2', 'Joints polis autour', '16.74', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '58', '7', 'Sans film anti éclat', '0','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '58', '7', 'Avec film anti éclat', '23.92', '0.00', '0.00', '0');


INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '59', '2', 'Coupe Brute', '0.00', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '59', '2', 'biseaux 10 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '59', '2', 'Biseaux 25 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '59', '2', 'Joints polis autour', '16.74', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '59', '7', 'Sans film anti éclat', '0','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '59', '7', 'Avec film anti éclat', '23.92', '0.00', '0.00', '0');

INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '60', '2', 'Coupe Brute', '0.00', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '60', '2', 'biseaux 10 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '60', '2', 'Biseaux 25 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '60', '2', 'Joints polis autour', '16.74', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '60', '7', 'Sans film anti éclat', '0','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '60', '7', 'Avec film anti éclat', '23.92', '0.00', '0.00', '0');

INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '61', '2', 'Coupe Brute', '0.00', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '61', '2', 'biseaux 10 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '61', '2', 'Biseaux 25 mm et Bords Polis', '59.8','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '61', '2', 'Joints polis autour', '16.74', '0.00', '0.00', '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '61', '7', 'Sans film anti éclat', '0','0.00', '0.00',  '0');
INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `id_attribute`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES (NULL, '61', '7', 'Avec film anti éclat', '23.92', '0.00', '0.00', '0');




