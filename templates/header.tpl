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
			
			<div class="modal fade" id="LostPwd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Mot de passe oublié</h4>
                </div>
                <div class="modal-body">
                    <form method="post" action="/index.php?action=lost_pwd">
                        <div class="box_chmp">
                            <label>email :</label>
                            <input class="txt_account" type="text" name="email">
                            <div class="clear"></div>
                        </div>
                        <br/>
                        <input class="submit_generer_mdp" type="submit" value=" ">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>                            
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
			
			
                <div id="top-header">
                    <div id="top-header-centre">
                        <span id="top-header-texte" ><h1 style="font-size: 0.9em;line-height: 29px;display:inline;">Vos vitres moins chères avec Allovitres, spécialiste de la vente de verre pas cher</h1></span>
                        <div id="top-header-droit">
                                    {if isset($smarty.session.is_logged) && $smarty.session.is_logged}
						
								
								<span id="test12345" style="height:29px;height-line:29px;min-height:29px;display:inline-block;">
								
								
								 <ul class="menu_ident">
								<li><div class="puce_caddie"><div class="puce_caddie_taille"><p class="puce_caddie_taille">{$cart_nb_items}</p></div></div><a href="/?cart"><img style="margin-right:15px;" src="/img/caddie.png"/></a> | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?my-account">Bonjour {$smarty.session.user.firstname} </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
								 <li class="ident">
									<span><a style="color:white;text-decoration:none;" href="/?action=logout">SE DECONNECTER</a></span>
			
									
								</li> 
								</ul>
								
								
								
								
								
								</span>
								
								
								
								
								
                            {else}
                                <span id="test12345" style="height:29px;height-line:29px;min-height:29px;display:inline-block;">
								
								
								 <ul class="menu_ident">
								<li><div class="puce_caddie"><div class="puce_caddie_taille"><p class="puce_caddie_taille">{$cart_nb_items}</p></div></div><a href="/?cart"><img style="margin-right:15px;" src="/img/caddie.png"/></a></li>
								 <li class="ident">
									<span><a style="color:white;text-decoration:none;" href="/?identification">SE CONNECTER</a></span>
									<ul>
										<li>
											<span>J'ai déjà un compte</span>
											<span id="login_message"></span>
											<form action="/index.php?action=login" method="post" id="loginForm">
												<input name="email" id="login_email" type="text" placeholder="Mon adresse email" />
												<input name="passwd" id="login_pass" type="password" placeholder="Mon mot de passe" />
                                                <input type="hidden" name="referer" value="/?my-account" />
												<a class="oubli" href="" data-target="#LostPwd" data-toggle="modal">Mot de passe oublié ?</a>
												<button id="login_submit" class="bouton_generique" type="submit" title="Se connecter" style="background-image:url('/img/btn_compteclient.png');width:180px;height:20px;border:0;">Se connecter</button>
												
											</form>
										</li>
										<li>
											<span>Je n'ai pas de compte</span>
											<a class="bouton_generique" href="/?register" title="Créer un compte client">Créer un compte client</a>
										</li>
									</ul>
									
								</li> 
								</ul>
								
								
								
								
								
								</span>
                            {/if}
                        </div>
                    </div>
                </div>

                <div id="header">
                    <a href="/"><img id="logo" src="/img/logo.png" /></a>
                    <div id="header-droit">
                        <div><a href="/index.php?contactez-nous"><img id="img-contactez-nous" src="/img/contactez-nous.png" /><span id="nous-contacter">Nous contacter</span></a></div>
                        <span id="telephone" title="du lundi au vendredi de 9h à 12h et de 14h à 18h.">0 892 70 11 13</span>
                        <span id="telephone2">(0,34€TT/min)</span>
						<span id="telephone3">Du lundi au vendredi<br/>de 9h à 12H et de 14h à 18h</span>						
                    </div>
                    <div id="reseau-sociaux">
                        <a target="_blank" href="https://plus.google.com/b/117282875950258035705/+Allovitres/posts"><img class="rs-icon" src="/img/RS-gplus.png" /></a>
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
                             <input type="submit" style="background-image:url(/img/btn_searchmod.png);width:204px;height:34px;margin:25px 0 0 25px;border:0;" value=""/>
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
                        <input type="submit" style="background-image:url(/img/btn_searchmod.png);width:204px;height:34px;margin-top:25px;border:0;" value=""/>
                    </form>
                </div>

                <script>
                $("#select_type2").chained("#select_type1");
                </script>
                <div id="rech-conteneur-droit2">

                </div>


            </div>