	<div class="bloc_page">
	<p><span id="fil-ariane-page"><?php echo $fil_ariane; ?> <span>Identification</span></span></p>
	<div id="identification" class="bloc_page_gauche clear-it">
		<div class="page_titre">
			<span class="titre">Identification</span>
		</div>
		

		<div class="forms">
			<div class="creation form">
				<h3 class="indent">Créer un compte</h3>
				<p>Nulla vel consectetur risus, non placerat nisi. Mauris sit amet felis sit amet sapien porttitor eleifend vitae eget diam.</p>
				<p class="">
					<a href="" class="particulier">particulier</a>
					<a href="" class="pro">Professionnel</a>
				</p>
			</div>
			<div class="login form">
				<form action="" method="post">
				<h3 class="indent">J'ai Déja un compte un compte</h3>
					<div class="row">
						<label for="login">identifiant :</label><input class="text" type="text" name="login" id="login">
					</div>
					<div class="row">
						<label for="password">Mot de passe :</label><input class="text" type="password" name="password" id="password">
					</div>
					<p>
						<a href="" class="forget">Mot de passe oublié</a><input class="submit" type="submit" value="Se connecter">
					</p>
				</form>
			</div>
		</div>
		
	</div>

	<?php include_once 'pages/panier.php';?>
	<div class="clearfix"></div>
</div>