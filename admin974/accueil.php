
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
            $mail->MsgHTML("Ticket fermé");

            if ($mail->Send()) {
                echo "<div class='alert alert-success'>Ticket #" . $id . " fermé</div>";
            }
        }
    }
    if (isset($_POST["ticket_pending"]) && $_POST["ticket_pending"]) {
        $param = array(
            "status" => 2,
            "date_upd" => date("y-m-d H:i:s")
        );
        $r = $db->where("id_ticket", $_POST["id_ticket"])
                ->update("av_ticket", $param);

        if ($r) {
            $r = $db->rawQuery("select * 
                from av_ticket a, admin_user b 
                where a.id_user = b.id_admin 
                and id_ticket=?", array($_POST["id_ticket"]));

            $id = $r[0]["id_ticket"];
            $title = $r[0]["title"];

            $description = "Bonjour, <br><br>
            Une mise à jour a été livré concernant cette demande.<br><br>
            Nous restons en attente de confirmation de votre part.<br><br>
            Cordialement";

            if ($_POST["ticket_pending_description"] != '')
                $description = $_POST["ticket_pending_description"];


            $mail->SetFrom($_SESSION['email']);
            $mail->AddAddress($r[0]["email"]);
            $mail->AddCC("stephane.alamichel@gmail.com");
            $mail->AddCC("benoit@trusttelecom.fr");
            $mail->Subject = "AV - INCIDENT#" . $id . " - " . $title . " - est en attente ";
            $mail->MsgHTML($description);

            if ($mail->Send()) {
                echo "<div class='alert alert-success'>Ticket #" . $id . " a été modifié</div>";
            }
        }
    }
    if (isset($_POST["ticket_reopen"]) && $_POST["ticket_reopen"]) {
        //statut = open
        $param = array(
            "status" => 0,
            "date_upd" => date("y-m-d H:i:s")
        );
        $r = $db->where("id_ticket", $_POST["id_ticket"])
                ->update("av_ticket", $param);

        if ($r) {
            $r = $db->rawQuery("select * 
                from av_ticket a, admin_user b 
                where a.id_user = b.id_admin 
                and id_ticket=?", array($_POST["id_ticket"]));

            $id = $r[0]["id_ticket"];
            $title = $r[0]["title"];
            $description = $_POST["ticket_reopen_description"];

            $mail->SetFrom($_SESSION['email']);
            $mail->AddAddress("stephane.alamichel@gmail.com");
            $mail->AddAddress("benoit@trusttelecom.fr");
            $mail->Subject = "AV - INCIDENT#" . $id . " - " . $title . " - a été ré-ouvert ";
            $mail->MsgHTML($description);

            if ($mail->Send()) {
                echo "<div class='alert alert-success'>Ticket #" . $id . " a été modifié</div>";
            }
        }
    }

    $filter = 1;
    if (isset($_GET["filter"]))
        $filter = 0;

    $t = $db->rawQuery("select * 
                        from av_ticket a, admin_user b 
                        where a.id_user= b.id_admin 
                        and (? = 0 or status not in (1))
                        order by date_add desc ", array($filter));
    ?>
    <div class="col-xs-5" style="overflow:scroll; height: 400px">
        <h3>Les nouveautés</h3>
        <ul class="list-unstyled">
            <li>04 fév 2014</li>
            <ul>
                <li>BO - multiples amélioration BO - ticket 55/54/44</li>
            </ul>
            <li>03 fév 2014</li>
            <ul>
                <li>BO - Devis - ajout des formes</li>
            </ul>
            <li>20 jan 2014</li>
            <ul>
                <li>FO - Forme spécifique - Ajout forme rond v2</li>
            </ul>
            <li>03 jan 2014</li>
            <ul>
                <li>FO - Téléchargement facture - correction faille de sécurité.</li>
            </ul>
            <li>02 jan 2014</li>
            <ul>
                <li>BO - Commande - Ajout bouton retrait du camion (incidence#5)</li>                
            </ul>            
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
        <form action="" class="form-horizontal" method="post" role="form">
            <div class="form-group">
                <label class="col-xs-2 control-label" for="inputTitle"><b>Titre :</b> </label>
                <div class="col-xs-5">
                    <input type="text" name="title" value="" id="inputTitle" required="required" class="form-control">
                </div>            
                <label class="col-xs-2 control-label" for="inputSeverity"><b>Sévérité :</b>  </label>
                <div class="col-xs-3">
                    <select name="severity" required="required" id="inputSeverity" class=" form-control">
                        <option value="">--</option>
                        <option value="0">0 - Demande d'évolution </option>
                        <option value="1">1 - Basse</option>
                        <option value="2">2 - Moyen</option>
                        <option value="3">3 - Critique</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-3">
                <label class="control-label" for="inputDescription"><b>Description :</b> </label>
            </div>  
            <div class="col-xs-12">
                <textarea name ='description' id="inputDescription"></textarea>
            </div>
            <div class="col-xs-12">
                <p>
                    <input type="submit" name="new_ticket" value="Envoyer" class="btn btn-block btn-primary">
                </p>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>

    <div class="col-xs-6">
        <table>
            <tr>
                <td class="alert-ticket-0">La demande est chez TT</td>                
                <td class="alert-ticket-2">La demande est chez AV</td>
                <td class="alert-ticket-1">La demande est cloturée</td> 
            </tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="col-xs-6">
        <a href="?filter" class="btn btn-info">Voir tous les tickets</a>
    </div>
    <div class="clearfix"></div>
    <div class="col-xs-12">
        <h3>Les dernières incidences</h3>            
        <table class="table table-bordered table-condensed">
            <tr>
                <th>#</th>
                <th>Demandeur</th>
                <th>Date</th>                
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
                    <td nowrap>
                        <?= $ticket["title"] ?> (<?= $ticket["severity"] ?>)<br><br>
                        <?
                        switch ($ticket["status"]) {
                            case 0 : $state_label = "Ouvert";
                                break;
                            case 1 : $state_label = "Fermer";
                                break;
                            case 2 : $state_label = "Attente validation du demandeur";
                                break;
                            default:
                                $state_label = "";
                        }
                        ?>
                        Etat : <?= $state_label ?> <br> <?= ($ticket["date_upd"] != "") ? "le : " . strftime("%a %d %b %y %T", strtotime($ticket["date_upd"])) : "" ?>
                    </td>
                    <td><?= $ticket["description"] ?></td>                    

                    <td>
                        <?
                        if ($_SESSION["user_id"] == $ticket["id_user"]) {
                            if ($ticket["status"] == 0 || $ticket["status"] == 2) {
                                ?>
                                <form action="" method="post">
                                    <input type="hidden" name="id_ticket" value="<?= $ticket["id_ticket"] ?>">
                                    <input type="submit" name="ticket_close" value="Fermer la demande" class="btn btn-xs btn-default">
                                </form>

                                <?
                                if ($ticket["status"] == 2) {
                                    ?>
                                    <form action="" method="post">
                                        <input type="hidden" name="id_ticket" value="<?= $ticket["id_ticket"] ?>">
                                        <input type="text" name="ticket_reopen_description" id="ticket_reopen_description" placeholder="votre commentaire ici" required="required">
                                        <input type="submit" name="ticket_reopen" value="Ré-ouvrir" class="btn btn-xs btn-default">
                                    </form>
                                    <?
                                }
                            }
                        }
                        if ($ticket["status"] == 0 && ( $_SESSION['email'] == "stephane.alamichel@gmail.com" || $_SESSION['email'] == "benoit@trusttelecom.fr")) {
                            ?>
                            <form action="" method="post">
                                <input type="hidden" name="id_ticket" value="<?= $ticket["id_ticket"] ?>">
                                <input type="text" name="ticket_pending_description" id="ticket_pending_description" placeholder="votre commentaire ici">
                                <input type="submit" name="ticket_pending" value="Demander validation" class="btn btn-xs btn-default">
                            </form>
                            <?
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
