<div id="slider">
</div>
<div class="largeur">
    <div class="bloc-titre"><h1 style="font-family: Arial;font-size: 14px;display:inline;">ALLOVITRES : Spécialiste de la miroiterie</h1></div>
    <div style="float: left;background: #fff;width: 100%">
        <div class="bloc-bas promotions">
            <a href="?promotions"> <h3 id="promotions" class="indent">Promotions</h3></a>
            <p>Profitez du meilleur prix et de nos promos sur le verre laqué en couleur, les miroirs avec film anti-éclats et le double vitrage avec ou sans gaz argon.</p>
            {foreach key=key item=product from=$promoshome name="promo"}
                <div class="produit {if $smarty.foreach.promo.first}first{/if}">
                    <span class="promo"></span>
                    <a href="{$categorie.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html">
                        <img src="/img/p/{$product.cover.filename}" width="147">
                    </a>
                    <h3 class="titre">{$product.name}</h3>
                    <p class="prix">
                        {if $product.is_promo ==1 && $product.price_orig > 0} 
                            <span style="text-decoration: line-through;font-size: 25px;"> {($product.price_orig*$config.vat_rate)|round:"2"} €</span><br>
                        {/if}
                        {($product.price*$config.vat_rate)|round:2} €

                    </p>
                    <p class="liens">
                        {*<a href="" class="panier indent">Panier</a>*}
                        <a href="{$categorie.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html"" class="voir indent">Voir</a>
                    </p>
                </div>                
            {/foreach}
        </div>

        <a href="/double-vitrage-sur-mesure/54-double-vitrage-avec-gaz-argon.html">
            <div class="bloc-bas produit-du-mois">
                <h3 class="indent">Produit du mois</h3>
                <div class="produit">
                    <div class="infos">
                        <img src="/img/zoom.png" alt="">
                        <p class="desc"><span class="blue">DOUBLE VITRAGE</span><br>avec gaz argon</p>
                        <p class="prix">à partir de <span class="blue">67,91 €</span></p>
                        
                    </div>
                    <img src="/img/miroir.png" alt="">
					
                    <p class="therme clearfix"><img src="/img/thermique.png" alt="" style="text-align: right;">Avantages thermiques</p>
					
                </div>
            </div>
        </a>
    </div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="largeur">

    <div class="bloc-bas">
        {*
        <img src="/img/Livraison-partout-en-France.png" alt="" style="margin-right: 15px;">
        <img src="/img/Service-de-pose.png" alt="">
        *}
        <p class="clearfix"></p>
        <div class="blocks">

            <div class="partenaires">
                <!--<img src="/img/bohle.png" alt="" style="margin: 0px auto; display: block;">-->
				<table >
					<tr>
						<td style="padding:10px;font-weight:200;">
						<a href="http://www.allovitres.com/content/16-prise-de-mesure-et-pose-d-un-vitrage-sur-fenetre-pvc">
							<span style="font-weight:normal;">
							- PRISE DE MESURES ET POSE D'UNE VITRE SUR UNE FENÊTRE PVC
							</span>
						</a>
						</td>
					</tr>
					<tr>
						<td style="padding:10px;">
							<a href="http://www.allovitres.com/content/19-comment-mesurer-une-vitre">
								<span style="font-weight:normal;">
								- PRISE DE MESURE DE VOTRE VITRE
								</span>
								</a>
						</td>			
					</tr>
					<tr>
						<td style="padding:10px;">
							<a href="http://www.allovitres.com/content/18-comment-poser-une-vitre-simple-vitrage-ou-double-vitrage-sur-fenetre-bois">
								<span style="font-weight:normal;">
								- POSE DE VOTRE SIMPLE OU DOUBLE VITRAGE SUR UNE FENÊTRE BOIS
								</span>
							</a>
						</td>			
					</tr>
					<tr>
						<td style="padding:10px;">
							<a href="http://www.allovitres.com/content/20-foire-aux-questions">
								<span style="font-weight:normal;">
								- FOIRE AUX QUESTIONS
								</span>
							</a>
						</td>			
					</tr>
				</table>
            </div>
            <div class="videos">
                <p class="center">Suivez étapes par étapes le montage de vos vitres et miroirs grâce à de <span class="blue">vrais conseils de pros !</span></p>

                <img src="/img/youtube-thumb.png" alt="" style="margin: 0px auto; display: block;">

                <p style="margin-top: 0;"><a href="" class="plus">Voir toutes nos vidéos</a> <img src="/img/youtube-logo.png" style="float: right; position: relative; right: 10px;" alt=""></p>
            </div>
            <div class="formes">
                <p>Votre vitrage n’est pas rectangulaire ?</p>
                <img src="/img/formes.png" alt="" style="margin: 0px auto; display: block;">
                <p style="margin-top: 0;"><a href="">Cliquez ici pour renseigner votre forme spécifique</a></p>
            </div>
        </div>


        <p class="clearfix"></p>



        <div id="editorial_block_center" class="editorial_block">
            <a href="http://www.allovitres.com" title="AlloVitres, vitrage sur mesure, vitres et miroirs en ligne pas cher">		</a>		<h1>AlloVitres, vitrage sur mesure, vitres et miroirs en ligne pas cher</h1>	<h2>Leader de la vente de verres sur-mesure sur internet</h2>	<div class="rte"><p><span style="color: #3366ff;"><h3>ALLOVITRES, SPECIALISTE DE LA VENTE EN LIGNE DE VERRE, VITRES ET MIROIRS</h3></span></p>
                <p style="text-align: justify;"><br /><span style="color: #333333;">Acheter des vitres, du verre et des mliroirs en ligne sur internet, c'est avant tout une question de confiance.</span><span style="color: #333333;"><img style="float: right;" src="/img/vehicule.png" alt="vehicule allovitres" width="600" height="249" /></span></p>
                <p style="text-align: justify;"><span style="color: #333333;"><strong>Acheter des vitres</strong>, des <strong>baies vitrée</strong>s et des <strong>miroirs</strong> sur le site internet </span><span style="color: #888888;"><strong>Allovitres</strong></span><span style="color: #333333;">, c'est acheter son verre beaucoup moins cher et en toute simplicité. Installer soi-même ses vitres ou changer une baie vitrée ou vitre cassée en toute sécurité, c'est possible avec l'aide de l'assistance technique du <strong>miroitier <strong>vitrier </strong>Allovitres</strong>.<br /><br /><strong></strong></span></p>
                <p style="text-align: justify;"><span style="color: #333333;"><strong>Le site internet Allovitres est le </strong>spécialiste de la <strong>vente en ligne de verre, de vitrages </strong>et de<strong> miroirs pas chers. A</strong>vec un large choix de produits verriers comme les vitres <strong>simple vitrage</strong>, le <strong>double vitrage</strong>, le <strong>verre feuilleté</strong>, le verre armé, le <strong>verre trempé sécurit</strong>, le <strong>miroir</strong> sans tain, le verre teinté, le <strong>verre laqué</strong> mais aussi tous les verres décoratifs pour les <strong>crédences de cuisine,</strong> les <strong>parois et portes de douche</strong> en verre et les vitres vitrocéramiques pour inserts et cheminées, Allovitres se positionne aujourd'hui comme le l<span>eader <span>en France</span> des ventes de <strong>verre et vitrage sur mesure</strong> sur Internet</span>.</span></p>
                <p style="text-align: justify;"><span style="color: #333333;"><br />Profitez du meilleur prix et de nos promos sur le verre laqué en couleur, les <strong>miroirs avec film anti-éclats et le double vittrage avec ou sans gaz argon</strong>. </span></p>
                <p style="text-align: justify;"><span style="color: #333333;"><strong><br /></strong></span></p>
                <p style="text-align: justify;"><span style="color: #333333;"><strong>Allovitres</strong> intervient également pour vos<strong> vitres cassées</strong> dans tout le département des Bouches du Rhône 13, à <strong>Marseille</strong>, Aix en Provence, Aubagne et Martigues et toutes les villes alentours.</span></p>
                <p style="text-align: justify;"><span style="color: #000000;">Vous êtes rétissant à acheter vos<strong> vitres en ligne</strong> ? Nos conseillers miroitiers<strong> Allovitres</strong> sont des profesionnels du verre et de la <strong>vitrerie</strong> et sont disponibles par téléphone ou email pour répondre à toutes vos questions du lundi au vendredi de 9h à 12h et de 14h à 18h. Ils sauront vous aiguiller dans le choix du verre le mieux adapté à vos besoins.</span></p>
                <p style="text-align: justify;"><span style="color: #000000;">Le verre présente de très nombreuses spécificités comme les traitements <strong>trempé sécurit</strong>, le <strong>feuilleté</strong>, le <strong>laqué</strong>, le <strong>sablé</strong>, le <strong>dépoli à l'acide</strong>, le <strong>décoré</strong>, <strong>l'imprimé</strong>, le teinté, le sans tain, et bien d'autres encore en fonction de vos besoins techniques ou décoratifs. </span></p>
                <p style="text-align: justify;"><span style="color: #000000;">Les agents techniques de la miroiterie Allovitres vous aident à choisir les spécificités dont vous avez besoin et peuvent vous accompagner dans la prise de dimension ou la pose. Le miroitier <strong>Allovitres peut aussi </strong>vous proposer de mettre en sécurité une vitre cassée, ou de remplacer votre vitre dans les plus brefs délais et ce, dans le cadre de la prise en charge de votre<strong> assurance habitation "bris de verre"</strong>.</span></p>
                <p style="text-align: justify;"><span style="color: #000000;"><strong>La miroiterie Allovitres.com </strong>est aussi la<strong> </strong>garantie d'un verre de qualité, d'une livraison rapide et pas cher (à partir de 25 € pour une livraison dans toute la France) et la facilité de la<strong> vente en ligne</strong>.</span></p>
                <p><span style="color: #000000;"><strong>Le miroitier Allovitres</strong> s'engage également à vous livrer votre commande de <strong>vitre, verre ou miroir</strong><span style="font-family: Arial; font-size: 12px; font-style: normal; line-height: normal;"><strong> </strong>dans tous les départements de France <strong>à partir de</strong></span><strong style="font-family: Arial; font-size: 12px; font-style: normal;"> 25€</strong><span style="font-family: Arial; font-size: 12px; font-style: normal; line-height: normal;">.</span></span></p>
                <p><span style="color: #000000;">En effet, la<strong> miroiterie Allovitres</strong> vous livre votre commande de <strong>verre, miroir, vitrage ou vitre</strong> dans toutes les régions et départements de France <strong>à partir de 25 euros TTC</strong>. Toutes les livraisons de verre s’effectuent en pied d’immeuble.</span></p>
                <p><span style="color: #000000;"><strong>Allovitres </strong>assure<strong> </strong>les<strong> livraisons de verre </strong>aussi bien dans les départements de la région de <strong>Marseille dans les </strong><strong>Bouches du Rhône</strong>, le <strong>Vaucluse</strong> et sur la zone de <strong>Paris</strong> et <strong>région parisienne</strong>, la région <strong>Rhône Alpes</strong> de<strong>Lyon</strong> et ses alentours, mais aussi dans les régions du <strong>Nord</strong>, de la <strong>Normandie</strong>, de la <strong>Bretagne </strong>et de la<strong> Loire Atlantique</strong>.</span></p>
                <p><span style="color: #000000;"><strong>Allovitres</strong> met tout en œuvre pour vous garantir une livraison de votre <strong>verre</strong> et de vos <strong>vitres</strong> <strong>dans toute la France sous 7 à 15 jours ouvrés</strong> (Délais moyens hors samedi, dimanche et jours fériés en fonction des zones géographiques de livraison).</span></p>
                <p style="text-align: justify;"><span style="color: #000000;">La recherche du <strong>verre sur mesur</strong>e dont vous avez besoin est simplifiée. Vous sélectionnez directement le produit que vous voulez acheter, vous y indiquez les dimensions précises qu'il vous faut, les spécificités à appliquer et le façonnage souhaité comme des <strong>bords polis non coupants</strong>, les traitements sécurit pour la solidité et la sécurité en cas de <strong>bris de verre,</strong> et vous obtenez votre<strong> meilleur prix </strong>grâce à notre calculateur en ligne.</span></p>
                <p style="text-align: justify;"><span style="color: #000000;">Acheter des<strong> vitres double vitrage ou simple vitrage en lign</strong>e chez le vitrier<strong> Allovitres</strong> devient la solution économique pas cher, pratique et confortable pour commander vos vitres et baies vitrées beaucoup <strong>moins cher</strong> qu'ailleurs et en toute sérénité.</span></p></div></div>




        {*		
        <div class="carre carre-orange">Le site internet Allovitres est le spécialiste de la vente en ligne de verre, de vitrages et de miroirs pas chers. Avec un large choix de produits verriers comme les vitres simple vitrage, le double vitrage, le verre feuilleté, le verre armé, le verre trempé sécurit, le miroir sans tain, le verre teinté, le verre laqué mais aussi tous les verres décoratifs pour les crédences de cuisine, les parois et portes de douche en verre et les vitres vitrocéramiques pour inserts et cheminées, Allovitres se positionne aujourd'hui comme le leader en France des ventes de verre et vitrage sur mesure sur Internet.</div>
        <p class="clearfix separ"></p>
        <div class="carre carre-vert">Le site internet Allovitres est le spécialiste de la vente en ligne de verre, de vitrages et de miroirs pas chers. Avec un large choix de produits verriers comme les vitres simple vitrage, le double vitrage, le verre feuilleté, le verre armé, le verre trempé sécurit, le miroir sans tain, le verre teinté, le verre laqué mais aussi tous les verres décoratifs pour les crédences de cuisine, les parois et portes de douche en verre et les vitres vitrocéramiques pour inserts et cheminées, Allovitres se positionne aujourd'hui comme le leader en France des ventes de verre et vitrage sur mesure sur Internet.</div>
        <p class="clearfix separ"></p>
        <div class="carre carre-blue">Le site internet Allovitres est le spécialiste de la vente en ligne de verre, de vitrages et de miroirs pas chers. Avec un large choix de produits verriers comme les vitres simple vitrage, le double vitrage, le verre feuilleté, le verre armé, le verre trempé sécurit, le miroir sans tain, le verre teinté, le verre laqué mais aussi tous les verres décoratifs pour les crédences de cuisine, les parois et portes de douche en verre et les vitres vitrocéramiques pour inserts et cheminées, Allovitres se positionne aujourd'hui comme le leader en France des ventes de verre et vitrage sur mesure sur Internet.</div>
        *}  
    </div>
    <div class="bloc-ombre" style="height:0px;"></div>
</div>
<br/>