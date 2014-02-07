drop table av_accounting_summary;
drop table av_accounting_entries;
drop table av_accounting_output;

CREATE TABLE IF NOT EXISTS `av_accounting_entries` (
  `id_actg_entries` int(11) NOT NULL AUTO_INCREMENT,
  `batch_name` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sens` varchar(1) NOT NULL,
  `calculation` varchar(50) NOT NULL,  
  `account` varchar(50) NULL,
  PRIMARY KEY (`id_actg_entries`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `av_accounting_entries` (`batch_name`, `name`, `sens`, `calculation`, `account`) VALUES
('VENTES', 'VENTE TTC', 'D', 'total_paid', null ),
('VENTES', 'TVA 16.6%', 'C', 'montant_tva_196', '44571100'),
('VENTES', 'TVA 20%', 'C', 'montant_tva_20', '44571400'),
('VENTES', 'FDP HT', 'C', 'frais_de_port_ht', '70850000'),
('VENTES', 'VENTE HT', 'C', 'total_ht', '70110000'),
('REMBOURSEMENTS', 'REMBOURSEMENT TTC', 'C', 'total_refund', null ),
('REMBOURSEMENTS', 'TVA 16.6%', 'D', 'montant_tva_196', '44571100'),
('REMBOURSEMENTS', 'TVA 20%', 'D', 'montant_tva_20', '44571400'),
('REMBOURSEMENTS', 'FDP HT', 'D', 'frais_de_port_ht', '70850000'),
('REMBOURSEMENTS', 'VENTE HT', 'D', 'total_ht', '70110000')
;


CREATE TABLE IF NOT EXISTS `av_accounting_output` (
  `id_actg_output` int(11) NOT NULL AUTO_INCREMENT,
  `batch_name` varchar(50) NOT NULL,
  `batch_no` int NOT NULL,
  `date_add` datetime NOT NULL,
  `entry_name` varchar(50) NOT NULL,
  `entry_calculation` varchar(50) NOT NULL,    
  `output` text NOT NULL,
  PRIMARY KEY (`id_actg_output`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `av_accounting_summary` (
  `id_actg_summary` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,  
  `date_add` datetime NOT NULL,
  `batch_name` varchar(50) NOT NULL,
  `batch_no` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,  
  PRIMARY KEY (`id_actg_summary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

