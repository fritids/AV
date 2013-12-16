ALTER TABLE  `av_tournee` ADD INDEX (  `id_order_detail` );
ALTER TABLE  `av_tournee` ADD INDEX (  `id_truck` ,  `date_livraison` ) ;