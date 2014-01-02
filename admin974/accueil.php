
<div class="container">
    <?
    require_once '../classes/class.phpmailer.php';
    $mail = new PHPMailer();
//Set who the message is to be sent from
    $mail->SetFrom($confmail["from"]);
    $mail->CharSet = 'UTF-8';
    $db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

    if (isset($_POST["new_ticket"]) && $_POST["new_ticket"]) {

        $param = array("id_user" => $_SESSION["user_id"],
            "date_add" => date("y-m-d H:i:s"),
            "severity" => $_POST["severity"],
            "status" => 0,
            "title" => $_POST["title"],
            "description" => $_POST["description"],
        );
        $id = $db->insert("av_ticket", $param);

        $mail->SetFrom($_SESSION['email']);
        $mail->AddAddress("stephane.alamichel@gmail.com");
        $mail->AddAddress("benoit@trusttelecom.fr");

        $mail->Subject = "AV - INCIDENT#" . $id . " - " . $_POST["title"] . " - SEVERITE : " . $_POST["severity"];
        $mail->MsgHTML($_POST["description"]);

        if ($mail->Send()) {
            echo "<div class='alert alert-success'>Ticket #" . $id . " envoyé</div>";
        }
    }
    if (isset($_POST["ticket_close"]) && $_POST["ticket_close"]) {
        $param = array(
            "date_close" => date("y-m-d H:i:s"),
            "status" => 1
        );
        $r = $db->where("id_ticket", $_POST["id_ticket"])
                ->update("av_ticket", $param);

        if ($r) {
            $r = $db->where("id_ticket", $_POST["id_ticket"])
                    ->get("av_ticket");

            $id = $r[0]["id_ticket"];
            $title = $r[0]["title"];
            $description = $r[0]["description"];

            $mail->SetFrom($_SESSION['email']);
            $mail->AddAddress("stephane.alamichel@gmail.com");
            $mail->AddAddress("benoit@trusttelecom.fr");
            $mail->Subject = "AV - INCIDENT#" . $id . " - " . $title . " a été CLOTURE";
            $mail->MsgHTML($description);

            if ($mail->Send()) {
                echo "<div class='alert alert-success'>Ticket #" . $id . " fermé</div>";
            }
        }
    }


    $t = $db->rawQuery("select * 
                        from av_ticket a, admin_user b 
                        where a.id_user= b.id_admin 
                        order by date_add desc ");
    ?>
    <div class="col-xs-5" style="overflow:scroll; height: 475px">
        <h3>Les nouveautés</h3>
        <ul class="list-unstyled">
            <li>01 jan 2014</li>
            <ul>
                <li><b>Bonne et heureuse année 2014 !!!</b></li>
                <li>FO - TVA - Augmentation taux de tva à 20%</li>
                <li>FO - DEVIS - possibilité d'ajouter une pièce jointe</li>
            </ul>
            <li>31 déc 2013</li>
            <ul>
                <li>FO - pro - correction calcul réduction (incident#3)</li>
                <li>BO - feuille de route - correction n° de tel manquant(incident#4)</li>
            </ul>
            <li>30 déc 2013</li>
            <ul>
                <li>FO - paiement - Mise en production socgenactif</li>
                <li>BO - Tournéé - Amélioration chargement général</li>
            </ul>
            <li>29 déc 2013</li>
            <ul>
                <li>BO - Livraison - le retrait d'un produit du camion passe la commande en "préparation en cours" (bug du 27/12)</li>
                <li>BO - Fournisseur - le système ne renverra pas une commande fournisseur si le produit est déjà en "commande fournisseur" (bug du 27/12)</li>
                <li>BO - Fournisseur - correction envoi bdc sur 2450 (bug du 27/12)</li>
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
            <input type="submit" name="new_ticket" value="Envoyer" class="btn btn-primary">
        </form>
    </div>
    <div class="clearfix"></div>

    <div class="col-xs-12">
        <h3>Les dernières incidences</h3>            
        <table class="table table-bordered table-condensed">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Demandeur</th>
                <th>Titre</th>
                <th>Description</th>             
                <th></th>
            </tr>
            <?
            foreach ($t as $ticket) {
                ?>
                <tr class="alert-ticket-<?= $ticket["status"] ?>">
                    <td><?= $ticket["id_ticket"] ?></td>
                    <td><?= $ticket["prenom"] ?></td>
                    <td><?= strftime("%a %d %b %y %T", strtotime($ticket["date_add"])) ?></td>
                    <td><?= $ticket["title"] ?> (<?= $ticket["severity"] ?>)</td>
                    <td><?= $ticket["description"] ?></td>                    
                  
                    <td>
                        <?
                        if ($_SESSION["user_id"] == $ticket["id_user"]) {
                            if ($ticket["status"] == 0) {
                                ?>
                                <form action="" method="post">
                                    <input type="hidden" name="id_ticket" value="<?= $ticket["id_ticket"] ?>">
                                    <input type="submit" name="ticket_close" value="Fermer" class="btn btn-xs btn-default">
                                </form>
                                <?
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?
            }
            ?>
        </table>
    </div>
</div>