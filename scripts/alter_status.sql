ALTER TABLE  `av_order_status` ADD  `id_level` INT NOT NULL AFTER  `id_statut`;


INSERT INTO `av_order_status` (`id_statut`, `id_level`, `title`) VALUES
(15, 1, 'En attente de validation'),
(16, 1, 'Commandé chez le fournisseur'),
(17, 1, 'ARC reçu'),
(18, 1, 'Reçu entrepot'),
(19, 1, 'livraison programmé'),
(20, 1, 'Livré');

ALTER TABLE  `av_supplier` ADD  `emai` VARCHAR( 150 ) NOT NULL;

ALTER TABLE  `av_order_detail` CHANGE  `id_supplier`  `id_supplier` INT( 10 ) UNSIGNED NULL;

update `av_order_detail` set id_supplier = null;