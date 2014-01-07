ALTER TABLE  `av_zone` DROP  `warehouse` ;
ALTER TABLE  `av_zone` ADD  `warehouse` VARCHAR( 255 ) NULL;


update av_zone set warehouse = 'SAS ALLOVITRES<br>1900 Avenue Paul Julien RN7<br>13100 Le Tholonet<br>email : contact@miroiteriedupaysdaix.com<br>SARL Miroiterie du Pays d\'Aix - RC5522928845' ;

ALTER TABLE  `av_departements` ADD INDEX (  `nom` );



CREATE TABLE IF NOT EXISTS `av_supplier_zone` (
  `id_supplier_zone` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) NOT NULL,
  `id_zone` int(11) NOT NULL,
  PRIMARY KEY (`id_supplier_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
