<div class="largeur">
    <div class="bloc-titre">Produit</div>
    <div class="bloc-bas" style="height:400px">

        <form action="?cart" method="post">
            <input type="hidden" name="id_product" value="{$product.id_product}">
            <input type="hidden" name="add">
            <input type="submit" value="Ajouter au panier" >
        </form> 

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

        {if isset($product.product_caract)}
            <h2>details </h2>
            <ul>
                {foreach key=key item=caracts from=$product.product_caract}
                    <li>
                        {$caracts.caract_name} : {$caracts.caract_value}  
                    </li>                            
                {/foreach}
            </ul>
        {/if}



    </div>
</div>