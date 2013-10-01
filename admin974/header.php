<?
include ("config/av_conf.php");
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html">

        <script src="ckeditor/ckeditor.js"></script>
        <link rel="stylesheet" href="css/admin.css">
        <link rel="stylesheet" href="css/av_admin.css">
        <!-- tinyMCE -->
        <script language="javascript" type="text/javascript" src="tinymce/tinymce.min.js"></script>
        <script language="javascript" type="text/javascript">
            tinyMCE.init({
                mode: "specific_textareas",
                auto_reset_designmode: true
            });
        </script>
        <!-- /tinyMCE -->
    </head>
    <body>

        <div class="breadcrumb">
            <span class="left"></span>
            <ul>
                <li><a href="av_product.php">Produits</a></li>
                <li><a href="av_product_caract.php"> Produits -Caractéristiques</a></li>
                <li><a href="av_product_attribute.php">Produits - Options</a></li>                    
                <li><a href="av_product_images.php">Produits - Images</a></li>
                <li><a href="av_customer.php">Clients</a></li>
                <li><a href="av_orders.php">Commandes</a></li>
                <li><a href="av_category.php">Gestion des categories</a></li>
                <li><a href="logout.php">Deconnexion</a></li>
            </ul>
            <span class="right"></span>
        </div>