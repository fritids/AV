CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_order_detail_stock` AS 
SELECT d.`id_order`, id_order_detail, id_product,
(select if(count(1)>0,1,0) from av_order_detail a , av_product c where a.`id_order_detail`=d.`id_order_detail` and a.id_product = c.id_product and c.stock_tracking = 1) is_product_tracking,
ifnull((select sum(a.product_quantity) from av_order_detail a , av_product c where a.`id_order_detail`=d.`id_order_detail` and a.id_product = c.id_product and c.stock_tracking = 1),0) quantity_ordered,
ifnull((select sum(c.quantity) from av_order_detail a , av_product c where a.`id_order_detail`=d.`id_order_detail` and a.id_product = c.id_product and c.stock_tracking = 1),0) quantity_available
FROM `av_order_detail` d, `av_orders` b
where d.id_order = b.id_order
group by d.`id_order`, id_order_detail, id_product;

/*
CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_orders_stock` AS 
SELECT `id_order`, current_state,
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order`) nb_product,
(select count(1) from av_order_detail a , av_product c where a.`id_order`=b.`id_order` and a.id_product = c.id_product and c.stock_tracking = 1) is_product_tracking
FROM `av_orders` b
group by `id_order`, `current_state`;

CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_orders_pivot_stock` AS 
select id_order, quantity_ordered, quantity_available,
				if(is_product_tracking > 0, 
					if(is_product_tracking = nb_product,
						5,
						6),
					0) STOCK_TRACKING_INFO,
					if(is_product_tracking > 0, 
						if (quantity_available < 0,
							8,
							if(quantity_ordered = quantity_available,
								5,
								if(quantity_ordered < quantity_available,
									5,
									6)
								)
							),								
						0) STOCK_TRACKING_QTE						
from mv_orders_stock;
*/

CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_orders_stock` AS 
SELECT a.* , d.id_order_detail, d.id_product, d.is_product_tracking, d.quantity_ordered, d.quantity_available ,c.title state_label
FROM  `av_orders` a, av_order_status c , mv_order_detail_stock d
WHERE a.current_state = c.id_statut
and a.id_order = d.id_order
and `current_state` <>  '';
