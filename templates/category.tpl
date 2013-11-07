
<div id="categorie" class="bloc_page_gauche clear-it">
    <div class="page_titre">
        <span class="titre">{$categorie.name}</span>
        <span class="products_number">Il y a {$nb_produits} produits correspondants à votre recherche</span>
    </div>

    <div class="categorie_desc">
        {$categorie.description}
    </div>

    <div class="categorie_slider">&nbsp;</div>
    <div class="verre-categorie">
        <a href=""><img src="img/verre-vetroceramique.png" alt=""></a>
        <a href=""><img src="img/verre-tri-feuillete.png" alt=""></a>
        <a href=""><img src="img/verre-feuille.png" alt=""></a>
        <a href=""><img src="img/verre-arme.png" alt=""></a>
    </div>

    <div class="clearfix"></div>
    {*
    <p id="pager">
    <span class="active">1</span>
    <a href="">2</a>
    </p>
    *}
    {foreach key=key item=product from=$products}
        <div class="item">
            <div class="img block">
                <a href="?p&id={$product.id_product}"><img src="img/p/{$product.cover.filename}" alt="" width="140"></a>
            </div>
            <div class="desc block">
                <h3>{$product.name}</h3>
                <p>{$product.description_short}</p>
            </div>
            <div class="prix block">
                <span>{$product.price} €</span>
                <a href="?p&id={$product.id_product}" class="indent">Voir le produit</a>
            </div>

            <div class="clearfix"></div>
        </div>
    {/foreach}
    
    {*
    <p id="pager">
    <span class="active">1</span>
    <a href="">2</a>
    </p>
    
    <p class="page_number">Page 1/2</p>
    *}
</div>
