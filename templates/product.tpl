<script>

    $(document).ready(function($) {
        var square = 0;
        var weight = {$product.weight};
        var unit_price = {$product.price};


        $('#width').change(function() {
            qte = $('#quantity').val();
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();
            square = (pwidth * pheigth) / 10000;
            $('#surface').val(square.toFixed(2));
            $('#total_poids').val((square.toFixed(2) * weight * qte).toFixed(2));
            $('#total_price').val((square.toFixed(2) * unit_price * qte).toFixed(2));

        });

        $('#heigth').change(function() {
            qte = $('#quantity').val();
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();
            square = (pwidth * pheigth) / 10000;
            $('#surface').text(square.toFixed(2));
            $('#total_poids').text((square.toFixed(2) * weight * qte).toFixed(2));
            $('#total_price').text((square.toFixed(2) * unit_price * qte).toFixed(2));
        });

        $('#quantity').change(function() {
            qte = $('#quantity').val();
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();

            square = (pwidth * pheigth) / 10000;
            $('#surface').text(square.toFixed(2));
            $('#total_poids').text((square.toFixed(2) * weight * qte).toFixed(2));
            $('#total_price').text((square.toFixed(2) * unit_price * qte).toFixed(2));
        });
    });

</script>

<div id="produit" class="bloc_page_gauche clear-it">    

    <p class="rouge">Délais de livraisons entre 4 à 5 semaines concernant ce produit.</p>

    <h1>{$product.name}</h1>

    <div id="features">
        <div class="images">
            {*
            {literal}
                <div class="cycle-slideshow"
                     data-cycle-timeout=0
                     data-cycle-pager="#custom-pager"
                     data-cycle-pager-template='<a href="#" ><img src="{{src}}" width=95 height=95></a>'
                     >
                {/literal}

                {if isset($product.images)}
                    {foreach key=key item=image from=$product.images}
                        <img src="img/{$image.filename}" />                    
                    {/foreach}
                {/if} 

            </div>
            *}
            <div id="custom-pager"></div>
        </div>	
        <form action="?cart" method="post">
            <div class="features">
                <div class="separ clearfix">
                    <div class="infos">
                        <p class="prix" ><span id="total_price">{$product.price}</span> €</p>
                        <p><span id="surface"></span> m² calculé</p>
                        <p><span id="total_poids"></span> kg calculés</p>
                    </div>
                    <div class="add_to_cart">

                        <input type="hidden" name="id_product" value="{$product.id_product}">
                        <input type="hidden" name="add">
                        <label for="qty">Quantité :</label>
                        <input type="text" class="qte" name="quantity" id ="quantity" value="1">
                        <input type="submit" value="Ajouter au panier" class="indent submit">

                    </div>
                </div>

                <div class="clearfix"></div>
                <p class="ref">Réference:{$product.reference} <img src="img/sans-frais.png" style="margin-left: 25px;" alt=""></p>

                {if isset($product.attributes)}
                    <div class="row clearfix">
                        <label for="faconnage">Façonnage</label>
                        <select name="options" id="faconnage">
                            {foreach key=key item=option from=$product.attributes}
                                <option value='{$option.id_product_attribute}'>
                                    {$option.name} 
                                </option>                            
                            {/foreach}
                        </select>
                    </div>
                {/if}
                <div class="row clearfix">
                    <label for="width">Largeur</label>
                    <input type="text" id ="width" name="width" value="" class="text">
                    <span class="info">de {$product.min_width} à {$product.max_width} mm</span>
                </div>	
                <div class="row clearfix">
                    <label for="height">Longeur</label>
                    <input type="text" id ="heigth" name="height" value="" class="text">
                    <span class="info">de {$product.min_height} à {$product.max_height} mm</span>
                </div>                
            </div>   	
        </form>
    </div>

    <div class="clearfix"></div>

    <div class="share">
        <a href="" class="indent imprimer">Imprimer</a>
        <a href="" class="indent envoyer-a-un-ami">Envoyer a un ami</a>
    </div>


    <h3 class="paragraphe-titre">Caractéristiques techniques -  {$product.name} <a href="#" class="top"></a></h3>
    <p class="produit-menu"><a href="">Produits complémentaires</a>
        | <a href="">Descriptif du produit</a>
        | <a href=""> Nos conseils de pose en vidéo</a>
        | Caractéristiques techniques</p>

    <div class="caracteristique">
        {foreach key=key item=caracts from=$product.caracteristiques}
            <div class="row">{$caracts.caract_name}</div>
            <div class="row odd">{$caracts.caract_value}</div>
        {/foreach}    
    </div>

    <h3 class="paragraphe-titre">DESCRITPIF DU PRODUIT : {$product.name} <a href="#" class="top"></a></h3>
    <p class="produit-menu"><a href="">Produits complémentaires</a>
        | <a href="">Descriptif du produit</a>
        | <a href=""> Nos conseils de pose en vidéo</a>
        | Caractéristiques techniques</p>
</p>
<div class="desc">
    {$product.description}
</div>

<h3 class="paragraphe-titre">Nos conseils de pose en vidéo  <a href="#" class="top"></a></h3>
<p class="produit-menu"><a href="">Produits complémentaires</a>
    | <a href="">Descriptif du produit</a>
    | <a href=""> Nos conseils de pose en vidéo</a>
    | Caractéristiques techniques</p>
</p>
<div style="width:560px;margin:0 auto">
    <iframe align="middle" width="560" height="315" src="{$product.video}" frameborder="0" allowfullscreen></iframe>
</div>

<h3 class="paragraphe-titre">Julie D’ ALLOVITRES vous conseille ces produits complémentaires  <a href="#" class="top"></a></h3>
<p class="produit-menu"><a href="">Produits complémentaires</a>
    | <a href="">Descriptif du produit</a>
    | <a href=""> Nos conseils de pose en vidéo</a>
    | Caractéristiques techniques</p>
</p>

<div class="complement">
    <div class="produit first">
        <img src="img/product1.jpg" alt="">
        <h3 class="titre">Miroir argenté 3 mm</h3>
        <p class="prix">30€</p>
        <p class="liens">
            <a href="" class="panier indent">Panier</a>
            <a href="" class="voir indent">Voir</a>
        </p>
    </div>
    <div class="produit">
        <img src="img/product1.jpg" alt="">
        <h3 class="titre">Miroir argenté 3 mm</h3>
        <p class="prix">30€</p>
        <p class="liens">
            <a href="" class="panier indent">Panier</a>
            <a href="" class="voir indent">Voir</a>
        </p>
    </div>
    <div class="produit">
        <img src="img/product1.jpg" alt="">
        <h3 class="titre">Miroir argenté 3 mm</h3>
        <p class="prix">30€</p>
        <p class="liens">
            <a href="" class="panier indent">Panier</a>
            <a href="" class="voir indent">Voir</a>
        </p>
    </div>			
</div>
</div>
