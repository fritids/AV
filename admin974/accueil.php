
<div class="container">
    <?
    require_once '../classes/class.phpmailer.php';
    $mail = new PHPMailer();
//Set who the message is to be sent from
    $mail->SetFrom($confmail["from"]);
    $mail->CharSet = 'UTF-8';
    $db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

    if ($_POST) {

        $param = array("id_user" => $_SESSION["user_id"],
            "date_add" => date("y-m-d H:i:s"),
            "severity" => $_POST["severity"],
            "status" => "NEW",
            "title" => $_POST["title"],
            "description" => $_POST["description"],
        );
        $id = $db->insert("av_ticket", $param);

        $mail->SetFrom($_SESSION['email']);
        foreach ($monitoringEmails as $dest) {
            $mail->AddAddress($dest);
        }
        $mail->AddAddress("stephane.alamichel@gmail.com");

        $mail->Subject = "AV - INCIDENT#" . $id . " - " . $_POST["title"] . " - SEVERITE : " . $_POST["severity"];
        $mail->MsgHTML($_POST["description"]);

        if ($mail->Send()) {
            echo "<div class='alert alert-success'>Ticket #" . $id . " envoyé</div>";
        }
    }
    ?>
    <div class="col-xs-5">
        <h3>Les nouveautés: </h3>
        <ul class="list-unstyled">
            <li>29 déc 2013</li>
            <ul>
                <li>BO - Livraison - le retrait d'un produit du camion passe la commande en "préparation en cours" (bug du 27/12)</li>
                <li>BO - Fournisseur - le système ne renverra pas une commande fournisseur si le produit est déjà en "commande fournisseur" (bug du 27/12)</li>
                <li>BO - Commande - le système affectera "Livré" si tous les produits sont considérés "Livré" (demande 27/12)</li>
            </ul>
            <li>26 déc 2013</li>
            <ul>
                <li>BO - Stats & reporting : modification export XLS</li>
                <li>BO - Tournée : correction bug lien vers une commande</li>
                <li>BO - Commandes: Création n° de facture </li>
                <li>BO - Devis : Poids obligatoire sur produits exotiques</li>
                <li>FO - Produit - Ajout bouton d'accès aux formes spécifiques</li>
            </ul>
        </ul>
    </div>
    <div class="col-xs-7">
        <h3>Déclarer un incident</h3>

        <form action="" method="post" role="form">

            <div class="form-group">
                <b>Titre :</b> <input type="text" name="title" value="" required="required">
            </div>

            <div class="form-group">
                <b>Sévérité :</b>  
                <select name="severity" required="required">
                    <option value="">--</option>
                    <option value="0">0 - Demande d'évolution </option>
                    <option value="1">1 - Basse</option>
                    <option value="2">2 - Moyen</option>
                    <option value="3">3 - Critique</option>
                </select>
            </div>
            <div class="form-group">
                <b>Description:</b> <textarea name ='description'></textarea>
            </div>
            <input type="submit" value="Envoyer" class="btn btn-primary">
        </form>
    </div>
</div>