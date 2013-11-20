<?php

function getDevis($did) {
    global $db;
    $r = $db->where("id_devis", $did)
            ->get("av_devis");

    foreach ($r as $k => $devis) {
        $r[$k]["customer"] = getUserDevisCustomer($devis["id_customer"]);
        $r[$k]["details"] = getUserDevisDetail($devis["id_devis"]);        
    }
    return $r;
}

function getUserDevisCustomer($cid) {
    global $db;
    $r = $db->where("id_customer", $cid)
            ->get("av_customer");
    return $r[0];
}

function getUserDevis($cid) {
    global $db;
   

    $r = $db->rawQuery("select a.*, b.nom, b.prenom 
                                from av_devis a , admin_user b 
                                where a.id_user = b.id_admin 
                                and id_customer = ?", array($cid));
            
    foreach ($r as $k => $devis) {
        $r[$k]["details"] = getUserDevisDetail($devis["id_devis"]);
    }
    return $r;
}

function getUserDevisDetail($oid) {
    global $db;
    $params = array($oid);

    $r = $db->rawQuery("SELECT a.*
        FROM av_devis_detail a         
        where id_devis = ? ", $params);

    foreach ($r as $k => $devisdetail) {
        $r[$k]["combinations"] = getUserDevisProductAttributs($devisdetail["id_devis_detail"]);
    }

    return $r;
}

function getUserDevisProductAttributs($ddid) {
    global $db;
    $params = array($ddid);

    $r = $db->rawQuery("SELECT a.*
        FROM  av_devis_product_attributes a         
        where id_devis_detail = ? ", $params);


    return $r;
}

?>
