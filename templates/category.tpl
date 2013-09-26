<div class="largeur">
    <div class="bloc-titre">My categories</div>
    <div class="bloc-bas" style="height:400px">
        {foreach key=key item=product from=$products}
            <a href="?p={$product.id_product}">{$product.name}<a>
        {/foreach}
    </div>
</div>