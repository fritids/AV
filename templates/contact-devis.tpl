<div id="bloc_page_gauche">
    <form action="/index.php?action=send_devis" method="post">
        <div id="titre-bloc">DEMANDE DE DEVIS</div>
        <h3>VOS INFORMATIONS</h3>
        <label for="nom">Nom</label><input id="nom" name="lastname" type="text" value="{$user.lastname}" required="true"/><br />
        <label for="prenom">Prénom</label><input id="prenom" name="firstname" type="text"  value="{$user.firstname}" required="true"/><br />
        <label for="tel">Tel</label><input id="email" name="tel" type="tel" value="{$user.invoice.phone}" required="true"/><br />
        <label for="email">Adresse mail</label><input id="email" name="email" type="email" value="{$user.email}" required="true"/><br />
        <h3 class="clear-it">VOTRE DEMANDE</h3>
        <textarea id="demande" name="demande" required="true" style="height:200px;width:500px;">{$user.demande}</textarea><br />
        <input type="submit" class="bouton" name="b1" />
    </form>
</div>