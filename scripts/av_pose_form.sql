
CREATE TABLE IF NOT EXISTS `av_product_pose_form` (
  `id_product_pose_form` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) DEFAULT NULL,
  `id_pose_form` int(10) DEFAULT NULL,  
  PRIMARY KEY (`id_product_pose_form`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `av_pose_form` (
  `id_pose_form` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pose_form_parent` int(10) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `title` varchar(128) NOT NULL,
  `answer` varchar(128) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `active` int(1) DEFAULT '1',
  `position` int(2),
  PRIMARY KEY (`id_pose_form`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `av_order_product_pose` (
  `id_order_product_pose` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) NOT NULL,
  `id_order_detail` int(10) NOT NULL,  
  `id_product` int(10) NOT NULL,
  `id_pose_question` int(10) NOT NULL,  
  `id_pose_anwser` int(10) NOT NULL,    
  `area` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `vat_rate` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_order_product_pose`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Contenu de la table `av_pose_form`
--

INSERT INTO `av_pose_form` (`id_pose_form`, `id_pose_form_parent`, `name`, `title`, `answer`, `price`, `active`) VALUES
(1, NULL, 'presta_q1', 'Habitez-vous :', '', '0.00', 1),
(2, 1, '', '', 'En maison', '0.00', 1),
(3, 1, '', '', 'En appartement', '0.00', 1),
(4, NULL, 'presta_q2', 'L''intervention doit avoir lieu :', '', '0.00', 1),
(5, 4, '', '', 'En étage', '0.00', 1),
(6, 4, '', '', 'En rez de chaussée', '0.00', 1);
