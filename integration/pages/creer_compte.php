	<div class="bloc_page clear-it">
		<span id="fil-ariane-page"><?php echo $fil_ariane; ?> <span>CREATION DE COMPTE</span></span>
		<div class="clear-it" style="margin-top:5px;">
			<div id="bloc_page_gauche">
			<form>
			<div id="titre-bloc">CREATION DE COMPTE</div>
				 <h3>INFORMATION DE COMPTE</h3>
				 <label for="nom">Nom</label><input id="nom" name="nom" type="text" /><br />
				 <label for="prenom">Prénom</label><input id="prenom" name="prenom" type="text" /><br />
				 <label for="email">Adresse mail</label><input id="email" name="email" type="email" /><br />
				 <label for="mdp">Mot de passe</label><input id="mdp" name="mdp" type="text" /><br />
				 <label for="tel">Numéro de téléphone</label><input id="tel" name="tel" type="tel" /><br />
				 <label for="tel2">Numéro de téléphone 2</label><input id="tel2" name="tel2" type="tel" /><br />
				 <h3 class="clear-it">INFORMATION DE FACTURATION & LIVRAISON</h3>
				 <div id="facturation">
					 <label for="adresse">Adresse de facturation *</label>
					 <textarea id="adresse"></textarea><br />
					<label for="cp">Code Postal *</label>
					 <input id="cp" name="cp" type="text" /><br />
					 <label for="ville">Ville *</label>
					 <input id="ville" name="ville" type="text" /><br />
	
				 </div>
				 <div id="livraison">
					 <label for="LIV_adresse">Adresse de livraison *</label>
					 <textarea id="LIV_adresse" disabled></textarea><br />
					 <label for="LIV_cp">Code Postal *</label>
					 <input disabled id="LIV_cp" name="cp" type="text" /><br />
					 <label for="LIV_ville">Ville *</label>
					 <input disabled id="LIV_ville" name="ville" type="text" /><br />
	
				 </div>
				 <div id="creer-cpt-checking">
				 <input id="liv" name="liv" type="checkbox" />Cochez si votre adresse de livraison est différente de l’adresse de facturation.<br />
				 <input id="cgv" name="cgv" type="checkbox" />J’ai lu et j’accepte les conditions générales de vente.<br />
				 </div>
				 <input type="submit" class="bouton" name="b1" />
			</form>
			</div>
			<div id="bloc_page_droite">
			<div id="panier">
				<span id="panier-titre">PANIER</span>
				<hr />	
				<p>
				1x VERRE TRI... 299,00 €<br/>
				10.10.10/4 (30 mm), Joints polis autour, 1000, 1000
				</p>
				<hr />	
				<p>
				Expédition 25,00 €<br/>
				Taxes 53,10 €<br/>
				Total 324,00 €<br/>
				Les prix sont TTC <br/>
				</p>
				<a class="bouton" href="">Panier</a><a class="bouton" href="">Commander</a>
			</div>
			<div class="bloc-droite">
			<h2>NOS SERVICES</h2>
			<ul>
				<li><img src="img/b1.png" />Devis Spécifique</li>
				<li><img src="img/b2.png" />Découpe sur mesure</li>
				<li><img src="img/b3.png" />Service de pose</li>
				<li><img src="img/b4.png" />Livraison</li>
				<li><img src="img/b5.png" />Nos conseils Videos</li>
			</ul>
			</div>
			</div>
		</div>
		
		
	</div>