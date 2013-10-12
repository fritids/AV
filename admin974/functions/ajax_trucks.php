<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

function addtruckTournee($id) {
    global $db;

    $imp = explode("|", $id);

    $info = array(
        "id_truck" => $imp[1],
        "id_order_detail" => $imp[2],
        "date_livraison" => $imp[3],
        "status" => 1,
    );

    $r = $db->insert("av_tournee", $info);

    if ($r)
        return(json_encode($imp[2] . " " . $imp[3] . "ok"));
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

function updValidTruck($id) {
    global $db;

    $imp = explode("|", $id);

    $info = array(
        "status" => 2,
    );

    $r = $db->where("id_truck", $imp[0])
            ->where("date_livraison", $imp[1])
            ->update("av_tournee", $info);


    $info = array(
        "id_truck" => $imp[0],
        "date_delivery" => $imp[1],
        "status" => 1
    );
    
    $r = $db->insert("av_truck_planning", $info);


    if ($r)
        return(json_encode($imp . "ok"));
}

// APPEL
if (isset($_POST["func"])) {
    print_r(call_user_func($_POST["func"], $_POST["id"]));
}

//updValidTruck("1|2013-10-31");
//echo deltruckProduct(5);
?>
