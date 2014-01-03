
<div id="categorie" class="bloc_page_gauche clear-it">
    <div class="page_titre">
        <span class="titre">Recherche</span>
        <span class="products_number">Il y a {$search_result|count} produits correspondants à votre recherche</span>
    </div>

    <div class="clearfix"></div>

    {foreach key=key item=product from=$search_result}   
        <div class="item">
            <table>
                <tr>
                    <td>
                        <div class="img block" style="display:inline-block;vertical-align:middle;">
                            <a href="/{$product.category.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html"><img src="/img/p/{$product.cover.filename}" alt="{$product.name}" width="140"></a>
                        </div>
                    </td>
                    <td>
                        <div class="desc block">
                            <h3><a href="/{$product.category.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html">{$product.name}</a></h3>
                            <p>{$product.description_short}</p>
                        </div>
                        <div class="prix block">
                            <span>
                                {($product.price*$config.vat_rate)|round:"2"} €
                                {if $product.is_promo ==1} 
                                    <span style="text-decoration: line-through;font-size: 15px;
                                          color: red;"> {($product.price_orig*$config.vat_rate)|round:"2"} €</span>
                                {/if}
                            </span>
                            {if $product.id_category != 19 && !($product.width && $product.height)}
                                <span style="font-size: 14px;font-weight: none;">Prix au m²</span>
                            {/if}
                            <a href="/{$product.category.link_rewrite}/{$product.id_product}-{$product.link_rewrite}.html" class="indent">Voir le produit</a>
                        </div>
                        <div class="clearfix"></div>
                    </td>
                </tr>
            </table>
        </div>

    {/foreach}

</div>
