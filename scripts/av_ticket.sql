
CREATE TABLE IF NOT EXISTS `av_ticket` (
  `id_ticket` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `severity` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id_ticket`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;