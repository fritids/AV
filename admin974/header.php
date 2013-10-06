<?
@session_start();
include ("config/av_conf.php");
include ("./av_utilities.php");
include ("./securite.php");

$_SERVER['REMOTE_USER'] = $_SESSION["email"];

//require_once './extensions/phpMyEdit-report.class.php';
//require_once './extensions/phpMyEdit-mce-cal.class.php';
//require_once './extensions/phpMyEdit-slide.class';
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html">
        <link rel="stylesheet" href="css/admin.css">
        <link rel="stylesheet" href="css/av_admin.css">
        <link href="css/bootstrap.css" rel="stylesheet">
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
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <!-- Fixed navbar -->
        <div class="navbar navbar-default navbar-fixed-top navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">ADMIN</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="#">Accueil</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalogues <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="av_product.php">Produits</a></li>
                                <li><a href="av_product_attribute.php">Caracteristiques</a></li>
                                <li><a href="av_product_caract.php">Options</a></li>
                                <li><a href="av_product_images.php">Images</a></li>                                
                            </ul>
                        </li>     
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Les ventes <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="av_orders.php">Commandes</a></li>
                                <li><a href="av_customer.php">Clients</a></li>
                                <li><a href="av_address.php">Adresses</a></li>
                            </ul>
                        </li>                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="admin_user.php">Utilisateurs</a></li>
                                <li class="divider"></li>
                                <li><a href="av_range_weight.php">Transport</a></li>
                                <li class="divider"></li>
                                <li><a href="av_order_status.php">Commandes - statut </a></li>                                
                            </ul>
                        </li>                        
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="logout.php">Deconnexion</a></li>                        
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
