<?php


class Validator {

  private $currentPrice;
  private $posizioniAperte;

  public function __construct($currentPrice, $posizioniAperte) {

    $this->currentPrice = $currentPrice;
    $this->posizioniAperte = $posizioniAperte;


  }

  public function validate(){
    if(isset($this->currentPrice) && isset($this->posizioniAperte)){
      return true;
    }
    return false;
  }

}

?>