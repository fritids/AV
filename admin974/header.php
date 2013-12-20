<?
@session_start();
require_once 'config/av_conf.php';
include ("./av_utilities.php");
include ("./securite.php");

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
        <link rel="stylesheet" href="css/dragdrop.css">
        <!-- tinyMCE -->
        <script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
        <script language="javascript" type="text/javascript">
            tinyMCE.init({
                mode: "specific_textareas",
                auto_reset_designmode: true,
                theme: "advanced",
                width: "800",
                height: "300",
                plugins: "table,autosave,save,insertdatetime,preview,searchreplace,fullscreen,jbimages",
                theme_advanced_buttons1_add_before: "save,separator",
                theme_advanced_buttons1_add: "fontselect,fontsizeselect",
                theme_advanced_buttons2_add: "insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
                theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
                theme_advanced_buttons3_add_before: "tablecontrols,separator",
                theme_advanced_buttons3_add: "emotions,iespell,flash,advhr,separator,print",
                theme_advanced_toolbar_location: "top",
                theme_advanced_toolbar_align: "left",
                theme_advanced_path_location: "bottom",
                plugin_insertdate_dateFormat: "%Y-%m-%d",
                plugin_insertdate_timeFormat: "%H:%M:%S",
                
            });
        </script>

        <!-- /tinyMCE -->

        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>        
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script src="js/jquery.chained.js"></script>        
        <script src="js/bootstrap.min.js"></script>

        <script>
            jQuery(function($) {
                $.datepicker.regional['fr'] = {
                    closeText: 'Fermer',
                    prevText: '<Préc',
                    nextText: 'Suiv>',
                    currentText: 'Courant',
                    monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                    monthNamesShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
                        'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                    dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                    dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                    dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                    weekHeader: 'Sm',
                    //dateFormat: 'dd/mm/yy',
                    dateFormat: 'yyyy-mm-dd',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''};

                $.datepicker.setDefaults($.datepicker.regional['fr']);
            });

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
                $("#datepicker2").datepicker("setDate", new Date());

                
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
                    <a class="navbar-brand" href="http://www.allovitres.com/"><img src="../img/logo.png" style="height: 50px;"> </a>

                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">                        
                        <?
                        if ($_SESSION['role'] == "ADMIN") {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalogues <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="av_product.php">Produits</a></li>                                    
                                    <li><a href="av_product_attribute.php">Caracteristiques</a></li>
                                    <li><a href="av_product_caract.php">Options</a></li>
                                    <li><a href="av_product_images.php">Images</a></li>     
                                    <li class="divider"></li>
                                    <li><a href="av_category.php">Categories</a></li>
                                    <li><a href="av_attributes.php">Attributs</a></li>                                    
                                </ul>
                            </li>     
                            <?
                        }
                        ?>
                        <?
                        if ($_SESSION['role'] == "ADMIN" || $_SESSION['role'] == "COMMANDE" || $_SESSION['role'] == "LOGISTIC") {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Les ventes <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="av_orders.php">Commandes</a></li>                                    
                                    <li><a href="av_customer.php">Clients</a></li>
                                    <li><a href="av_orders_dashboard.php">Tableau de bord</a></li>
                                </ul>
                            </li>  
                            <?
                        }
                        ?>
                        <?
                        if ($_SESSION['role'] == "ADMIN" || $_SESSION['role'] == "LIVRAISON" || $_SESSION['role'] == "LOGISTIC") {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Livraison <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="av_tournee.php">La tournée</a></li>                                
                                    <li><a href="av_bon_livraison.php">Bon livraison</a></li>                                
                                    <li><a href="av_roadmap.php">Feuille de route</a></li> 
                                    <li class="divider"></li>
                                    <li><a href="av_supplier.php">Les fournisseurs</a></li>                                
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
                        if ($_SESSION['role'] == "ADMIN" || $_SESSION['role'] == "COMMANDE" || $_SESSION['role'] == "LOGISTIC") {
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
