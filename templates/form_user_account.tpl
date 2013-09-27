<div class="bloc_page clear-it">

    <div class="clear-it" style="margin-top:5px;">
        <div id="bloc_page_gauche">
            <div id="titre-bloc">CONNEXION</div>
            <h3>COMPTE EXISTANT</h3>
            <form action="index.php?action=login" method="post">
                <input type="text" name="email">
                <input type="text" name="passwd">
                <input type="submit" class="bouton" name="b1" />
            </form>
            <form action="index.php?action=new_user" method="post">
                <div id="titre-bloc">CREATION DE COMPTE</div>
                <h3>INFORMATION DE COMPTE</h3>
                <label for="nom">Nom</label><input id="nom" name="lastname" type="text" value="{$user.lastname}" /><br />
                <label for="prenom">Prénom</label><input id="prenom" name="firstname" type="text"  value="{$user.firstname}" /><br />
                <label for="email">Adresse mail</label><input id="email" name="email" type="email" value="{$user.email}" /><br />
                <label for="mdp">Mot de passe</label><input id="mdp" name="passwd" type="text" /><br />
                <label for="tel">Numéro de téléphone</label><input id="tel" name="phone" type="tel" value="{$user.phone}" /><br />
                <label for="tel2">Numéro de téléphone 2</label><input id="tel2" name="phone_mobile" type="tel"  value="{$user.phone_mobile}"/><br />
                <h3 class="clear-it">INFORMATION DE FACTURATION & LIVRAISON</h3>
                <div id="facturation">
                    <label for="adresse">Adresse de facturation *</label>
                    <textarea id="adresse" name="invoice_address1" >{$user.invoice.address1}</textarea><br />
                    <label for="cp">Code Postal *</label>
                    <input id="cp" name="invoice_postcode" type="text" value="{$user.invoice.postcode}"/><br />
                    <label for="ville">Ville *</label>
                    <input id="ville" name="invoice_country" type="text" value="{$user.invoice.country}"/><br />

                </div>
                <div id="livraison">
                    <label for="LIV_adresse">Adresse de livraison *</label>
                    <textarea id="LIV_adresse"  name="delivery_address1">{$user.delivery.address1}</textarea><br />
                    <label for="LIV_cp">Code Postal *</label>
                    <input id="LIV_cp" name="delivery_postcode" type="text" value="{$user.delivery.postcode}"/><br />
                    <label for="LIV_ville">Ville *</label>
                    <input id="LIV_ville" name="delivery_country" type="text" value="{$user.delivery.country}"/><br />

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
                {assign var="total" value="0"}
                {foreach key=key item=product from=$cart}
                    <p>{$product.id} nom={$product.nom} qte= {$product.qte} prix = {$product.prix}</p> 
                    {$total =+ $product.prix}
                {/foreach}
                <hr />
                
                <p>
                    Expédition xx,00 €<br/>
                    Taxes xx,xx €<br/>
                    Total {$total} €<br/>
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

