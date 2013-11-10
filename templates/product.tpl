
<div id="produit" class="bloc_page_gauche clear-it">    
    <p class="rouge">{$product.msg_dispo}</p>
    <h1>{$product.name}</h1>
    <div id="texte"></div>
    <div id="features">
        <div class="images">
            {literal}
                <div class="cycle-slideshow"
                     data-cycle-timeout=0
                     data-cycle-pager="#custom-pager"
                     data-cycle-pager-template='<a href="#" ><img src="/{{src}}" width=95 height=95></a>'
                     >
                {/literal}
                <img src="/img/p/{$product.cover.filename}" width="325" />  

                {if isset($product.images)}
                    {foreach key=key item=image from=$product.images}
                        <img src="/img/p/{$image.filename}" width="325" />                    
                    {/foreach}
                {/if}
            </div>
            <div id="custom-pager"></div>
        </div>	
        <form action="/?cart" method="post" id="validation">
            <div class="features">
                <div class="separ clearfix">
                    <div class="infos">
                        <p class="prix" ><span id="total_price">{$product.price}</span> €</p>
                        {if $product.id_category !=19}
                            <p><span id="surface"></span> m² calculé</p>
                            <p><span id="total_poids"></span> kg calculés</p>
                        {/if}
                    </div>
                    <div class="add_to_cart">

                        <input type="hidden" name="id_product" value="{$product.id_product}">
                        <input type="hidden" name="add">
                        <input type="hidden" name="price" id="price" value="">
                        <label for="qty">Quantité :</label>
                        <input type="text" class="qte" name="quantity" id ="quantity" value="1">
                        <input type="submit" value="Ajouter au panier" id="validation" class="indent submit">

                    </div>
                </div>

                <div class="clearfix"></div>
                <p class="ref">Réference:{$product.reference} {*<img src="/img/sans-frais.png" style="margin-left: 25px;" alt="">*}</p>

                {if isset($product.combinations)}
                    {foreach key=key item=combination from=$product.combinations}
                        <div class="row clearfix">
                            <label for="{$combination.name}">{$combination.name}</label>

                            <select name="options[{$key}]" id="{$combination.name}" class="attribute">                                
                                {foreach key=key item=attribute from=$combination.attributes}
                                    <option value='{$attribute.id_product_attribute}'>{$attribute.name}</option>                            
                                {/foreach}
                            </select>
                        </div>
                    {/foreach}

                {/if}

                {if $product.id_category != 19 && !($product.width && $product.height)}
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
                    <div class="row clearfix">
                        <input type="button" value="Calculer" id="calculer" class="submit">						
                    </div>	
                {/if}
            </div>   	
        </form>
    </div>

    <div class="clearfix"></div>

    <div class="share">
        <a href="" class="indent imprimer">Imprimer</a>
        <a href="" class="indent envoyer-a-un-ami">Envoyer a un ami</a>
    </div>

    {*
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
    *}
    <div class="desc">
        {$product.description}
    </div>
{*
    <h3 class="paragraphe-titre">Nos conseils de pose en vidéo  <a href="#" class="top"></a></h3>
    <p class="produit-menu"><a href="">Produits complémentaires</a>
        | <a href="">Descriptif du produit</a>
        | <a href=""> Nos conseils de pose en vidéo</a>
        | Caractéristiques techniques</p>
</p>
<div style="width:560px;margin:0 auto">
    <iframe align="middle" width="560" height="315" src="/{$product.video}" frameborder="0" allowfullscreen></iframe>
</div>
*}
{*
<h3 class="paragraphe-titre">Julie D’ ALLOVITRES vous conseille ces produits complémentaires  <a href="#" class="top"></a></h3>
<p class="produit-menu"><a href="">Produits complémentaires</a>
| <a href="">Descriptif du produit</a>
| <a href=""> Nos conseils de pose en vidéo</a>
| Caractéristiques techniques</p>
</p>

<div class="complement">
<div class="produit first">
<img src="/img/product1.jpg" alt="">
<h3 class="titre">Miroir argenté 3 mm</h3>
<p class="prix">30€</p>
<p class="liens">
<a href="" class="panier indent">Panier</a>
<a href="" class="voir indent">Voir</a>
</p>
</div>
<div class="produit">
<img src="/img/product1.jpg" alt="">
<h3 class="titre">Miroir argenté 3 mm</h3>
<p class="prix">30€</p>
<p class="liens">
<a href="" class="panier indent">Panier</a>
<a href="" class="voir indent">Voir</a>
</p>
</div>
<div class="produit">
<img src="/img/product1.jpg" alt="">
<h3 class="titre">Miroir argenté 3 mm</h3>
<p class="prix">30€</p>
<p class="liens">
<a href="" class="panier indent">Panier</a>
<a href="" class="voir indent">Voir</a>
</p>
</div>			
</div>
*}
</div>

