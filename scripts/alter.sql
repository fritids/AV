ALTER TABLE  `av_product` CHANGE  `max_surface`  `max_surface_facture` DECIMAL ( 5, 2 ) NOT NULL;

ALTER TABLE  `av_product` ADD  `min_surface_facture` DECIMAL( 5, 2 ) NOT NULL AFTER  `min_height`;

ALTER TABLE  `av_product` CHANGE  `min_surface_facture`  `min_area_invoiced` DECIMAL( 5, 2 ) NOT NULL;

ALTER TABLE  `av_product` CHANGE  `max_surface_facture`  `max_area_invoiced` DECIMAL( 5, 2 ) NOT NULL;


update av_product set `min_area_invoiced` = 0.35, `max_area_invoiced` =3.5;
update av_product set `min_area_invoiced` = 0.50, `max_area_invoiced` =3.5 and id_category= 12;