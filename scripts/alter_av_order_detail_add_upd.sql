ALTER TABLE  `av_order_detail` ADD  `date_upd` DATETIME NULL AFTER  `id_product`;
ALTER TABLE  `av_truck_planning` ADD  `date_add` DATETIME NULL AFTER  `id_truck`;
ALTER TABLE  `av_tournee` ADD  `date_upd` DATETIME NULL AFTER  `id_order_detail`;