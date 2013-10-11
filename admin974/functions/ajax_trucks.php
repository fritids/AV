<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

function gettruckProduct($id) {
    return(json_encode($id . "ok"));
}

function addtruckProduct($id) {
    global $db;
    
    $imp = explode("|", $id) ;

    $info = array(
        "id_truck" => $imp[1],
        "id_order_detail" => $imp[2],
        "id_planning" => 1,
        "status" => 1,
    );

    $r = $db->insert("av_truck_product", $info);

    //if ($r)
        return(json_encode("ok"));
}

function addtruckTournee($id) {
    global $db;
    
    $imp = explode("|", $id) ;

    $info = array(
        "id_truck" => $imp[1],
        "id_order_detail" => $imp[2],
        "date_livraison" => $imp[3],
        "status" => 1,
    );

    $r = $db->insert("av_tournee", $info);

    //if ($r)
        return(json_encode($imp[2]."ok"));
}

function deltruckProduct($id) {
    global $db;
    
    $r = $db->where("id_truck_product", $id)
            ->delete("av_truck_product");
    if ($r)
        return(json_encode($id . "ok"));
}
function delProduitTournee($id) {
    global $db;
    
    $r = $db->where("id_order_detail", $id)
            ->delete("av_tournee");
    if ($r)
        return(json_encode($id . "ok"));
}

function updtruckStatus($id) {
    return(json_encode($id . "ok"));
}

if (isset($_POST["func"])) {
    print_r(call_user_func($_POST["func"], $_POST["id"]));
}




//addtruckProduct(1);
//echo deltruckProduct(5);
?>
