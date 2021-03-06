<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
		<meta name="viewport" content="width=1200">

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/allovitres.css">
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/menu.css" type="text/css">
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
		<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.3.min.js"></script>-->
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="http://malsup.github.io/jquery.cycle2.js"></script>
		<script> 
$(window).load(function(){

var initialBg =  $('#test123').css("background-image"); // added

var firstTime = true;
var arr = [initialBg, "url(bg/2.png)", "url(bg/1.png)", "url(bg/2.png)", "url(bg/1.png)"]; // changed
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
	<script language="JavaScript">
		function switch_recherche(a_cacher,a_voir)
			{
				document.getElementById(a_cacher).style.display = 'none';
				document.getElementById(a_voir).style.display = 'inline';
			}
		function switch_recherche2(a_selectionner,a_deselectionner)
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
				<span id="top-header-texte" >Vos vitres moins chères avec Allovitres, spécialiste de la vente de verre pas cher</span>
				<div id="top-header-droit"><div class="puce_caddie"><div class="puce_caddie_taille"><p class="puce_caddie_taille">5</p></div></div><img src="img/caddie.png"/>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="?page=creer_compte" title="connexion">SE CONNECTER</a></div>

			
			</div>
		</div>
		<div id="header">
			<img id="logo" src="img/logo.png" />
			<div id="header-droit">
				<div><img id="img-contactez-nous" src="img/contactez-nous.png" /><span id="nous-contacter">Nous contacter</span></div>
				<span id="telephone">0 892 72 11 13</span>
				<span id="telephone2">(0,34€TT/min)</span>
			</div>
			<div id="reseau-sociaux">
			<img class="rs-icon" src="img/RS-twitter.png" />
			<img class="rs-icon" src="img/RS-facebook.png" />
			<div>
		</div>
	</header>

	<nav>
		<div id="menu-separation-haut"></div>
		<div id='cssmenu' style="clear:both;">
			<ul>
				<li class='active'><a href='#'><span>verre simple</span></a></li>
				<li><a href='#'><span>double vitrage</span></a></li>
				<li><a href='#'><span>verre spécifique</span></a></li>
				<li class='last'><a href='#'><span>accessoires</span></a></li>
			</ul>
		</div>
		<div id="menu-separation-bas"></div>
	</nav>
	<div id="recherche">
		<div id="rech-conteneur-gauche" >
			<div id="rech-selecteur1" onmouseover="switch_recherche('rech-conteneur-droit2', 'rech-conteneur-droit1');switch_recherche2('rech-selecteur1','rech-selecteur2');">
				<span class="txt-recherche">RECHERCHE</span>
				<span class="txt-recherche2">Par type de verre</span>
			</div>
			<div id="rech-selecteur2" onmouseover="switch_recherche('rech-conteneur-droit1', 'rech-conteneur-droit2');switch_recherche2('rech-selecteur2','rech-selecteur1');">
				<span class="txt-recherche">RECHERCHE</span>
				<span class="txt-recherche2">Par type de projet</span>
			</div>
		</div>
		<div id="rech-conteneur-droit1">
			<ul>
				<li>
					<label>Type 1</label>
					<select id="select_type1">
						<option value="0">choix 1</option>
						<option value="1">choix 2</option>
					</select>
				</li>
				<li>
					<label>type 2</label>
					<select id="select_type2">
						<option value="0">choix 1</option>
						<option value="1">choix 2</option>
					</select>
				</li>
				<li>
					<label>Type 3</label>
					<select id="select_type3">
						<option value="0">choix 1</option>
						<option value="1">choix 2</option>
					</select>
				</li>
				<li>
					<label>type 4</label>
					<select id="select_type4">
						<option value="0">choix 1</option>
						<option value="1">choix 2</option>
					</select>
				</li>
			</ul>
			<img id="btn-rechercher" src="img/rechercher.png" />
		</div>
		<div id="rech-conteneur-droit2">
			
		</div>
	</div>