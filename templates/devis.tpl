
<div id="mesdevis" class="clear-it">
    <h3 id="titre">Mes devis</h3>
    {if $mydevis}
        <p>Gérez, supprimez, imprimez, enregistrez ou validez vos devis.</p>
        <table>
            <thead>
                <tr>
                    <th class="first">N° DE DEVIS</th>
                    <th>Date de devis</th>
                    <th>Envoyé par</th>
                    <th>Prix ttc</th>
                    <th>Gérer</th>
                    <th class="last">Commander</th>
                </tr>
            </thead>
            <tbody>
                {foreach item=devis from=$mydevis}
                    <tr>
                        <td class="numero">Devis N° <span>{$devis.id_devis}</span></td>
                        <td class="date">{$devis.date_add|date_format:"%d/%m/%y"}</td>
                        <td class="client">--</td>
                        <td class="prix">{$devis.total_paid} €</td>
                        <td class="action">
                            <a href="?devis&id={$devis.id_devis}&action=del" class="supprimer"><img src="img/supprimer.png" alt=""/>Supprimer</a>
                            <a href="?devis&id={$devis.id_devis}&action=view" class="afficher"><img src="img/pdf.png" alt=""/>Afficher</a>
                            
                        </td>
                        <td class="commander">
                            <span class="valide">{$item.current_state}</span>
                        </td>
                    </tr>
                {/foreach}

            </tbody>
        </table>

        {if $mydevisdetail}
            <h3 >Detail </h3>
            <table>
                <thead>
                    <tr>
                        <th class="first">Produit</th>
                        <th>largeur <br>(mm)</th>
                        <th>Hauteur <br>(mm)</th>
                        <th>Epaisseur <br>(mm)</th>
                        <th>Quantité</th>
                        <th>Prix Unit.</th>
                        <th>Frais de port</th>
                        <th class="last">Total Ttc</th>
                    </tr>
                </thead>
                <tbody>
                    {assign var='totalttc' value=0}
                    {foreach item=devis from=$mydevisdetail}
                        <tr>
                            <td>{$devis.product_name}</td>
                            <td>{$devis.product_width}</td>
                            <td>{$devis.product_height}</td>
                            <td>{$devis.product_depth}</td>
                            <td>{$devis.product_quantity}</td>
                            <td>{$devis.product_price} €</td>
                            <td>{$devis.product_shipping} €</td>                            
                            <td>{$devis.total_price_tax_incl} €</td>
                            {$totalttc = $totalttc +$devis.total_price_tax_incl}
                        </tr>
                    {/foreach}
                    <tr>
                        <td colspan="7">Total : </td>
                        <td>{$totalttc} €</td>
                    </tr>
                </tbody>
            </table>
        {/if}
    {else}
        <p>Pas de devis en cours</p>
    {/if}
</div>

<div class="clearfix"></div>