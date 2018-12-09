<?php


class Donchian {

  private $PERIODI = 16;
  private $TIMEFRAME = "1h";
  private $bitmex;

  public function __construct($bitmex) {

    $this->bitmex = $bitmex;

  }

  public function getBandaH(){
    $candele = $this->bitmex->getCandles($this->TIMEFRAME, $this->PERIODI);
    $bandaH = $candele[0]['high'];

    for($i=0; $i<count($candele); $i++){
      if($candele[$i]['high'] > $bandaH){
          $bandaH = $candele[$i]['high'];
      }
      
    }

    return floatval($bandaH);

  }

  public function getBandaL(){
    $candele = $this->bitmex->getCandles($this->TIMEFRAME, $this->PERIODI);
    $bandaL = $candele[0]['low'];

    for($i=0; $i<count($candele); $i++){
      if($candele[$i]['low'] < $bandaL){
          $bandaL = $candele[$i]['low'];
      }
      
    }

    return floatval($bandaL);

  }

  public function getBandWidth(){
    return ( 1.0- ($this->getBandaL()/$this->getBandaH()));

  }
 
}

?>