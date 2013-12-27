<div id="recapitulatif">
    <h1>Merci, votre commande a été accepté.</h1>

    {if $payment == 'Chèque'}
        <b>Référence </b> : {$reference} <br>        
        {$config.payment.cheque_infos}        
    {/if}
    {if $payment == 'Virement bancaire'}
        {$config.payment.virement_infos}
        <b>Référence </b> : {$reference} <br>       
        
    {/if}
    

</div>


