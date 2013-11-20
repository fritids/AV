
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
                        <td class="client">{$devis.prenom}</td>
                        <td class="prix">{$devis.total_paid} €</td>
                        <td class="action">
                            {if $devis.current_state == 1}
                                <button>
                                    <a href="/?devis&id={$devis.id_devis}&action=del" class="supprimer"><img src="/img/supprimer.png" alt=""/>Supprimer</a>
                                </button>
                            {/if}
                            &nbsp;&nbsp;
                            <form action="/index.php?action=dl_devis" method="post">
                                <input type="hidden" name="id_devis"  value="{$devis.id_devis}"/>
                                <button type="submit" class="btn btn-primary btn-lg " value="{$devis.id_devis}">
                                    Afficher
                                </button>
                            </form>
                        </td>
                        {if $devis.current_state == 1}
                            <td class="commander">
                                <form action="/index.php?action=order_devis" method="post">
                                    <input type="hidden" name="id_devis"  value="{$devis.id_devis}"/>
                                    <button type="submit"><span class="jevalide">Ajouter au panier</span></button>
                                </form>
                            </td>                            
                        {/if}
                        {if $devis.current_state == 2}
                            <td class="rejeted">
                                <span>Devis rejeté</span>
                            </td>                          
                        {/if}
                        {if $devis.current_state == 3}
                            <td class="commander">
                                <span class="valide">Devis déjà payé</span>
                            </td>                          
                        {/if}
                    </tr>
                    </form>
                {/foreach}
            </tbody>
        </table>

    {else}
        <p>Pas de devis en cours</p>
    {/if}
</div>

<div class="clearfix"></div>