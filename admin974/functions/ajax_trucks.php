<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

function addtruckTournee($id) {
    global $db;
    $imp = explode("|", $id);

    $order = $db->where("id_order_detail", $imp[2])
            ->get("av_order_detail");



    $info = array(
        "id_truck" => $imp[1],
        "id_order" => $order[0]["id_order"],
        "id_order_detail" => $imp[2],
        "date_livraison" => $imp[3],
        "nb_product_delivered" => $imp[4],
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

    /* on fixe la date livraison du camion */

    $info = array("status" => 2);

    $r = $db->where("id_truck", $imp[0])
            ->where("date_livraison", $imp[1])
            ->update("av_tournee", $info);

    /* on bloque le camion pour la date livraison */
    $infoTruckPlanning = array(
        "id_truck" => $imp[0],
        "date_delivery" => $imp[1],
        "status" => 1
    );

    $r = $db->insert("av_truck_planning", $infoTruckPlanning);

    /* on passe les produits en livraison prévu */
    $orderDetails = $db->rawQuery("select id_order_detail from av_tournee where id_truck = ? and date_livraison = ? and status = 2", array($imp[0], $imp[1]));

    foreach ($orderDetails as $orderDetail) {
        $r = $db->where("id_order_detail", $orderDetail["id_order_detail"])
                ->update("av_order_detail", array("product_current_state" => 7));
    }

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
