<script>
    function validatePassword() {
        if ($("#mdp").val() != $("#mdp2").val()) {
            alert("Le mot de passe ne correspond pas");
            //more processing here
            return(false);
        }
    }
</script>
<div id="bloc_page_gauche">
    <form action="/index.php?action=new_user" method="post" onsubmit="return  validatePassword();">
        {if isset($smarty.session.is_logged) && $smarty.session.is_logged}
            <div id="titre-bloc">MODIFICATION DE COMPTE</div>
        {else}
            <div id="titre-bloc">CREATION DE COMPTE</div>
        {/if}
        <h3>INFORMATION DE COMPTE</h3>
        <label for="nom">Nom</label><input id="nom" name="lastname" type="text" value="{$user.lastname}" required="true"/><br />
        <label for="prenom">Prénom</label><input id="prenom" name="firstname" type="text"  value="{$user.firstname}" required="true"/><br />
        <label for="email">Adresse mail</label><input id="email" name="email" type="email" value="{$user.email}" required="true"/><br />
        <label for="mdp">Mot de passe</label><input id="mdp" name="passwd" type="password" required="true"/><br />
        <label for="mdp2">Verification mot de passe</label><input id="mdp2" name="passwd2" type="password" required="true"/><br />
        <h3 class="clear-it">INFORMATION DE FACTURATION & LIVRAISON</h3>
        <div id="facturation">
            <label for="tel">Numéro de téléphone</label>
            <input id="tel" name="invoice_phone" type="tel" value="{$user.invoice.phone}" required="true" /><br />
            <label for="tel2">Numéro de téléphone 2</label>
            <input id="tel2" name="invoice_phone_mobile" type="tel"  value="{$user.invoice.phone_mobile}" /><br />

            <label for="adresse">Adresse de facturation *</label>
            <textarea id="adresse" name="invoice_address1" required="true">{$user.invoice.address1}</textarea><br />
            <label for="cp">Code Postal *</label>
            <input id="cp" name="invoice_postcode" type="text" value="{$user.invoice.postcode}" required="true" {literal}pattern="[0-9]{5}"{/literal}/><br />
            <label for="ville">Ville *</label>
            <input id="ville" name="invoice_city" type="text" value="{$user.invoice.city}" required="true"/><br />

        </div>
        <div id="livraison">
            <label for="tel">Numéro de téléphone</label>
            <input id="tel" name="delivery_phone" type="tel" value="{$user.delivery.phone}" /><br />
            <label for="tel2">Numéro de téléphone 2</label>
            <input id="tel2" name="delivery_phone_mobile" type="tel"  value="{$user.delivery.phone_mobile}" /><br />

            <label for="LIV_adresse">Adresse de livraison *</label>
            <textarea id="LIV_adresse"  name="delivery_address1">{$user.delivery.address1}</textarea><br />
            <label for="LIV_cp">Code Postal *</label>
            <input id="LIV_cp" name="delivery_postcode" type="text" value="{$user.delivery.postcode}"  {literal}pattern="[0-9]{5}"{/literal}/><br />
            <label for="LIV_ville">Ville *</label>
            <input id="LIV_ville" name="delivery_city" type="text" value="{$user.delivery.city}"/><br />

        </div>
        <div id="creer-cpt-checking">
            <input id="liv" name="liv" type="checkbox" />Cochez si votre adresse de livraison est différente de l’adresse de facturation.<br />
            {if !isset($smarty.session.is_logged)}
                <input id="cgv" name="cgv" type="checkbox" required="required" />J’ai lu et j’accepte les conditions générales de vente.<br />
            {/if}
        </div>
        <input type="submit" class="bouton" name="b1" />
    </form>
</div>