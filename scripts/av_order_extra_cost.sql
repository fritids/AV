CREATE TABLE IF NOT EXISTS `av_extra_cost` (
  `id_extra_cost` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,    
  `price` decimal(10,2) NOT NULL default 0,    
  `cost_id_tax` int(3) NOT NULL,    
  PRIMARY KEY (`id_extra_cost`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `av_extra_cost` (`id_extra_cost`, `name`, `price`, `cost_id_tax`) 
VALUES (NULL, 'SMS', '0.83', '2'),
	   (NULL, 'Frais de transport', '20.83', '2');


CREATE TABLE IF NOT EXISTS `av_order_extra_cost` (
  `id_order_extra_cost` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) NOT NULL,
  `id_extra_cost` int(10) NOT NULL,  
  `price` decimal(10,2) NOT NULL,    
  `cost_id_tax` int(3) NOT NULL,   
  PRIMARY KEY (`id_order_extra_cost`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

insert into av_order_extra_cost (id_order, id_extra_cost, price,cost_id_tax) 
select id_order, 1, 1/1.196,1 from av_orders where alert_sms = 1 and date(date_add) < '2014-01-01';

insert into av_order_extra_cost (id_order, id_extra_cost, price,cost_id_tax) 
select id_order, 1, 1/1.2,2 from av_orders where alert_sms = 1 and date(date_add) >= '2014-01-01';

insert into av_order_extra_cost (id_order, id_extra_cost, price,cost_id_tax) 
select id_order, 2, 25/1.196,1 from av_orders where date(date_add) < '2014-01-01';

insert into av_order_extra_cost (id_order, id_extra_cost, price,cost_id_tax) 
select id_order, 2, 25/1.2,2 from av_orders where date(date_add) >= '2014-01-01';