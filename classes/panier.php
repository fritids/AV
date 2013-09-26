<?PHP

class Panier {

    private $panier = array();

    // constructeur
    function __construct() { // constructeur
        @session_start();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        $this->panier = & $_SESSION['cart'];
    }

    // ajouter un article $refproduit
    public function addItem($refproduit = "", $nb = 1, $prix = 0, $nom) {
        @$this->panier[$refproduit]['quantity'] += $nb;
        @$this->panier[$refproduit]['prix'] += $prix;
        @$this->panier[$refproduit]['nom'] = $nom;
        if ($nb <= 0)
            unset($this->panier[$refproduit]);
    }

    // ajouter un article $refproduit
    public function addItemOption($refproduit = "", $refoption = "", $nb = 1, $prix = 0,$nom) {
        @$this->panier[$refproduit][$refoption]['quantity'] += $nb;
        @$this->panier[$refproduit][$refoption]['prix'] += $prix;
        @$this->panier[$refproduit][$refoption]['nom'] = $nom;
        if ($nb <= 0)
            unset($this->panier[$refproduit][$refoption]);
    }

    // ajouter un article $refproduit
    public function addItemContact($refproduit = "", $refcontact = "") {
        @$this->panier[$refproduit]['contact'] = $refcontact;
    }

    // ajouter un article $refproduit
    public function addItemFiles($refproduit = "", $reffiles = "") {
        @$this->panier[$refproduit]['files'][] = $reffiles;
    }

    // supprimer un article $refproduit
    public function removeItem($refproduit = "", $nb = 1) {
        @$this->panier[$refproduit]['quantity'] -= $nb;
        if ($this->panier[$refproduit]['quantity'] <= 0)
            unset($this->panier[$refproduit]);
    }

    // choisir la quantit� d'article $refproduit
    public function setQuantity($refproduit = "", $toSet = "") {
        @$this->panier[$refproduit]['quantity'] = $toSet;
        if ($toSet <= 0)
            unset($this->panier[$refproduit]);
    }

    // afficher la quantit� de produits dans le panier
    // param�tre : $refproduit : permet d'afficher la quantit� pour le produit de cette r�f�rence
    // si le param�tre est vide, on affiche la quantit� totale de produit
    public function showQuantity($refproduit = "") {
        if ($refproduit) {
            return $this->panier[$refproduit]['quantity'];
        } else {
            $total = 0;
            foreach ($this->panier as $ref => $data) {
                $total += $data['quantity'];
            }
        }
        return $total;
    }

    // afficher la liste des articles (et accessoirement, leur quantit�)

    public function showCart() {
        $list = array();
        $i = 0;
        foreach ($this->panier as $ref => $data) {
            if (!empty($ref)) {
                $list[$i]["id"] = $ref;
                $list[$i]["qte"] = $data['quantity'];
                $list[$i]["prix"] = $data['prix'];
                $list[$i]["nom"] = $data['nom'];
                foreach ($this->panier[$ref] as $oref => $option) {
                    if (!empty($option['quantity']) && $option['quantity'] > 0) {
                        $list[$i]["options"][] = array("o_id" => $oref, "o_qte" => $option['quantity'], "o_prix" => $option['prix'], "o_nom" => $option['nom']);
                        //$list[$i]["options"][] = $option;
                    }
                }

                //les contacts
                if (!empty($this->panier[$ref]['contact'])) {
                    foreach ($this->panier[$ref]['contact'] as $key => $contact) {
                        if (!empty($contact))
                            $list[$i]["contact"][$key] = $contact;
                    }
                }
                // fichiers attachés
                if (!empty($this->panier[$ref]['files'])) {
                    foreach ($this->panier[$ref]['files'] as $key => $file) {
                        if (!empty($file))
                            $list[$i]["files"][$key] = $file;
                    }
                }
                $i++;
            }
        }
        return $list;
    }

    public function addContact($contact) {
        array_push($this->panier["contact"], $contact);
    }

    public function getContact() {
        return($this->panier["contact"]);
    }

    public function flush() {

        $this->panier = array();
        unset($_SESSION['cart']);
    }

}

// fin de la classe
?>