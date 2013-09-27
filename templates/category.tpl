<div class="largeur">
    <div class="bloc-titre">My categories</div>
    <div class="bloc-bas" style="height:400px">
        <ul>
            {foreach key=key item=product from=$products}
                <li><a href="?p&id={$product.id_product}"> {$product.name}</a></li>
                {/foreach}
        </ul>
    </div>
</div>