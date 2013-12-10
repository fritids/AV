CREATE OR REPLACE 
ALGORITHM = UNDEFINED
VIEW  `mv_orders` AS 
SELECT a.* , b.ARC_INFO, b.RECU_INFO
FROM  `av_orders` a, mv_orders_pivot_arc_recu b 
WHERE   a.id_order = b.id_order
and `current_state` <>  '';


CREATE OR REPLACE 
ALGORITHM = UNDEFINED
VIEW  `mv_orders_pivot_arc_recu` AS 
select id_order, if(nb_product_arc > 0 , 
	if(nb_product_arc = nb_product, 'ARC Reçu Totalement', 'ARC Partiellement Reçu'),
	'Pas d''ARC') ARC_INFO ,  
 if(nb_product_recu > 0 ,
	if(nb_product_recu = nb_product, 'Recu Totalement', 'Reçu partiellement'),
	'Rien reçu')
	RECU_INFO
from mv_orders_nb_arc_recu c;


CREATE OR REPLACE 
ALGORITHM = UNDEFINED
VIEW  `mv_orders_nb_arc_recu` AS 
SELECT `id_order`, 
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order`) nb_product,
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order` and product_current_state = 17 ) nb_product_arc,
(select count(1) from av_order_detail a where a.`id_order`=b.`id_order` and product_current_state = 18 ) nb_product_recu
FROM `av_orders` b
group by `id_order`