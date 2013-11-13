
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
                <form action="/index.php?action=order_devis" method="post">
                    <input type="hidden" name="id_devis"  value="{$devis.id_devis}"/>
                    <tr>
                        <td class="numero">Devis N° <span>{$devis.id_devis}</span></td>
                        <td class="date">{$devis.date_add|date_format:"%d/%m/%y"}</td>
                        <td class="client">{$devis.prenom}</td>
                        <td class="prix">{$devis.total_paid} €</td>
                        <td class="action">
                            {if $devis.current_state == 1}
                                <button>
                                    <a href="/?devis&id={$devis.id_devis}&action=del" class="supprimer"><img src="/img/supprimer.png" alt=""/>Supprimer</a>
                                </button>
                            {/if}
                            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal{$devis.id_devis}">
                                <a href="/?devis&id={$devis.id_devis}&action=view" class="afficher"><img src="/img/pdf.png" alt=""/>Afficher</a>
                            </button>
                        </td>

                        {if $devis.current_state ==1}
                            <td class="commander">
                                <button type="submit"><span class="jevalide">Ajouter au panier</span></button>
                            </td>                            
                        {/if}
                        {if $devis.current_state ==2}
                            <td class="commander">
                                <span class="valide">Devis déjà payé</span>
                            </td>                          
                        {/if}
                    </tr>
                </form>
            {/foreach}

            </tbody>
        </table>


        {foreach item=devis from=$mydevis}
            <!-- Modal -->
            <div class="modal fade" id="myModal{$devis.id_devis}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Devis n° {$devis.id_devis}</h4>
                        </div>
                        <div class="modal-body">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="first">Produit</th>
                                        <th>Quantité</th>
                                        <th>Prix Unit.</th>
                                        <th>Poids</th>
                                        <th class="last">Total Ttc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {assign var='totalttc' value=0}
                                    {foreach item=devisdet from=$devis.detail}
                                        {$totalttc = $totalttc +$devisdet.total_price_tax_incl}
                                        <tr>
                                            <td>{$devisdet.product_name}</td>
                                            <td>{$devisdet.product_quantity}</td>
                                            <td>{$devisdet.product_price} €</td>
                                            <td>{$devisdet.product_weight} kg</td>                                            
                                            <td>{$devisdet.total_price_tax_incl} €</td>
                                        </tr>
                                    {/foreach}
                                    <tr>
                                        <td colspan="4">Total : </td>
                                        <td>{$totalttc} €</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>                            
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        {/foreach}

    {else}
        <p>Pas de devis en cours</p>
    {/if}
</div>

<div class="clearfix"></div>