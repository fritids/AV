
CREATE TABLE IF NOT EXISTS `av_newsletter` (
  `id_newsletter` int(11) NOT NULL AUTO_INCREMENT,
  `date_add` datetime NOT NULL,
  `email` varchar(128) NOT NULL,
  PRIMARY KEY (`id_newsletter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;