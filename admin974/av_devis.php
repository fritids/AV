<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#pname').autocomplete('ajax/ajax_devis.php');
    });
</script>


<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h3>Client existant</h3>
            <select class="pme-input-0" >
                <option>--</option>
            </select>
        </div>
        <form class="form-horizontal" role="form">
            <div class="col-md-3">
                <h3>Nouveau client</h3>
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <input type="text" value="" name="firstname" class="form-control" placeholder="Nom">
                        <input type="text" value="" name="lastname" class="form-control" placeholder="Prénom">
                        <input type="text" value="" name="email" class="form-control" placeholder="E-mail">
                        <input type="text" value="" name="pwd"  class="form-control" placeholder="Mot de passe">
                    </div>

            </div>

            <div class="col-md-3">
                <h3>Addresse Livraison</h3>

                <div class="form-group">

                    <input type="text" value="" name="adresse1" class="form-control" placeholder="Adresse 1">
                    <input type="text" value="" name="adresse2" class="form-control" placeholder="Adresse 2">
                    <input type="text" value="" name="postcode" class="form-control" placeholder="Code postal">
                    <input type="text" value="" name="ville" class="form-control" placeholder="Ville">                

                </div>

            </div>
            <div class="col-md-3">
                <h3>Addresse Facturation</h3>
                <form class="form-horizontal" role="form">
                    <div class="form-group">

                        <input type="text" value="" name="adresse1" class="form-control" placeholder="Adresse 1">
                        <input type="text" value="" name="adresse2" class="form-control" placeholder="Adresse 2">
                        <input type="text" value="" name="postcode" class="form-control" placeholder="Code postal">
                        <input type="text" value="" name="ville" class="form-control" placeholder="Ville">                

                    </div>
            </div>
            <input type="submit" class="col-md-offset-3 col-md-9 btn-lg btn-warning">
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Commande</h2>
            <table class="table table-condensed col-md-12" id="tab_devis">
                <tr>
                    <th>Produit</th>
                    <th>Option</th>
                    <th>Largeur</th>
                    <th>Hauteur</th>
                    <th>Profondeur</th>
                    <th>Quantity</th>
                    <th>Poids</th>
                    <th>Prix HT</th>
                    <th>Prix TTC</th>
                    <th>Action</th>
                </tr>
                <tr id="template1">
                    <td><input type="text" name="product_name" id="pname"></td>
                    <td>
                        <select name="product_option" class="pme-input-0" >
                            <option>----</option>
                        </select>
                    </td>
                    <td><input type="text" name="product_width" ></td>
                    <td><input type="text" name="product_height" ></td>
                    <td><input type="text" name="product_depth" ></td>
                    <td><input type="text" name="product_quantity" ></td>                    
                    <td>Poids</td>
                    <td>Prix HT</td>
                    <td>Prix TTc</td>
                    <td id="btn_action">
                        <button id="newline"><span class="glyphicon glyphicon-plus"></span></button>
                        <button id="delline"><span class="glyphicon glyphicon-remove"></span></button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row pull-right">
        <input type="submit" class="btn-lg btn-warning">
    </div>
</div>

<script>
    var c = 1;
    $("#newline").click(function() {
        var cloned = $('#template1').clone();
        // Set the id of cloned, use i++ instead of incrementing it elsewhere.
        $(cloned).attr('id', 'dup' + (c++));
        $(cloned).find('#btn_action').html('<button id="delline"><span class="glyphicon glyphicon-remove"></span></button>');
        $(cloned).appendTo('#tab_devis');
        $("button[name='deltkt'").click(function() {
            $(this).parent().remove();
        })
    });

    $('#pname').autocomplete({
        serviceUrl: 'functions/ajax_devis.php',
        onSelect: function(suggestion) {
            alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
        }
    });
</script>



