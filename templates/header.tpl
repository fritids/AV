<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>{$meta.title|lower|ucfirst}</title>
        <meta name="description" content="{$meta.description}">
        <meta name="keywords" content="{$meta.keywords}">
        <meta name="robots" content="index,follow" />
        <link rel="icon" type="image/vnd.microsoft.icon" href="/img/favicon.ico" />
        <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
        <meta name="viewport" content="width=1200">

        <link rel="stylesheet" href="/css/normalize.css">
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/allovitres.css">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="/css/menu.css" type="text/css">
        <link rel="stylesheet" href="/css/menu.css" type="text/css">
        <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css">
        {if isset($categorie)}
            <link rel="canonical" href="http://www.allovitres.com/{$categorie.id_category}-{$categorie.link_rewrite}"/>
        {/if}

        {if isset($product)}
            <link rel="canonical" href="http://www.allovitres.com/{$product.category.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html"/>
        {/if}

        <script src="/js/vendor/modernizr-2.6.2.min.js"></script>
        <!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.3.min.js"></script>-->
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script type="text/javascript" src="http://malsup.github.io/jquery.cycle2.js"></script>
        <script type="text/javascript" src="/js/bootstrap.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script src="js/jquery.chained.js"></script>    
                <!-- lien : /{$categorie.id_category}-{$categorie.link_rewrite}/ -->
        <!--       <script>
                   $(window).load(function() {
       
                       var initialBg = $('#test123').css("background-image"); // added
       
                       var firstTime = true;
                       var arr = [initialBg, "url(/bg/2.png)", "url(/bg/1.png)", "url(/bg/2.png)", "url(/bg/1.png)"]; // changed
                       (function recurse(counter) {
                           var bgImage = arr[counter];
                           if (firstTime == false) {
       
       
                               $('#test123').fadeTo(200, 0, function() {
                                   $('#test123').css('background-image', bgImage);
                               }).delay(500).fadeTo(200, 1);
       
       
                           } else {
                               firstTime = false;
                           }
                           delete arr[counter];
                           arr.push(bgImage);
                           setTimeout(function() {
                               recurse(counter + 1);
                           }, 10000);
                       })(0);
                   });
               </script>
        -->
        <script language="JavaScript">
            function switch_recherche(a_cacher, a_voir)
            {
                    document.getElementById(a_cacher).style.display = 'none';
                    document.getElementById(a_voir).style.display = 'inline';
                }
                function switch_recherche2(a_selectionner, a_deselectionner)
            {
                    document.getElementById(a_selectionner).style.background = '#70b1fa';
                    document.getElementById(a_deselectionner).style.background = '#1880b1';
                }
        </script>

    </head>
    <body id="body">
        <div>
            <!--[if lt IE 7]>
                <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
            <![endif]-->

            <header>
                <div id="top-header">
                    <div id="top-header-centre">
                        <span id="top-header-texte" ><h1 style="font-size: 0.9em;line-height: 29px;display:inline;">Vos vitres moins chères avec Allovitres, spécialiste de la vente de verre pas cher</h1></span>
                        <div id="top-header-droit"><div class="puce_caddie"><div class="puce_caddie_taille"><p class="puce_caddie_taille">{$cart_nb_items}</p></div></div><a href="/?cart"><img src="/img/caddie.png"/></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                    {if isset($smarty.session.is_logged) && $smarty.session.is_logged}
                                Bonjour <a href="/?my-account">{$smarty.session.user.lastname} {$smarty.session.user.firstname}</a>
                                <a href="/?action=logout">deconnexion</a>
                            {else}
                                <span style="background:#fe6600;height:29px;height-line:29px;min-height:29px;display:inline-block;padding:0 10px;"><a href="/?identification" title="connexion">SE CONNECTER</a></span>
                            {/if}
                        </div>
                    </div>
                </div>

                <div id="header">
                    <a href="/"><img id="logo" src="/img/logo.png" /></a>
                    <div id="header-droit">
                        <div><a href="/index.php?contactez-nous"><img id="img-contactez-nous" src="/img/contactez-nous.png" /><span id="nous-contacter">Nous contacter</span></a></div>
                        <span id="telephone">0 892 70 11 13</span>
                        <span id="telephone2">(0,34€TT/min)</span>                        
                    </div>
                    <div id="reseau-sociaux">
                        <img class="rs-icon" src="/img/RS-twitter.png" />
                        <a target="_blank" href="https://www.facebook.com/Allovitres"><img class="rs-icon" src="/img/RS-facebook.png" /></a>
                        <div>
                        </div>
                    </div>
            </header>
            <script>
                $(function() {
                    $.noConflict();
                    $("#recherche_classique").autocomplete({
                        source: '/functions/ajax_produits.php',
                        select: function(event, ui) {
                            console.log(ui.item.id_product);
                            $("#search_product").val(ui.item.id_product);
                        }
                    });
                });
            </script>

            {include file='sub_menu.tpl'}

            <div id="recherche">

                <div id="rech-conteneur-gauche" >
                    <div id="rech-selecteur1" onmouseover="switch_recherche('rech-conteneur-droit2', 'rech-conteneur-droit1');
                    switch_recherche2('rech-selecteur1', 'rech-selecteur2');">
                        <span class="txt-recherche">RECHERCHE</span>
                        <span class="txt-recherche2">Par type de verre</span>
                    </div>
                    <div id="rech-selecteur2" onmouseover="switch_recherche('rech-conteneur-droit1', 'rech-conteneur-droit2');
                    switch_recherche2('rech-selecteur2', 'rech-selecteur1');">
                        <span class="txt-recherche">RECHERCHE</span>
                        <span class="txt-recherche2">Par type de projet</span>
                    </div>
                </div>
                <div id="rech-conteneur-droit1">
                    <span id="recherche-header-classique">
                        <form action="/index.php?search" method="post">
                            <input type="hidden" name="p" value="">
                            <input type="hidden" name="id" value="" id="search_product">
                            <input type="text" name="search_query" id="recherche_classique" class="recherche_classique" placeholder="Rechercher produits..."/>
                            <input type="submit" value="Valider">
                        </form>
                    </span>
                </div>
                <div id="rech-conteneur-droit2">
                    <form action="index.php?search" method="post"> 

                        <ul>
                            <li>
                                <label>Etape 1</label>
                                <select id="select_type1" name="search_lvl_1">
                                    {foreach key=key item=search from=$searchs}
                                        <option value="{$key}" {if $smarty.post.search_lvl_1 == $key} selected {/if}>{$search.lvl1_title}</option>                                
                                    {/foreach}
                                </select>
                            </li>
                            <li>
                                <label>Etape 2</label>
                                <select id="select_type2" name="search_lvl_2">
                                    {foreach key=key item=search from=$searchs}
                                        {foreach key=key2 item=lvl2 from=$search.lvl2}
                                            <option value="{$lvl2.id_search_lvl2}" class="{$key}" {if $smarty.post.search_lvl_2 == $lvl2.id_search_lvl2} selected {/if}>{$lvl2.lvl2_title}</option>                                
                                        {/foreach}
                                    {/foreach}
                                </select>
                            </li>

                        </ul>
                        <input type="submit" style="background-image:url(img/rechercher.png);width:195px;height:41px;margin-top:15px;" value=""/>
                    </form>
                </div>

                <script>
                $("#select_type2").chained("#select_type1");
                </script>
                <div id="rech-conteneur-droit2">

                </div>


            </div>