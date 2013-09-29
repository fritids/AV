{if !isset($smarty.session.is_logged)}
    <div id="titre-bloc">CONNEXION</div>
    <h3>COMPTE EXISTANT</h3>
    <form action="index.php?action=login" method="post">
        <input type="text" name="email">
        <input type="text" name="passwd">
        <input type="submit" class="bouton" name="b1" />
    </form>
{/if}
<form action="index.php?action=new_user" method="post">
    <div id="titre-bloc">CREATION DE COMPTE</div>
    <h3>INFORMATION DE COMPTE</h3>
    <label for="nom">Nom</label><input id="nom" name="lastname" type="text" value="{$user.lastname}" required="true"/><br />
    <label for="prenom">Prénom</label><input id="prenom" name="firstname" type="text"  value="{$user.firstname}" required="true"/><br />
    <label for="email">Adresse mail</label><input id="email" name="email" type="email" value="{$user.email}" required="true"/><br />
    <label for="mdp">Mot de passe</label><input id="mdp" name="passwd" type="text" required="true"/><br />
    <label for="tel">Numéro de téléphone</label><input id="tel" name="phone" type="tel" value="{$user.phone}" required="true"/><br />
    <label for="tel2">Numéro de téléphone 2</label><input id="tel2" name="phone_mobile" type="tel"  value="{$user.phone_mobile}"/><br />
    <h3 class="clear-it">INFORMATION DE FACTURATION & LIVRAISON</h3>
    <div id="facturation">
        <label for="adresse">Adresse de facturation *</label>
        <textarea id="adresse" name="invoice_address1" required="true">{$user.invoice.address1}</textarea><br />
        <label for="cp">Code Postal *</label>
        <input id="cp" name="invoice_postcode" type="text" value="{$user.invoice.postcode}" required="true" /><br />
        <label for="ville">Ville *</label>
        <input id="ville" name="invoice_country" type="text" value="{$user.invoice.country}" required="true"/><br />

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
