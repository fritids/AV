<?
@session_start();
require_once 'config/av_conf.php';
include ("./av_utilities.php");
include ("./securite.php");

//echo setlocale(LC_ALL, 0);
setlocale(LC_ALL, 'fr_FR.utf8', 'fr');

mysql_query("SET NAMES UTF8");

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
        <link rel="stylesheet" href="css/date-picker.css" type="text/css" media="screen"/>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
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

        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>        
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script src="js/jquery.chained.js"></script>        
        <script src="js/bootstrap.min.js"></script>
        <script>
            $(function() {
                $("#datepicker").datepicker({
                    showButtonPanel: true
                });
                $("#datepicker").datepicker("option", "dateFormat", "yy-mm-dd");
            });

            $(function() {
                //$("#datepicker2").datepicker($.datepicker.regional[ "fr" ], showButtonPanel: true);
                $("#datepicker2").datepicker({
                    showButtonPanel: true
                });
                $("#datepicker2").datepicker("option", "dateFormat", "yy-mm-dd");

                $('#datepicker2').val("<?= @$_GET["planning"] ?>");
                $('#datepicker').val("<?= @$_GET["invoice_date"] ?>");
            });
        </script>
    </head>
    <body>



        <!-- Fixed navbar -->
        <div class="navbar navbar-default navbar-fixed-top navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="?"><img src="../img/logo.png" style="height: 50px;"> </a>

                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">                        
                        <?
                        if ($_SESSION['role'] == "ADMIN" ) {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalogues <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="av_product.php">Produits</a></li>
                                    <li><a href="av_product_attribute.php">Caracteristiques</a></li>
                                    <li><a href="av_product_caract.php">Options</a></li>
                                    <li><a href="av_product_images.php">Images</a></li>                                
                                </ul>
                            </li>     
                            <?
                        }
                        ?>
                        <?
                        if ($_SESSION['role'] == "ADMIN" || $_SESSION['role'] == "COMMANDE") {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Les ventes <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="av_orders.php">Commandes</a></li>
                                    <li><a href="av_customer.php">Clients</a></li>
                                    <li><a href="av_address.php">Adresses</a></li>
                                </ul>
                            </li>  
                            <?
                        }
                        ?>
                        <?
                        if ($_SESSION['role'] == "ADMIN" || $_SESSION['role'] == "LIVRAISON") {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Livraison <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="av_tournee.php?planning=<?= date("Y-m-d") ?>">La tournée</a></li>                                
                                    <li><a href="av_tournee_manifest.php">Manifest</a></li>                                
                                </ul>
                            </li>  <?
                        }
                        ?>  

                        <?
                        if ($_SESSION['role'] == "ADMIN") {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="admin_user.php">Utilisateurs</a></li>
                                    <li class="divider"></li>
                                    <li><a href="av_range_weight.php">Frais de port</a></li>
                                    <li><a href="av_zone.php">Zones</a></li>
                                    <li><a href="av_departements.php">Departement / Zones </a></li>
                                    <li class="divider"></li>
                                    <li><a href="av_order_status.php">Statuts des commandes </a></li>                                
                                    <li class="divider"></li>
                                    <li><a href="av_camion.php">Camions</a></li>                                
                                    <li class="divider"></li>
                                    <li><a href="av_cms_lang.php">Contenu manager</a></li>                                    
                                </ul>
                            </li>  
                            <?
                        }
                        ?>  
                            <?
                        if ($_SESSION['role'] == "ADMIN"|| $_SESSION['role'] == "COMMANDE") {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Devis <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="av_devis.php" >Saisir un devis</a></li>                    
                                    <li class="divider"></li>
                                    <li><a href="av_devis_view.php">Voir les devis</a></li>                              
                                </ul>
                            </li>  
                            <?
                        }
                        ?>  
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                                    
                        <li><a href="logout.php">Deconnexion</a></li>                        
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
