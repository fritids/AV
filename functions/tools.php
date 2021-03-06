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
            and  a.id_departement = ? ";

    $z = $db->rawQuery($query, array(substr($postcode, 0, 2)));

    if ($z)
        return ($z[0]["nom"]);
}

function getItemTourneeinfo($odetail) {
    global $db;
    $r = $db->rawQuery("select a.*, b.name truck_name
        from av_tournee a , av_truck b
        where a.id_truck= b.id_truck
        and a.id_order_detail = ?", array($odetail));

    if ($r)
        return($r[0]);
}

function addLog($info) {
    global $db;

    $params = array(
        "updated" => date("Y-m-d H:i:s"),
        "user" => $_SESSION["email"],
        "host" => $_SERVER['REMOTE_ADDR'],
        "operation" => $info["operation"],
        "tab" => $info["tabs"],
        "col" => $info["col"],
        "rowkey" => $info["rowkey"],
        "oldval" => addslashes($info["oldval"]),
        "newval" => addslashes($info["newval"])
    );
    $r = $db->insert("changelog", $params);
}

function getSearchCriterias() {
    global $db;

    $r = $db->rawQuery("select distinct a.id_search_lvl1 , b.label lvl1_title
        from av_search_result a , av_search_criteria b
        where a.id_search_lvl1 = b.id_search_criteria");

    foreach ($r as $i => $lvl1) {
        $k = $db->rawQuery("select distinct a.id_search_lvl2 , b.label lvl2_title
        from av_search_result a , av_search_criteria b
        where a.id_search_lvl2 = b.id_search_criteria
        and a.id_search_lvl1 = ?
        ", array($lvl1["id_search_lvl1"]));

        $o[$lvl1["id_search_lvl1"]]["lvl1_title"] = $lvl1["lvl1_title"];
        $o[$lvl1["id_search_lvl1"]]["lvl2"] = $k;
    }

    /* select a.* , b.label lvl1_title, c.label lvl2_title, d.id_product, d.name product_name
      from av_search_result a , av_search_criteria b ,av_search_criteria c , av_product d
      where a.id_search_lvl1 = b.id_search_criteria
      and a.id_search_lvl2 = c.id_search_criteria
      and a.result_id_product = d.id_product
     */
    return $o;
}

function getSearchResults($param1, $param2) {
    global $db;

    $r = $db->rawQuery("select result_id_product
      from av_search_result a 
      where id_search_lvl1  = ?
      and id_search_lvl2  = ?
      ", array($param1, $param2));

    foreach ($r as $i => $product) {
        $o[$i] = getProductInfos($product["result_id_product"]);
    }
    return $o;
}

function getSearchResultsByName($param1) {
    global $db;

    $r = $db->rawQuery("SELECT id_product FROM av_product WHERE active = 1 and lower(name) LIKE ? ", array("%" . $param1 . "%"));

    foreach ($r as $i => $product) {
        $o[$i] = getProductInfos($product["id_product"]);
    }
    return $o;
}

function mapCustomAttribute($pCustomDetail) {
    global $db;

    foreach ($pCustomDetail as $k => $main_attribute) {
        if (is_array($main_attribute)) {

            $r = $db->where("id_attribute", $k)
                    ->get("av_attributes");

            $o[$k]["id_attribute"] = $k;
            $o[$k]["main_item_name"] = $r[0]["name"];

            foreach ($main_attribute as $l => $sub_attributes) {
                if (is_array($sub_attributes)) {
                    $s = $db->where("id_attributes_items", $l)
                            ->get("av_attributes_items");

                    $o[$k][$l]["id_attributes_items"] = $l;
                    $o[$k][$l]["sub_item_name"] = $s[0]["name"];
                    $o[$k][$l]["picture"] = $s[0]["picture"];

                    foreach ($sub_attributes as $m => $sub_attribute) {
                        $o[$k][$l][$m]["price_impact_percentage"] = $s[0]["price_impact_percentage"];
                        $o[$k][$l][$m]["price_impact_amount"] = $s[0]["price_impact_amount"];
                        foreach ($sub_attribute as $n => $item_value) {

                            $t = $db->where("id_attributes_items_values", $n)
                                    ->get("av_attributes_items_values");

                            $o[$k][$l][$m][$n]["custom_value"] = $item_value;
                            $o[$k][$l][$m][$n]["item_value_name"] = $t[0]["name"];
                        }
                    }
                }
            }
        }
    }

    return $o;
}

function estFerie($j) { // Détermine si un jour (format Y-m-d) est férié
    $res = 0;
    // les jours fériés
    $joursFeries = array("-01-01", "-05-01", "-05-08", "-07-14", "-08-15", "-11-01", "-11-11", "-12-25");
    for ($i = 0; $i < count($joursFeries); $i++)
        if ($j == (substr($j, 0, 4) . $joursFeries[$i])) {
            $res = 1;
        }
    // Si c'est le lundi de paques
    if ($j == date("Y-m-d", strtotime("+1 day", easter_date(substr($j, 0, 4))))) {
        $res = 1;
    }
    // ascension
    if ($j == date("Y-m-d", strtotime("+39 day", easter_date(substr($j, 0, 4))))) {
        $res = 1;
    }
    return $res;
}

function getJours($datedeb, $datefin) {
    $nb_jours = 0;
    $dated = explode('-', $datedeb);
    $datef = explode('-', $datefin);
    $timestampcurr = mktime(0, 0, 0, $dated[1], $dated[2], $dated[0]);
    $timestampf = mktime(0, 0, 0, $datef[1], $datef[2], $datef[0]);
    while ($timestampcurr < $timestampf) {

        if ((date('w', $timestampcurr) != 0) && (date('w', $timestampcurr) != 6) && estFerie(date('Y-m-d', $timestampcurr)) == 0) {
            $nb_jours++;
        }
        $timestampcurr = mktime(0, 0, 0, date('m', $timestampcurr), (date('d', $timestampcurr) + 1), date('Y', $timestampcurr));
    }
    return $nb_jours;
}

function getSupplierZone($idzone) {
    global $db;

    $r = $db->rawQuery("select a.* 
                        from av_supplier a , av_supplier_zone b 
                        where a.id_supplier = b.id_supplier
                        and (b.id_zone = 0 or b.id_zone = ? )", array($idzone));

    return $r;
}

function getSupplierWarehouses() {
    global $db;

    $r = $db->rawQuery("select b.id_supplier, b.id_warehouse, b.id_supplier_warehouse, a.name supplier_name
                        from av_supplier a , av_supplier_warehouse b 
                        where a.id_supplier = b.id_supplier");

    return $r;
}

function genSecureKey() {

    return(md5(RandomString()));
}

?>
