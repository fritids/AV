<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

function getCamionProduct($id) {
    return(json_encode($id . "ok"));
}

function addCamionProduct($id) {
    global $db;

    $info = array(
        "id_camion" => 1,
        "id_order_detail" => 1,
        "id_planning" => 1,
        "status" => 1,
    );

    $r = $db->insert("av_camion_product", $info);

    if ($r)
        return(json_encode($id . "ok"));
}

function delCamionProduct($id) {
    global $db;
    
    $r = $db->where("id_camion_product", $id)
            ->delete("av_camion_product");
    if ($r)
        return(json_encode($id . "ok"));
}

function updCamionStatus($id) {
    return(json_encode($id . "ok"));
}

if (isset($_POST["func"])) {
    print_r(call_user_func($_POST["func"], $_POST["id"]));
}

//addCamionProduct(1);
//echo delCamionProduct(5);
?>
