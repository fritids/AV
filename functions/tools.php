<?php

function RandomString() {
    $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
        @$randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

function getDeliveryRatio($pweight) {
    global $db;

    $params = array($pweight);

    $r = $db->rawQuery("
        SELECT delivery_ratio 
        FROM `av_range_weight` 
        WHERE ? between `delimiter1` and `delimiter2`", $params);

    return($r[0]["delivery_ratio"]);
}

function getShippingPrice($pid, $pqte_order) {
    global $db;

    $p_info = getProductInfos($pid);

    $delivery_ratio = getDeliveryRatio($p_info["weight"]);

    return($p_info["weight"] * $ratio);
}

function getLastOrderId() {
    global $db;

    $r = $db->rawQuery("select max(id_order)+1 id from av_orders");

    $id = str_pad($r[0]["id"], 9, '0', STR_PAD_LEFT);

    return($id);
}

function getVoucherInfo($code) {
    global $db;
    $r = $db->where("voucher_code", $code)
            ->get("av_voucher");
    if ($r)
        return($r[0]);
}

function getDeliveryZone($postcode) {
    global $db;

    $query = "select b.nom 
            from av_departements a , av_zone b
            where  a.id_zone = b.id_zone
            and  a.id_departement = " . substr($postcode, 0, 2);

    $z = $db->rawQuery($query);

    if ($z)
        return ($z[0]["nom"]);
}

function getItemTourneeinfo($odetail) {
    global $db;
    $r = $db->where("id_order_detail", $odetail)
            ->where("status", 2)
            ->get("av_tournee");
    if ($r)
        return($r[0]);
}

function addLog($info) {
    global $db;

    $params = array(
        "updated" => date("Y-m-d H:i:s"),
        "user" => $_SESSION["email"],
        "host" => $_SERVER['REMOTE_ADDR'],
        "operation" =>  $info["operation"],
        "tab" => $info["tabs"],
        "col" => $info["col"],
        "rowkey" => $info["rowkey"],
        "oldval" => addslashes($info["oldval"]),
        "newval" => addslashes($info["newval"])
    );
    $r = $db->insert("changelog", $params);
}

?>
