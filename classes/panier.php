<?PHP

class Panier {

    private $panier = array();
    private $nbItems = 0;

    // constructeur
    function __construct() { // constructeur
        @session_start();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
            $_SESSION['cart_summary'] = array();
        }

        $this->panier = & $_SESSION['cart'];
        $this->panier_summary = & $_SESSION['cart_summary'];
        $this->nbItems = $this->getNbItems();
    }

    // ajouter un article $refproduit
    public function addItem($refproduit = "", $nb = 1, $price = 0, $name, $shipping, $surface, $dimension, $productInfos, $n) {

        if (empty($dimension))
            $surface = 1;

        $montant_produit_ttc = $nb * round($price, 2) * round($surface, 2);

        @$this->panier[$n][$refproduit]['quantity'] += $nb;
        @$this->panier[$n][$refproduit]['dimension'] = $dimension;
        @$this->panier[$n][$refproduit]['productinfos'] = $productInfos;
        @$this->panier[$n][$refproduit]['surface'] = round($surface, 2);
        @$this->panier[$n][$refproduit]['price'] = round($price, 2);
        @$this->panier[$n][$refproduit]['shipping'] = round($this->panier[$refproduit]['shipping'], 2) + round($shipping, 2);
        @$this->panier[$n][$refproduit]['prixttc'] = round($this->panier[$refproduit]['prixttc'], 2) + round($montant_produit_ttc, 2);

        @$this->panier_summary['total_shipping'] = round($this->panier_summary['total_shipping'], 2) + round($shipping, 2);
        @$this->panier_summary['total_taxes'] = round($this->panier_summary['total_taxes'], 2) + round($montant_produit_ttc - $montant_produit_ttc / 1.196, 2);
        @$this->panier_summary['total_produits'] = round($this->panier_summary['total_produits'], 2) + round($montant_produit_ttc, 2);
        @$this->panier_summary['total_amount'] = round($this->panier_summary['total_produits'], 2) + round($shipping, 2);
        @$this->panier[$n][$refproduit]['name'] = $name;

        if ($_SESSION["user"]["customer_group"] == 1) {
            $this->applyProDiscount();
        }

        if ($nb <= 0)
            unset($this->panier[$refproduit]);
    }

    // ajouter un article $refproduit
    public function addItemOption($refproduit = "", $refoption = "", $nb = 1, $price = 0, $name, $shipping, $surface, $dimension, $n) {

        $montant_produit_ttc = $nb * round($price, 2) * round($surface, 2);

        @$this->panier[$n][$refproduit]["options"][$refoption]['quantity'] += $nb;
        @$this->panier[$n][$refproduit]["options"][$refoption]['dimension'] = $dimension;
        @$this->panier[$n][$refproduit]["options"][$refoption]['surface'] = round($surface, 2);
        @$this->panier[$n][$refproduit]["options"][$refoption]['price'] = round($price, 2);
        @$this->panier[$n][$refproduit]["options"][$refoption]['shipping'] = round($this->panier[$n][$refproduit]["options"][$refoption]['shipping'], 2) + round($shipping, 2);
        @$this->panier[$n][$refproduit]["options"][$refoption]['prixttc'] = round($this->panier[$n][$refproduit]["options"][$refoption]['prixttc'], 2) + round($montant_produit_ttc, 2);
        @$this->panier[$n][$refproduit]['prixttc'] = round($this->panier[$n][$refproduit]['prixttc'], 2) + round($montant_produit_ttc, 2);

        @$this->panier_summary['total_shipping'] = round($this->panier_summary['total_shipping'], 2) + round($shipping, 2);
        @$this->panier_summary['total_taxes'] = round($this->panier_summary['total_taxes'], 2) + round($montant_produit_ttc - $montant_produit_ttc / 1.196, 2);
        @$this->panier_summary['total_produits'] = round($this->panier_summary['total_produits'], 2) + round($montant_produit_ttc, 2);
        @$this->panier_summary['total_amount'] = round($this->panier_summary['total_produits'], 2) + round($shipping, 2);

        @$this->panier[$n][$refproduit]["options"][$refoption]['name'] = $name;
        if ($nb <= 0)
            unset($this->panier[$n][$refproduit]["options"][$refoption]);
    }

    // supprimer un article $refproduit
    public function removeItem($refproduit = "", $nb = 1, $price = 0, $shipping, $surface, $n) {

        $montant_produit_ttc = $nb * round($price, 2) * round($surface, 2);

        @$this->panier[$n][$refproduit]['quantity'] -= $nb;
        @$this->panier[$n][$refproduit]['surface'] = round($surface, 2);
        @$this->panier_summary['total_shipping'] = round($this->panier_summary['total_shipping'], 2) - round($shipping, 2);
        @$this->panier_summary['total_taxes'] = round($this->panier_summary['total_taxes'], 2) - round($montant_produit_ttc - $montant_produit_ttc / 1.196, 2);
        @$this->panier_summary['total_produits'] = round($this->panier_summary['total_produits'], 2) - round($montant_produit_ttc, 2);
        @$this->panier_summary['total_amount'] = round($this->panier_summary['total_produits'], 2) + round($shipping, 2);


        if ($this->panier[$n][$refproduit]['quantity'] <= 0) {
            //remove option
            $option_amount = 0;
            if (isset($this->panier[$n][$refproduit]['options']))
                foreach ($this->panier[$n][$refproduit]['options'] as $k => $option) {
                    $option_amount += $option["quantity"] * $option["price"] * $option["surface"] + $option['shipping'];
                    $option_amount_produit += $option["quantity"] * $option["price"] * $option["surface"];
                }

            $this->panier_summary['total_shipping'] = round($this->panier_summary['total_shipping'], 2) - round($option['shipping'], 2);
            $this->panier_summary['total_taxes'] = round($this->panier_summary['total_taxes'], 2) - round($option_amount_produit - $option_amount_produit / 1.196, 2);
            $this->panier_summary['total_produits'] = round($this->panier_summary['total_produits'], 2) - round($option_amount_produit, 2);
            $this->panier_summary['total_amount'] = round($this->panier_summary['total_produits'], 2) + round($option['shipping'], 2);


            if ($this->panier_summary['total_taxes'] < 0)
                $this->panier_summary['total_taxes'] = 0;


            unset($this->panier[$n][$refproduit]);
            //array_splice($this->panier,1);
            /* $this->panier_summary['total_amount'] = 0;
              $this->panier_summary['total_shipping'] = 0;
              $this->panier_summary['total_taxes'] = 0;
              $this->panier_summary['total_produits'] = 0; */
        }
    }

    // choisir la quantit? d'article $refproduit
    public function setQuantity($refproduit = "", $toSet = "") {
        @$this->panier[$refproduit]['quantity'] = $toSet;
        if ($toSet <= 0)
            unset($this->panier[$refproduit]);
    }

    // afficher la quantit? de produits dans le panier
    // param?tre : $refproduit : permet d'afficher la quantit? pour le produit de cette r?f?rence
    // si le param?tre est vide, on affiche la quantit? totale de produit
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

    public function addVoucher($voucher = "") {
        $total_discount = 0;
        $discount = 0;
        $reduction = $voucher["reduction"];

        foreach ($_SESSION["cart"] as $i => $items) {
            foreach ($items as $ref => $item) {
                if (!empty($ref)) {
                    if ($item["productinfos"]["id_category"] == $voucher["value"]) {
                        $discount = round($_SESSION["cart"][$i][$ref]["prixttc"] * $reduction / 100, 2);
                        $total_discount = $discount;
                        $_SESSION["cart"][$i][$ref]["discount"] = $discount;
                        $_SESSION["cart"][$i][$ref]["voucher_code"] = $voucher["code"];
                    }
                }
            }
        }
        if ($total_discount >= 0) {
            $_SESSION["cart_summary"]["total_discount"] = $total_discount;
            $_SESSION["cart_summary"]["discount_title"] = $voucher["title"];
        }
    }

    public function applyProDiscount() {
        $total_discount = 0;
        $discount = 0;
        $reduction = 5;

        foreach ($_SESSION["cart"] as $i => $items) {
            foreach ($items as $ref => $item) {
                if (!empty($ref)) {
                    if (empty($item["pro_discounted"])) {
                        $discount = round($_SESSION["cart"][$i][$ref]["prixttc"] * $reduction / 100, 2);
                        $total_discount = $discount;
                        $_SESSION["cart"][$i][$ref]["discount"] = $discount;
                        $_SESSION["cart"][$i][$ref]["pro_discounted"] = 1;
                    }
                }
            }
        }
        if ($total_discount >= 0) {
            $_SESSION["cart_summary"]["total_discount"] = $total_discount;
        }
    }

    // afficher la liste des articles (et accessoirement, leur quantit?)

    public function showCart() {
        $list = array();
        //$i = 1;
        foreach ($this->panier as $i => $items) {
            foreach ($items as $ref => $data) {
                if (!empty($ref)) {
                    $list[$i]["id"] = $ref;
                    $list[$i]["quantity"] = $data['quantity'];
                    $list[$i]["shipping"] = $data['shipping'];
                    $list[$i]["surface"] = $data['surface'];
                    $list[$i]["dimension"] = $data['dimension'];
                    $list[$i]["productinfos"] = $data['productinfos'];
                    $list[$i]["price"] = $data['price'];
                    $list[$i]["name"] = $data['name'];
                    $list[$i]["prixttc"] = $data['prixttc'];
                    $list[$i]["discount"] = $data['discount'];
                    $list[$i]["voucher_code"] = $data['voucher_code'];
                    $list[$i]["pro_discounted"] = $data['pro_discounted'];

                    //les options
                    if (!empty($this->panier[$i][$ref]['options'])) {
                        foreach ($this->panier[$i][$ref]["options"] as $oref => $option) {
                            if (!empty($option['quantity']) && $option['quantity'] > 0) {
                                $list[$i]["options"][] = array("o_id" => $oref,
                                    "o_quantity" => $option['quantity'],
                                    "o_surface" => $option['surface'],
                                    "o_price" => $option['price'],
                                    "o_name" => $option['name'],
                                    "o_shipping" => $option['shipping'],
                                    "o_prixttc" => $option['prixttc'],
                                );
                                //$list[$i]["options"][] = $option;
                            }
                        }
                    }

                    $i++;
                }
            }
        }
        return $list;
    }

    public function getNbItems() {
        return(count($this->panier));
    }

    public function flush() {

        $this->panier = array();
        unset($_SESSION['cart']);
    }

}

// fin de la classe
?>