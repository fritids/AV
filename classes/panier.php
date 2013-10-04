<?PHP

class Panier {

    private $panier = array();

    // constructeur
    function __construct() { // constructeur
        @session_start();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
            $_SESSION['cart_summary'] = array();
        }
        $this->panier = & $_SESSION['cart'];
        $this->panier_summary = & $_SESSION['cart_summary'];
    }

    // ajouter un article $refproduit
    public function addItem($refproduit = "", $nb = 1, $price = 0, $name, $shipping, $surface) {
        @$this->panier[$refproduit]['quantity'] += $nb;
        @$this->panier[$refproduit]['surface'] += $surface;
        @$this->panier[$refproduit]['price'] += $price;
        @$this->panier[$refproduit]['shipping'] += $shipping;
        @$this->panier_summary['total_amount'] += $nb * $price * $surface + $shipping;
        @$this->panier[$refproduit]['name'] = $name;
        if ($nb <= 0)
            unset($this->panier[$refproduit]);
    }

    // ajouter un article $refproduit
    public function addItemOption($refproduit = "", $refoption = "", $nb = 1, $price = 0, $name, $shipping, $surface) {
        @$this->panier[$refproduit]["options"][$refoption]['quantity'] += $nb;
        @$this->panier[$refproduit]["options"][$refoption]['surface'] += $surface;
        @$this->panier[$refproduit]["options"][$refoption]['price'] += $price;
        @$this->panier[$refproduit]["options"][$refoption]['shipping'] += $shipping;
        @$this->panier_summary['total_amount'] += $nb * $price * $surface + $shipping;
        @$this->panier[$refproduit]["options"][$refoption]['name'] = $name;
        if ($nb <= 0)
            unset($this->panier[$refproduit]["options"][$refoption]);
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
    public function removeItem($refproduit = "", $nb = 1, $price = 0, $shipping, $surface) {
        @$this->panier[$refproduit]['quantity'] -= $nb;
        @$this->panier[$refproduit]['surface'] -= $surface;
        @$this->panier_summary['total_amount'] -= $nb * $price * $surface + $shipping;
        if ($this->panier[$refproduit]['quantity'] <= 0) {
            //remove option
            $option_amount = 0;
            if (isset($this->panier[$refproduit]['options']))
                foreach ($this->panier[$refproduit]['options'] as $k => $option)
                    $option_amount += $option["quantity"] * $option["price"] * $option["surface"] + $option['shipping'];

            $this->panier_summary['total_amount'] -=$option_amount;
            unset($this->panier[$refproduit]);
        }
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
                $list[$i]["quantity"] = $data['quantity'];
                $list[$i]["shipping"] = $data['shipping'];
                $list[$i]["surface"] = $data['surface'];
                $list[$i]["price"] = $data['price'];
                $list[$i]["name"] = $data['name'];

                //les options
                if (!empty($this->panier[$ref]['options'])) {
                    foreach ($this->panier[$ref]["options"] as $oref => $option) {
                        if (!empty($option['quantity']) && $option['quantity'] > 0) {
                            $list[$i]["options"][] = array("o_id" => $oref,
                                "o_quantity" => $option['quantity'],
                                "o_surface" => $option['surface'],
                                "o_price" => $option['price'],
                                "o_name" => $option['name'],
                                "o_shipping" => $option['shipping'],
                            );
                            //$list[$i]["options"][] = $option;
                        }
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