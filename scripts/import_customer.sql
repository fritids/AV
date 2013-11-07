
insert into av_customer ( `id_customer`, `id_gender`, `firstname`, `lastname`, `email`, `passwd`, `secure_key`, `active`, `date_add`, `date_upd`)
SELECT `id_customer`, `id_gender`, `firstname`, `lastname`, `email`, `passwd`, `secure_key`, `active`, `date_add`, `date_upd` FROM `ps_customer`

insert into av_category (`id_category`, `id_parent`, `active`, `date_add`, `date_upd`, `position`, `name`, `description`)
SELECT a.`id_category`, `id_parent`, `active`, `date_add`, `date_upd`, `position`, `name`, `description`  FROM `ps_category` a, ps_category_lang b WHERE a.id_category = b.id_category and b.id_lang= 2

insert into av_product (`id_product`, `id_category`, `quantity`, `price`, `unit_price_ratio`, `reference`, `width`, `height`, `depth`, `weight`, `active`, `date_add`, `date_upd`, `name`, `description`, `description_short` ,`min_width`, `min_height`)
select  a.`id_product`, `id_category_default`, `quantity`, `price`, `unit_price_ratio`, `reference`, `width`, `height`, `depth`, `weight`, `active`, `date_add`, `date_upd`, `name`, `description`, `description_short`, `width`, `height` FROM `ps_product` a, ps_product_lang b where a.id_product = b.id_product and b.id_lang = 2

insert into av_cms_lang (`id_cms`, `meta_title`, `meta_description`, `meta_keywords`, `content`)
SELECT `id_cms`, `meta_title`, `meta_description`, `meta_keywords`, `content` FROM `ps_cms_lang` 
where id_lang=2

insert into av_address (`id_address`, `id_customer`, `alias`, `address1`, `address2`, `postcode`,country, `city`, `phone`, `phone_mobile`, `date_add`, `date_upd`, `active`)
SELECT `id_address`, `id_customer`, `alias`, `address1`, `address2`, `postcode`, 'FRANCE', `city`, `phone`, `phone_mobile`,`date_add`, `date_upd`, `active` FROM `ps_address` WHERE 1


insert into av_orders (`id_order`, `reference`, `id_customer`, `id_address_delivery`, `id_address_invoice`, `current_state`, `payment`,`total_paid`, `invoice_date`, `delivery_date`, `date_add`, `date_upd`)
SELECT `id_order`, `reference`, `id_customer`, `id_address_delivery`, `id_address_invoice`, `current_state`, `payment`,`total_paid`, `invoice_date`, `delivery_date`, `date_add`, `date_upd` FROM `ps_orders` 
where id_lang= 2


insert into av_order_detail (`id_order_detail`, `id_order`, `id_product`, `product_attribute_id`, `product_name`, `product_quantity`, `product_price`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`)
SELECT `id_order_detail`, `id_order`, `product_id`, `product_attribute_id`, `product_name`, `product_quantity`, `product_price`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl` FROM `ps_order_detail` 


insert into  av_order_status (id_statut,`title`)
SELECT id_order_state, name 
FROM  `ps_order_state_lang` 
WHERE id_lang =2


update `av_address` set alias ='delivery' WHERE lower(alias) like '%livrai%' 
update `av_address` set alias ='invoice' WHERE lower(alias) not like '%livrai%' 

create table av_paypal_order 
SELECT * FROM `ps_paypal_order` WHERE 1

create table av_order_payment
SELECT * FROM `ps_order_payment` WHERE 1


update  `av_order_payment` set `id_order`=`order_reference`

insert into av_product_images (id_image, id_product, cover, filename)
SELECT id_image, id_product, cover, concat(id_product, '-',id_image, '.jpg' ) FROM  ps_image