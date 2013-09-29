<script>

    $(document).ready(function($) {
        var square = 0;
        $('#width').change(function() {
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();
            square = (pwidth * pheigth) / 10000;
            $('#quantity').val(square.toFixed(2));

        });
        $('#heigth').change(function() {
            pwidth = $('#width').val();
            pheigth = $('#heigth').val();
            square = (pwidth * pheigth) / 10000;
            $('#quantity').val(square.toFixed(2));

        });
    });

</script>

<div class="bloc-titre">Produit</div>
<div class="bloc-bas" style="min-height:400px">

    <form action="?cart" method="post">
        <input type="hidden" name="id_product" value="{$product.id_product}">
        <input type="hidden" name="add">
        <input type="submit" value="Ajouter au panier" >

        Width ({$product.min_width} mm - {$product.max_width}) : <input type="number" id ="width" name="width" value=""><br>
        Height ({$product.min_height}mm - {$product.max_height}) : <input type="number" id ="heigth" name="height" value=""><br>
        Total : <input type="number" id ="quantity" name="quantity" value="" required="true" readonly><br>


        <h1>Produits</h1>
        <ul>
            <li>id_product	        : {$product.id_product}   </li> 
            <li>id_category         : {$product.id_category}</li> 
            <li>quantity            : {$product.quantity}</li> 
            <li>price               : {$product.price}</li> 
            <li>unit_price_ratio    : {$product.unit_price_ratio}</li> 
            <li>reference           : {$product.reference}</li> 
            <li>width               : {$product.width}</li> 
            <li>height              : {$product.height}</li> 
            <li>depth               : {$product.depth}</li> 
            <li>weight              : {$product.weight}</li> 
            <li>active              : {$product.active}</li> 
            <li>date_add            : {$product.date_add}</li> 
            <li>date_upd            : {$product.date_upd}</li> 
            <li>name                : {$product.name}</li> 
            <li>description         : {$product.description}</li> 
            <li>description_short   : {$product.description_short}</li> 
        </ul>

        {if isset($product.caracteristiques)}
            <h2>details </h2>
            <ul>
                {foreach key=key item=caracts from=$product.caracteristiques}
                    <li>
                        {$caracts.caract_name} : {$caracts.caract_value}  
                    </li>                            
                {/foreach}
            </ul>
        {/if}

        {if isset($product.attributes)}
            <h2>Options </h2>
            <select name="options">
                {foreach key=key item=option from=$product.attributes}
                    <option value='{$option.id_product_attribute}'>
                        {$option.name} : prix = {$option.price}  : poids = {$option.weight}  
                    </option>                            
                {/foreach}
            </select>
        {/if}

    </form>

</div>