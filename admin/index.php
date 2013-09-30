<?php
session_start();
include("mysqlmdp.php");
mysql_connect($bdserv, $bduser, $bdpass);
mysql_select_db($bdname);
include('securite.php');
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="description" content="">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="breadcrumb">
        <span class="left"></span>
        <ul>
            <li><a href="/av_product.php">Produits</a></li>
            <li><a href="/av_customer.php">Clients</a></li>
            <li><a href="/av_orders.php">Commandes</a></li>
            <li><a href="/menus">Gestion de l'administration</a></li>
            <li><a href="logout.php">Deconnexion</a></li>
        </ul>
        <span class="right"></span>
    </div>

    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 'accueil';
    }

    switch ($page) {
        case "accueil":
            $titre = 'Allovitre';
            $meta_description = 'description meta';
            $fil_ariane = '<a href="/">ACCUEIL</a> >';
            break;
        case "creer_compte":
            $titre = 'Allovitre';
            $meta_description = 'description meta';
            $fil_ariane = '<a href="/">ACCUEIL</a> >';
            break;
    }

    include('pages/' . $page . '.php');
    ?>


</body>
</html>
