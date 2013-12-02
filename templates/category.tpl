
<div id="categorie" class="bloc_page_gauche clear-it">
    <div class="page_titre">
        <span class="titre">{$categorie.name}</span>
        <span class="products_number">Il y a {$nb_produits} produits correspondants à votre recherche</span>
    </div>

    <div class="categorie_desc">
        {$categorie.description}
    </div>

    <div class="categorie_slider">

        {if !isset($categorie.image)}
            <img src="/img/categorie.jpg"/>
        {else}
            <img src="/img/c/{$categorie.image}" alt="{$categorie.name}" width="673" height="246" >
        {/if}
    </div>
    {*    <div class="verre-categorie">
    <a href=""><img src="/img/verre-vetroceramique.png" alt=""></a>
    <a href=""><img src="/img/verre-tri-feuillete.png" alt=""></a>
    <a href=""><img src="/img/verre-feuille.png" alt=""></a>
    <a href=""><img src="/img/verre-arme.png" alt=""></a>
    </div>
    *}
    <div class="clearfix"></div>
    {*
    <p id="pager">
    <span class="active">1</span>
    <a href="">2</a>
    </p>
    *}

    {foreach key=key item=product from=$products}
        <div class="item">
            <table>
                <tr>
                    <td>
                        <div class="img block" style="display:inline-block;vertical-align:middle;">
                            <a href="/{$categorie.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html"><img src="/img/p/{$product.cover.filename}" alt="{$product.name}" width="140"></a>
                        </div>
                    </td>
                    <td>
                        <div class="desc block">
                            <h3><a href="/{$categorie.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html">{$product.name}</a></h3>
                            <p>{$product.description_short}</p>
                        </div>
                        <div class="prix block">
                            <span>{$product.price} €</span>
							<span style="font-size: 14px;font-weight: none;">Prix au m²</span>
                            <a href="/{$categorie.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html" class="indent">Voir le produit</a>
                        </div>
                        <div class="clearfix"></div>
                    </td>
                </tr>
            </table>
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
