<?php

require_once "../configs/settings.php";
include ("../classes/MysqliDb.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
/*
  $_POST["id_order"] = 1;
  $_POST["action"] = "update";
  $_POST["module"] = "orders";
  $_POST["current_state"] = "2"; */

function get_server_var($name) /* {{{ */ {
    if (isset($_SERVER[$name])) {
        return $_SERVER[$name];
    }
    global $HTTP_SERVER_VARS;
    if (isset($HTTP_SERVER_VARS[$name])) {
        return $HTTP_SERVER_VARS[$name];
    }
    global $$name;
    if (isset($$name)) {
        return $$name;
    }
    return null;
}

/* }}} */

function getOrderCurrentState() {

    global $db;

    $r2 = $db->where("reference", $_POST["reference"])
            ->get("av_orders");

    return(print_r(json_encode($r2[0]["current_state"])));
}

function getOrderCombobox() {

    global $db;
    $r = $db->get("av_order_status");

    $r2 = $db->where("reference", $_POST["reference"])
            ->get("av_orders");

    $opt = '<select name="' . $r2[0]["id_order"] . '" class="pme-input-1">';
    $opt .= '<option value="" selected> -- </option>';
    foreach ($r as $k => $v) {
        if ($r2[0]["current_state"] == $v["id_statut"]) {
            $opt .= '<option value="' . $v["id_statut"] . '" selected> ' . $v["title"] . '</option>';
        } else {
            $opt .= '<option value="' . $v["id_statut"] . '"> ' . $v["title"] . '</option>';
        }
    }
    $opt .= "</select>";

    return(print_r(json_encode($opt)));
}

function getOrderDetailCombobox() {

    global $db;
    $r = $db->get("av_order_status");

    $r2 = $db->where("id_order_detail", $_POST["id_order_detail"])
            ->get("av_order_detail");

    $opt = '<select name="' . $r2[0]["id_order_detail"] . '" class="pme-input-1">';
    $opt .= '<option value="" selected> -- </option>';
    foreach ($r as $k => $v) {
        if ($r2[0]["product_current_state"] == $v["id_statut"]) {
            $opt .= '<option value="' . $v["id_statut"] . '" selected> ' . $v["title"] . '</option>';
        } else {
            $opt .= '<option value="' . $v["id_statut"] . '"> ' . $v["title"] . '</option>';
        }
    }
    $opt .= "</select>";

    return(print_r(json_encode($opt)));
}

function updateOrder() {
    global $db;
    $r2 = $db->where("id_order", $_POST["id_order"])
            ->get("av_orders");


    $r = $db->where("id_order", $_POST["id_order"])
            ->update("av_orders", array("current_state" => $_POST["current_state"]));

    $info = array(
        "user" => addslashes(get_server_var('REMOTE_USER')),
        "host" => addslashes(get_server_var('REMOTE_ADDR')),
        "operation" => "update",
        "tab" => "av_orders",
        "rowkey" => $_POST["id_order"],
        "col" => "current_state",
        "oldval" => $r2[0]["current_state"],
        "newval" => $_POST["current_state"]
    );

    $r = $db->insert("changelog", $info);

    if ($r)
        return(print_r(json_encode("ok")));
}

function updateOrderDetail() {
    global $db;
    $r2 = $db->where("id_order_detail", $_POST["id_order_detail"])
            ->get("av_order_detail");


    $r = $db->where("id_order_detail", $_POST["id_order_detail"])
            ->update("av_order_detail", array("product_current_state" => $_POST["product_current_state"]));

    $info = array(
        "user" => addslashes(get_server_var('REMOTE_USER')),
        "host" => addslashes(get_server_var('REMOTE_ADDR')),
        "operation" => "update",
        "tab" => "av_order_detail",
        "rowkey" => $_POST["id_order_detail"],
        "col" => "product_current_state",
        "oldval" => $r2[0]["product_current_state"],
        "newval" => $_POST["product_current_state"]
    );

    $r = $db->insert("changelog", $info);

    if ($r)
        return(print_r(json_encode("ok")));
}

function getChangeLog($table, $key) {
    global $db;

    if (!empty($table))
        $db->where("tab", $table);

    if (!empty($key))
        $db->where("rowkey", $key);

    $r = $db->get("changelog");

    if ($r) {
        echo "<table class='table table-bordered table-condensed'>";
        echo "<tr><th>Opération</th><th>Date</th><th>Utilisateur</th><th>Référence</th><th>Colonne</th><th>Valeur</th></tr>";
        foreach ($r as $k => $v) {

            $operation = $v["operation"];
            $user = $v["user"];
            $time = $v["updated"];
            $key = $v["rowkey"];
            $tab = $v["tab"];
            $col = $v["col"];
            $user = $v["user"];
            $newval = @unserialize($v["newval"]);
            $oldval = @unserialize($v["oldval"]);


            echo "<td>" . $operation . "</td> <td> " . $time . " </td> <td> " . $user . " </td> <td> " . $key . " </td> <td> " . $col . " </td> <td> ";

            echo "<table>";

            if ($col == "current_state") {
                $func = "logGetStateLabel";
            } elseif ($col == "id_supplier") {
                $func = "logGetSupplier";
            } else {
                $func = "logNoFunc";
            }

            if (is_array($oldval)) {
                foreach ($oldval as $k2 => $v2) {
                    echo "<tr><td>" . $k2 . " </td><td>" . call_user_func($func, $v2) . "</td></tr> ";
                }
            } else {
                if (!empty($v["oldval"]))
                    echo "<tr><td>" . "ancienne valeur :</td><td> " . call_user_func($func, $v["oldval"]) . "</td></tr>";
            }

            if (is_array($newval)) {
                foreach ($newval as $k2 => $v2) {
                    echo "<tr><td>" . $k2 . " </td><td>" . call_user_func($func, $v2) . "</td></tr>";
                }
            } else {
                if (!empty($v["newval"]))
                    echo"<tr><td>" . "nouvelle valeur :</td><td>" . call_user_func($func, $v["newval"]) . "</td></tr>";
            }

            echo "</table></td></tr>";
        }
        echo "</table>";
    }else {
        echo "Pas de modifications recensées";
    }
}

function logNoFunc($a) {
    return $a;
}

function logGetStateLabel($a) {
    global $db;
    $r = $db->where(("id_statut"), $a)
            ->get("av_order_status");

    return $r[0]["title"];
}

function logGetSupplier($a) {
    global $db;
    $r = $db->where(("id_supplier"), $a)
            ->get("av_supplier");

    return $r[0]["name"];
}

/* dispatcher */

// Update 
if (isset($_POST["action"]) && $_POST["action"] == "update") {
    if ($_POST["module"] == "orders") {
        updateOrder();
    }
    if ($_POST["module"] == "orders_detail") {
        updateOrderDetail();
    }
}


// Select
if (isset($_POST["action"])) {
    if ($_POST["module"] == "orders") {
        if ($_POST["action"] == "getOrderCombobox") {
            getOrderCombobox();
        }
        if ($_POST["action"] == "getOrderCurrentState") {
            getOrderCurrentState();
        }
    }
    if ($_POST["module"] == "orders_detail") {
        if ($_POST["action"] == "getOrderDetailCombobox") {
            getOrderDetailCombobox();
        }
    }
}
/* dispatcher */
?>