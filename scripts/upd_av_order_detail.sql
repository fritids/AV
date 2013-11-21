update `av_order_detail` set`date_upd` ='2013-11-15 18:00:00' where `product_current_state` = 19 ;

update `av_truck_planning` set`date_upd` ='2013-11-15 18:00:00' where `product_current_state` = 19 


ALTER TABLE  `av_order_bdc` CHANGE  `category`  `category` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
CHANGE  `supplier_name`  `supplier_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
CHANGE  `bdc_filename`  `bdc_filename` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;

ALTER TABLE  `av_order_bdc` CHANGE  `id_order_detail`  `id_order_detail` INT( 11 ) NULL ;

select * 
from av_order 
where id_order


SELECT distinct id_order FROM `av_order_detail` WHERE `product_current_state` = 19
and date(`date_upd`) = '2013-11-15'


select distinct a.id_order, a.date_livraison, horaire, comment1, comment2, comment3  ,firstname , lastname, email
from av_tournee a, av_order_detail b, av_orders c, av_customer d
where a.id_order_detail = b.id_order_detail
and a.id_order = c.id_order
and c.id_customer = d.id_customer
and date(b.date_upd ) = '2013-11-15'