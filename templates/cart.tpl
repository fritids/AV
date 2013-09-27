<div class="largeur">
    <div class="bloc-titre">Panier</div>
    <div class="bloc-bas" style="height:400px">
        {foreach key=key item=product from=$cart}
            <li>{$product.id} nom={$product.nom} qte= {$product.qte} prix = {$product.prix}
                <form action="?cart" method="post">
                    <input type="hidden" name="id_product" value="{$product.id}">
                    <input type="hidden" name="qte" value="{$product.qte}">
                    <input type="hidden" name="del">
                    <input type="submit" value="Retirer" >
                </form> 
            </li> 
        {/foreach}
    </div>
</div>