<script>
    var unit_price = {$product.price};

    myArray = $('.attribute');

    //console.log($('.attribute').serializeArray());
    $.ajax({
        url: "/functions/ajax_declinaison.php",
        type: "POST",
        dataType: "json",
        async: false,
        data: {
            id: this.value,
            id_product: {$product.id_product},
            ids: $('.attribute').serializeArray()
        },
        success: function(result) {
            unit_price = result.price;
            unit_weight = result.weight;
            // console.log(unit_price);
            $('#total_price').text(unit_price);
            //$('#total_poids').text(unit_weight);
            $('#price').val(unit_price);
            //$('#texte').text(unit_price);
        }
    });
</script>
<script>

    $(document).ready(function($) {
        var square = 0;

        var unit_weight = 1;
        var min_width = {$product.min_width};
        var max_width = {$product.max_width};
        var min_height = {$product.min_height};
        var max_height = {$product.max_height};

        $('.attribute').change(function() {
            myArray = $('.attribute');
            //console.log($('.attribute').serializeArray());
            $.ajax({
                url: "/functions/ajax_declinaison.php",
                type: "POST",
                dataType: "json",
                async: false,
                data: {
                    id: this.value,
                    id_product: {$product.id_product},
                    ids: $('.attribute').serializeArray()
                },
                success: function(result) {
                    unit_price = result.price;
                    unit_weight = result.weight;
                    // console.log(unit_price);
                    $('#total_price').text(unit_price);
                    //$('#total_poids').text(unit_weight);
                    $('#price').val(unit_price);
                    //$('#texte').text(unit_price);
                }
            });

            if (pheigth > 0 && pwidth > 0 && pwidth > 0) {
                square = (pwidth * pheigth) / 1000000;
                $('#surface').val(square.toFixed(2));
                $('#total_poids').text((square.toFixed(2) * unit_weight * qte).toFixed(2));
                $('#total_price').text((square.toFixed(2) * unit_price * qte).toFixed(2));
                $('#price').val((square.toFixed(2) * unit_price * qte).toFixed(2));
            }
        });

        $('#width').change(function() {

            if ($('#width').val() < min_width) {
                alert("La largeur minimal est de " + min_width + " mm.");
                $('#width').val("");
                return;
            }
            if ($('#width').val() > max_width) {
                alert("La largeur maximal est de " + max_width + " mm.");
                $('#width').val("");
                return;
            }

            qte = $('#quantity').val();
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();


            if (pheigth > 0 && pwidth > 0 && pwidth > 0) {
                square = (pwidth * pheigth) / 1000000;
                $('#surface').val(square.toFixed(2));
                $('#total_poids').text((square.toFixed(2) * unit_weight * qte).toFixed(2));
                $('#total_price').text((square.toFixed(2) * unit_price * qte).toFixed(2));
                $('#price').val((square.toFixed(2) * unit_price * qte).toFixed(2));
            }


        });
        $('#heigth').change(function() {

            if ($('#heigth').val() < min_height) {
                alert("La Longeur minimal est de " + min_height + " mm.");
                $('#heigth').val("");
                return;
            }
            if ($('#heigth').val() > max_height) {
                alert("La Longeur maximal est de " + max_height + " mm.");
                $('#heigth').val("");
                return;
            }

            qte = $('#quantity').val();
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();
            square = (pwidth * pheigth) / 1000000;

            if (pheigth > 0 && pwidth > 0 && pwidth > 0) {
                $('#surface').text(square.toFixed(2));
                $('#total_poids').text((square.toFixed(2) * unit_weight * qte).toFixed(2));
                $('#total_price').text((square.toFixed(2) * unit_price * qte).toFixed(2));
                $('#price').val((square.toFixed(2) * unit_price * qte).toFixed(2));

            }
        });
        $('#quantity').change(function() {
            qte = $('#quantity').val();
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();
            square = (pwidth * pheigth) / 1000000;

            if (pheigth > 0 && pwidth > 0 && pwidth > 0) {
                $('#surface').text(square.toFixed(2));
                $('#total_poids').text((square.toFixed(2) * unit_weight * qte).toFixed(2));
                $('#total_price').text((square.toFixed(2) * unit_price * qte).toFixed(2));
                $('#price').val((square.toFixed(2) * unit_price * qte).toFixed(2));
            }
        });

        $('#calculer').click(function() {
            if (pheigth > 0 && pwidth > 0 && pwidth > 0) {
                square = (pwidth * pheigth) / 1000000;
                $('#surface').val(square.toFixed(2));
                $('#total_poids').text((square.toFixed(2) * unit_weight * qte).toFixed(2));
                $('#total_price').text((square.toFixed(2) * unit_price * qte).toFixed(2));
                $('#price').val((square.toFixed(2) * unit_price * qte).toFixed(2));
            }
        }
        )

        $('#validation').submit(function() {

            if ($('#quantity').val() == "" || $('#width').val() == "" || $('#heigth').val() == "") {
                return false;
            }
        });
    });

</script>