CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_orders` AS 
SELECT a.* , b.ARC_INFO, b.RECU_INFO, b.COMMANDE_INFO
FROM  `av_orders` a, mv_orders_pivot_arc_recu b 
WHERE   a.id_order = b.id_order
and `current_state` <>  '';


CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_orders_pivot_arc_recu` AS 
select id_order, if(current_state = 2, 
					if(nb_product_arc > 0 , 
						if(nb_product_arc = nb_product,
							5,
							6),
						if(nb_product_commande = nb_product, 
							5, 
							8)
							),
					if(current_state = 3,
						5, 
						0)
					) ARC_INFO ,  
				if(current_state = 2, 
					if(nb_product_commande > 0 ,
						if(nb_product_commande = nb_product, 
							5, 
							6),
						8),
					if(current_state = 3,
						5, 
						0)
					) COMMANDE_INFO,
				if(current_state = 3, 
					if(nb_product_recu > 0 ,
						if(nb_product_recu = nb_product, 5, 6),
						8),
				 0) RECU_INFO				
from mv_orders_nb_arc_recu c;


CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_orders_nb_arc_recu` AS 
SELECT `id_order`, current_state,
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order`) nb_product,
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order` and product_current_state = 17 ) nb_product_arc,
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order` and product_current_state = 18 ) nb_product_recu,
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order` and product_current_state = 16 ) nb_product_commande
FROM `av_orders` b
group by `id_order`, `current_state`