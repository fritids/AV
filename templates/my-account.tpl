<p><span id="fil-ariane-page"><?php echo $fil_ariane; ?> <span>Compte Personnel</span></span></p>
<div id="moncompte" class="clear-it">
    <h3 id="titre">Compte Personnel</h3>
    <p>Bienvenue sur votre page de compte personnel. Vous pouvez gérer vos informations personnelles, vos commandes ainsi que vos adresses.</p>

    <div class="blocks">
        <div class="block avoirs">
            <h3>MES AVOIRS</h3>
            <p>Vérifiez vos avoirs et/ou bons de réduction</p>
        </div>
        
        <a href="/?devis">
            <div class="block devis">
                <h3>MES DEVIS</h3>
                <p>Vous avez <span class="notif">{$mydevis|count}</span> devis en attente.</p>
            </div>
        </a> 

        <a href="/?orders-list">
            <div class="block commandes">
                <h3>HISTORIQUE DE MES COMMANDES</h3>
                <p>Consultez l’état de vos commandes</p>
            </div>
        </a>

        <a href="/?register">
            <div class="block infos">
                <h3>MES INFORMATIONS PERSOS</h3>
                <p><a href="/?register">Modifiez vos informations personnelles</a></p>
            </div>
        </a>
        <a href="/?register">
            <div class="block adresses">
                <h3>MES ADRESSES</h3>
                <p>Modifiez vos adresses de livraison ou facturation</p>
            </div>
        </a>

        <div class="block parrainage">
            <h3>PARRAINAGE</h3>
            <p>Parrainez des proches et bénéficiez d’offres exclusives</p>
        </div>
    </div>
</div>

<div class="clearfix"></div>