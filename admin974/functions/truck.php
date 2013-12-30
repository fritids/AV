<?php

function getTruckLoad($id) {
    global $db;

    $r = $db->rawQuery("select a.id_truck, date_livraison, capacity ,
                    if (sum(a.nb_product_delivered * product_weight / capacity * 100) >100 , 100 , sum(a.nb_product_delivered * product_weight / capacity * 100)) truck_weight_load, 
                    capacity - sum(a.nb_product_delivered * product_weight) poids_restant, 
                    sum(a.nb_product_delivered * product_weight) tot_weight,
                    count(1)
                    from av_tournee a,  av_order_detail b, av_truck c
                    where a.id_order_detail = b.id_order_detail
                    and a.id_truck = c.id_truck
                    and a.id_truck = ?
                    and a.date_livraison = ?
                    group by id_truck
                    ", $id);
    if ($r)
        return ($r[0]);
}
function getProductTruck($id_order_detail, $date_delivery) {
    global $db;

    $p = $db->where("id_order_detail", $id_order_detail)
            //->where("date_livraison", $date_delivery)
            ->get("av_tournee");

    if ($p)
        return ($p[0]);
}
?>
