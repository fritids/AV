<div id="mescommandes" class="clear-it">
    <h3 id="titre">Mes commandes</h3>
    {if $orders}
        <table>
            <thead>
                <tr>
                    <th class="first">N° DE CMD</th>
                    <th>DATE DE COMMANDE</th>
                    <th>MODE DE PAIEMENT</th>
                    <th>PRIX TTC</th>
                    <th >STATUT</th>
                    <th class="last"></th>
                </tr>
            </thead>
            <tbody>
                {foreach key=key item=order from=$orders}
                    {if $order.current_state!=null}
                        <tr>
                            <td class="numero">N° <span>{$order.id_order}</span></td>
                            <td class="date">{$order.date_add|date_format:"%d/%m/%y"}</td>
                            <td class="client">{$order.payment}</td>
                            <td class="prix">{$order.total_paid} € TTC</td>
                            <td class="action">{$order.statut_label}</td>
                            <td>
                                <form action="/index.php?action=dl_facture" method="post">
                                    <button type="submit" name="id_order" value="{$order.id_order}">Afficher</button>
                                </form>
                            </td>
                        </tr>
                    {/if}
                {/foreach}

            </tbody>
        </table>
    {else}
        <p>Aucune commande n'a été trouvée</p>
    {/if}
</div>

<div class="clearfix"></div>