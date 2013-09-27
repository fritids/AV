<?php

/*********************************
*                                *
* PayPal PHP Manager             *
* By Jeremy Desvaux              *
* June 2010                      *
* Creative Commons               *
*                                *
**********************************/

class paypalcheckout {

    //==> Class Variables <==/
    public $business;
    public $currency;
    public $cursymbol;
    public $location;
    public $returnurl;
    public $returntxt;
    public $cancelurl;
    public $items;

//=======================================================================//
//==> Class constructor, with default settings that can be overridden <==//
//=======================================================================//
public function __construct($config=""){

  //default settings
  $settings = array(
  'business' => 'alamichel.s@free.fr',         //paypal email address
  'currency' => 'EUR',                       //paypal currency
  'cursymbol'=> '&euro;',                   //currency symbol
  'location' => 'FR',                        //location code  (ex GB)
  'returnurl'=> 'http://moka-web.net/allovitres/',//where to go back when the transaction is done.
  'returntxt'=> 'Retour au site',         //What is written on the return button in paypal
  'cancelurl'=> 'http://moka-web.net/allovitres/',//Where to go if the user cancels.
  'shipping' => 0,                           //Shipping Cost
  'custom'   => ''                           //Custom attribute
  );

  //overrride default settings
  if(!empty($config)){foreach($config as $key=>$val){
    if(!empty($val)){ $settings[$key] = $val; }
  }}

  //Set the class attributes
  $this->business  = $settings['business'];
  $this->currency  = $settings['currency'];
  $this->cursymbol = $settings['cursymbol'];
  $this->location  = $settings['location'];
  $this->returnurl = $settings['returnurl'];
  $this->returntxt = $settings['returntxt'];
  $this->cancelurl = $settings['cancelurl'];
  $this->shipping  = $settings['shipping'];
  $this->custom    = $settings['custom'];
  $this->items = array();

}


    //=====================================//
    //==> Add a simple item to the cart <==//
    //=====================================//
    public function addSimpleItem($item){		
      if( //Check the quantity and the name
        !empty($item['quantity']) 
        && is_numeric($item['quantity']) 
        && $item['quantity']>0 
        && !empty($item['name'])
      ){ //And add the item to the cart if it is correct
        $items = $this->items;
        $items[] = $item;
        $this->items = $items;
      }
    }


    //=========================================//
    //==> Add an array of items to the cart <==//
    //=========================================//
    public function addMultipleItems($items){
        if(!empty($items)){foreach($items as $item){ //lopp through the items
            $this->addSimpleItem($item);  //And add them 1 by 1
        }}
    }


    //==================================================//
    //==> Returns a summary list of the cart content <==//
    //==================================================//
    public function getCartContentAsHtml($hidetotal=0){
        $content='<ul id="cartcontent">'; $total=0; $count = 0;
        $cpt=1;
        if(!empty($this->items)){foreach($this->items as $item){
          $amount = $item['quantity']*$item['price'];
          $content.='<li class="cartitem">'.$item['quantity'].' x "'.$item['name'].'" at '.$this->cursymbol.''.$item['price'];
          if($item['shipping']>0)$content.= ' + '.$this->cursymbol.''.$item['shipping'].' shipping ';
          $content.=' for '.$this->cursymbol.''.$amount;
          $content.='</li>';
          $total+=$amount;
          $count+=$item['quantity'];
          $cpt++;
       }}	
       if($hidetotal!=1){ $content.='<li class="carttotal">Total: '.$count.' Items for '.$this->cursymbol.''.$total.'</li>'; }
       $content.='</ul>';
       return $content;
    }

    //=====================//
    //==> Checkout Form <==//
    //=====================//
    public function getCheckoutForm(){

        $form='            
        <form id="paypal_checkout" action="https://www.paypal.com/cgi-bin/webscr" method="post">';

        //==> Variables defining a cart, there shouldn't be a need to change those <==//
        $form.='
        <input type="hidden" name="cmd" value="_cart" />
        <input type="hidden" name="upload" value="1" />			
        <input type="hidden" name="no_note" value="0" />						
        <input type="hidden" name="bn" value="PP-BuyNowBF" />					
        <input type="hidden" name="tax" value="0" />			
        <input type="hidden" name="rm" value="2" />';
    
        //==> Personnalised variables, they get their values from the specified settings nd the class attributes <==//
       $form.='
       <input type="hidden" name="business" value="'.$this->business.'" />
       <input type="hidden" name="handling_cart" value="'.$this->shipping.'" />
       <input type="hidden" name="currency_code" value="'.$this->currency.'" />
       <input type="hidden" name="lc" value="'.$this->location.'" />
       <input type="hidden" name="return" value="'.$this->returnurl.'" />			
       <input type="hidden" name="cbt" value="'.$this->returntxt.'" />
       <input type="hidden" name="cancel_return" value="'.$this->cancelurl.'" />			
       <input type="hidden" name="custom" value="'.$this->custom.'" />';

       //==> The items of the cart <==//
       $cpt=1;
       if(!empty($this->items)){foreach($this->items as $item){
         $form.='
          <div id="item_'.$cpt.'" class="itemwrap">
            <input type="hidden" name="item_name_'.$cpt.'" value="'.$item['name'].'" />
            <input type="hidden" name="quantity_'.$cpt.'" value="'.$item['quantity'].'" />
            <input type="hidden" name="amount_'.$cpt.'" value="'.$item['price'].'" />
           <input type="hidden" name="shipping_'.$cpt.'" value="'.$item['shipping'].'" />
         </div>';
         $cpt++;
       }}

       //==> The submit button, (you can specify here your own button) <==//
       $form.='
         <input id="ppcheckoutbtn" type="submit" value="Checkout" class="button" />
       </form>';

       return $form;
    }    
}


?>