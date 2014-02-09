
CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW  `mv_order_refund` AS 
SELECT a.*, 
	round((total_shipping)/(1+vat_rate/100),2) frais_de_port_ht,  
	total_refund - (round((total_shipping)/(1+vat_rate/100),2)) - (total_refund - round((total_refund/(1+vat_rate/100)),2)) total_ht, 
	total_refund - round((total_refund/(1+vat_rate/100)),2) montant_tva , 
	if(vat_rate = 19.6 ,total_refund - round((total_refund/(1+vat_rate/100)),2),0) montant_tva_196 , 
	if(vat_rate = 20 ,total_refund - round((total_refund/(1+vat_rate/100)),2),0) montant_tva_20 , 
	b.firstname, b.lastname
FROM  `av_order_refund` a, av_customer b
WHERE   a.id_customer= b.id_customer;