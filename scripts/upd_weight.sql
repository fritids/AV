ALTER TABLE  `av_order_product_attributes` ADD  `weight` INT NOT NULL;
ALTER TABLE  `av_devis_product_attributes` ADD  `weight` INT NOT NULL;


SELECT a.`id_devis_detail` , `product_quantity`,`product_width`, `product_height` , b.weight FROM `av_devis_detail` a , av_product b
WHERE a.`id_product` = b.`id_product`
and product_width != ''

update `av_devis_detail` a join av_product b  on a.`id_product` = b.`id_product`
set a.`product_weight` = `product_quantity` * (`product_width` * `product_height`) / 1000000 * b.weight
where a.product_width != ''

update `av_order_detail` a join av_product b  on a.`id_product` = b.`id_product`
set a.`product_weight` = `product_quantity` * (`product_width` * `product_height`) / 1000000 * b.weight
where a.product_width != ''

update `av_order_product_attributes` a 
join av_product_attribute b
   on a.`id_attribute` = b.`id_product_attribute` 
set a.`weight` = b.weight;

update `av_devis_product_attributes` a 
join av_product_attribute b
   on a.`id_attribute` = b.`id_product_attribute` 
set a.`weight` = b.weight;

update av_order_detail set product_weight =0;


-1
update `av_order_detail` a 
join av_product c  
on a.`id_product` = c.`id_product`
set a.`product_weight` = (`product_width` * `product_height`) / 1000000 * c.weight
where c.weight != 0
and ;


-2
update `av_order_detail` a 
join av_order_product_attributes b  
on a.`id_order_detail` = b.`id_order_detail`
set a.`product_weight` = product_weight + (`product_quantity` * (`product_width` * `product_height`) / 1000000 * b.weight)
where b.weight != 0;

update `av_order_detail` a 
join av_order_product_attributes b  
on a.`id_order_detail` = b.`id_order_detail`
set a.`product_weight` = `product_quantity` * (`product_width` * `product_height`) / 1000000 * (a.product_weight+b.weight)
where b.weight != 0;


update `av_order_detail` a 
set product_weight = product_weight / `product_quantity`
where product_weight != 0;

4276
select *
from av_order_detail where product_weight =0 where id_product >0;

update av_order_detail set product_weight =0 where product_weight !=0 and id_product >0;