<div id="produit" class="bloc_page_gauche clear-it">    
    <div id="texte"></div>
    <div id="features">
        <div class="images">
            <img src="" width="325" id="custom_img" />  
            <div id="custom-pager"></div>
        </div>	

        <form action="/?cart" method="post" id="validation">
            <div class="features">
                <div class="separ clearfix">
                    <div class="infos">
                        <p class="prix" ><span id="total_price">{($product.price*$config.vat_rate)|round:2}</span> €</p>
                        {if $product.id_category !=19}
                            <p><span id="surface"></span> m² calculé</p>                            
                            {*<p><span id="total_poids"></span> kg calculés</p>*}
                            <p><span id=""></span> min facturé <span class="min_area_invoiced"></span> m²</p>
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
                <p>Attribut personnalisable</p>
                {if isset($product.specific_combinations)}
                    {foreach key=key item=combination from=$product.specific_combinations}
                        <div class="row clearfix ">
                            <label for="{$combination.name}">{$combination.name}</label>

                            {if $combination.type == 1}
                                {if $combination.is_duplicable == 1}
                                    <div><a href="#" id="{$key}" class="add_item"> Ajouter un {$combination.name}  </a></div>
                                {else}
                                    <div id="i_{$key}" >
                                        <select name="custom[{$key}][]" id="{$key}" class="main_item">                                
                                            <option></option>
                                            {foreach key=key2 item=combination_item from=$combination.items}
                                                <option value='{$combination_item.id_attributes_items}'>{$combination_item.name}</option>                            
                                            {/foreach}
                                        </select>                                        
                                        <br>
                                        <div id="list_{$key}_i_{$key}" style="float:left"></div>
                                        <br>
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            bindItemAdd();
                                        });
                                    </script> 

                                {/if}
                                <div id="template_{$key}" style="display: none;">
                                    <select name="custom[{$key}][]" id="{$key}" class="main_item">                                
                                        <option></option>
                                        {foreach key=key2 item=combination_item from=$combination.items}
                                            <option value='{$combination_item.id_attributes_items}'>{$combination_item.name}</option>                            
                                        {/foreach}
                                    </select>

                                    {if $combination.is_duplicable == 1}
                                        <a href="#" class="del_item"> X </a>                                    
                                    {/if}
                                    <br>
                                    <div class="list_{$key}"></div>
                                    <br>
                                </div>                                
                                <div id="new_item_{$key}"></div>

                            {/if}
                            {if $combination.type == 0}    
                                <div><a href="#" id="{$key}" class="add_item"> Ajouter un {$combination.name}  </a></div><br><br><br>

                                <div id="template" style="display: none; float: left">
                                    Taille : <input type="text" id="custom_item_width" value="" style="width:40px">
                                    Dist. X :<input type="text" id="custom_item_pos_x" value="" style="width:40px">
                                    Dist. Y :<input type="text" id="custom_item_pos_y" value="" style="width:40px">
                                    <a href="#" class="del_item"> X </a>
                                </div>
                                <div id="custom_items"></div>
                            {/if}

                        </div>
                    {/foreach}
                {/if}


                <div class="row clearfix" style="display: none">
                    <label for="width">Largeur</label>
                    <input type="text" id ="width" name="width" value="" class="text">
                    <span class="info">de {$product.min_width} à {$product.max_width} mm</span>
                </div>	
                <div class="row clearfix" style="display: none">
                    <label for="height">longueur</label>
                    <input type="text" id ="height" name="height" value="" class="text">
                    <span class="info">de {$product.min_height} à {$product.max_height} mm</span>
                </div>   
                <div class="row clearfix">
                    <input type="button" value="Calculer" id="calculer" class="submit">						
                </div>	

            </div>   	
        </form>
    </div>
</div>
<script>
    var unit_price = {($product.price*$config.vat_rate)|round:2};
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
    });</script>

<script>

    var c = 0;
    var i = 0;
    $(".add_item").click(function() {
        item_id = $(this).attr("id");
        $clone_item = $("#template_" + item_id).clone();

        $clone_item.attr('id', (++c));
        $clone_item.find(".list_" + item_id).attr('id', 'list_' + item_id + "_" + c);
        /* $clone_item.find("#custom_item_pos_x").attr('name', 'custom[' + item_id + '][' + i + '][custom_item_pos_x]');
         $clone_item.find("#custom_item_pos_y").attr('name', 'custom[' + item_id + '][' + i + '][custom_item_pos_y]');*/
        $clone_item.appendTo("#new_item_" + item_id);
        $clone_item.show("slow");
        i++;

        bindItemAdd();

        $(".del_item").click(function() {
            $(this).parent().hide("slow");
            $(this).parent().remove();

        });
    });


