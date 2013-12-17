ALTER TABLE  `av_tournee` ADD INDEX (  `id_order_detail` );
ALTER TABLE  `av_tournee` ADD INDEX (  `id_truck` ,  `date_livraison` ) ;
ALTER TABLE  `av_tournee` DROP INDEX  `id_order_detail` ,ADD UNIQUE  `id_order_detail` (  `id_order_detail` )