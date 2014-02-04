drop table mv_orders;
drop table mv_orders_nb_arc_recu;
drop table mv_orders_pivot_arc_recu;
drop table mv_orders_pivot_stock;
drop table mv_orders_stock;
drop table mv_order_detail_stock;

drop view mv_orders_pivot_stock;
drop view mv_orders_stock;
drop view mv_order_detail_stock;

CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_order_detail_stock` AS 
SELECT d.`id_order`, id_order_detail, id_supplier_warehouse, id_product,
(select if(count(1)>0,1,0) from av_order_detail a , av_product c where a.`id_order_detail`=d.`id_order_detail` and a.id_product = c.id_product and c.id_product in (select distinct id_product from av_product_warehouse)) is_product_tracking,
ifnull((select sum(a.product_quantity) from av_order_detail a , av_product c where a.`id_order_detail`=d.`id_order_detail` and a.id_product = c.id_product and c.id_product in (select distinct id_product from av_product_warehouse)),0) quantity_ordered,
0 quantity_available
FROM `av_order_detail` d, `av_orders` b
where d.id_order = b.id_order
group by d.`id_order`, id_order_detail, id_supplier_warehouse, id_product;


CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_orders_stock` AS 
SELECT a.* , d.id_order_detail, d.id_product, d.is_product_tracking, d.quantity_ordered, e.id_warehouse,c.title state_label
FROM  `av_orders` a, av_order_status c , mv_order_detail_stock d
LEFT outer join av_supplier_warehouse e on d.id_supplier_warehouse = e.id_supplier_warehouse
WHERE a.current_state = c.id_statut
and a.id_order = d.id_order
and `current_state` <>  '';