</script>

<script>
    var min_area_invoiced;
    var max_area_invoiced;
    var square = 0;
    var unit_weight = 1;
    var min_width = {$product.min_width};
    var max_width = {$product.max_width};
    var min_height = {$product.min_height};
    var max_height = {$product.max_height};

    function bindItemAdd() {
        $('.main_item').change(function() {
            //console.log($(this).parent().attr("id"));

            $id_block = $(this).parent().attr("id");
            $id_sub_item = $(this).val();
            $id_item = $(this).attr("id");
            $id_product = {$product.id_product};

    {literal}
                $.ajax({
                    url: "/functions/ajax_custom_sub_items.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        id_item: $id_item,
                        id_sub_item: $id_sub_item,
                        id_product: $id_product
                    },
                    success: function(result) {
                        //console.log(result);
                        item_price_addition = 0;
                        //console.log('#list_' + $id_item + '_' + $id_block);
                        $('#list_' + $id_item + '_' + $id_block).text("");
                        if (result.min_area_invoiced !== null) {
                            min_area_invoiced = result.min_area_invoiced;
                        }
                        if (result.max_area_invoiced !== null) {
                            max_area_invoiced = result.max_area_invoiced;
                        }
                        if (result.picture !== null) {
                            $("#custom_img").attr("src", "/img/f/" + result.picture);
                        }


                        calculateprice();

                        $.each(result.item_values, function(key, value) {

                            /*$("#list_Formes").append($('<input />').attr({'type':'text', 'id':'url' + key}));*/
                            $item_input = $('<input />').attr({'type': 'text', 'id': key, 'name': 'custom[' + result.id_attribute + '][' + result.id_attributes_items + '][' + key + ']'});
                            if (value.is_width === 1) {
                                $item_input.addClass("primary_width");
                            }
                            if (value.is_height === 1) {
                                $item_input.addClass("primary_height");
                            }
                            $item_input.css("width", "50");
                            $('#list_' + $id_item + '_' + $id_block).append("<div>");
                            $('#list_' + $id_item + '_' + $id_block).append(value.name + ": ");
                            $('#list_' + $id_item + '_' + $id_block).append($item_input);
                            $('#list_' + $id_item + '_' + $id_block).append("</div>");
                        });
                    }
                });
    {/literal}
                $(".primary_width").change(function() {
                    $('#width').val($(this).val());
                    calculateprice()
                });
                $(".primary_height").change(function() {
                    $('#height').val($(this).val());
                    calculateprice()
                });

                $('.attribute').change();
            });
        }

        function calculateprice() {
            qte = $('#quantity').val();
            pwidth = $('#width').val();
            pheight = $('#height').val();

            if (pheight > 0 && pwidth > 0 && qte > 0) {
                area = (pwidth * pheight) / 1000000;
                if (area < min_area_invoiced) {
                    area = min_area_invoiced;
                }

                /*console.log(pwidth);
                console.log(pheight);
                console.log(area);
                console.log(area * unit_price * qte * shape_coef);
                console.log(unit_price);*/
                $('#surface').text(area);
                $('#total_poids').text((area * unit_weight * qte ).toFixed(2));
                $('#total_price').text((area * unit_price * qte ).toFixed(2));
                $('#price').val((area * unit_price * qte).toFixed(2));
            }
        }



        $(document).ready(function($) {

    
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
                        ids: $('.attribute').serializeArray(),
                        subItems: $('.main_item').serializeArray()
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
                calculateprice();
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

                calculateprice();
            });
            $('#height').change(function() {

                if ($('#height').val() < min_height) {
                    alert("La Longeur minimal est de " + min_height + " mm.");
                    $('#height').val("");
                    return;
                }
                if ($('#height').val() > max_height) {
                    alert("La Longeur maximal est de " + max_height + " mm.");
                    $('#height').val("");
                    return;
                }

                calculateprice();
            });
            $('#quantity').change(function() {
                calculateprice();
            });
            $('#calculer').click(function() {
                calculateprice();
            });
            $('#validation').submit(function() {
                if ($('#quantity').val() === "" || $('#width').val() === "" || $('#height').val() === "") {
                    return false;
                }
            });
        });

</script>