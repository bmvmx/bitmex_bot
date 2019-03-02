<?php


class WMA {

  private $PERIODI = 80;
  private $TIMEFRAME = "1h";
  private $bitmex;

  public function isPositive(){
    $resMedia = $this->getLastTwoValues();
    if((floatval($resMedia[0]) - floatval($resMedia[1])) > 0){
      return true;
    }
    return false;
  }
  

  public function getLastValue() {

    
    $candele = $this->bitmex->getCandles($this->TIMEFRAME, $this->PERIODI);

    $numCandele = count($candele);

    $numeratore = 0; 
    $denominatore = floatval($numCandele * ($numCandele+1))/2;

    for($i=0; $i<$numCandele; $i++){
      $numeratore += floatval($candele[$i]['close']*($numCandele-$i));

    }


    return floatval($numeratore/$denominatore);

  }

  public function getLastTwoValues() {

    $candele = $this->bitmex->getCandles($this->TIMEFRAME, (($this->PERIODI)+1));
    $numCandele = count($candele);
    $numCandeleEFF = $numCandele-1;

    $numeratore = 0; 
    $denominatore = floatval($numCandeleEFF * ($numCandeleEFF+1))/2;

    for($i=0; $i<($numCandele-1); $i++){
      $numeratore += ($candele[$i]['close']*($numCandeleEFF-$i));
    }   

    $media1 = $numeratore/$denominatore;


    $numeratore = 0; 
    for($i=1; $i<$numCandele; $i++){
      $numeratore += ($candele[$i]['close']*($numCandeleEFF-($i-1)));

    }

    $media2 = $numeratore/$denominatore;
    

    $stack = array(floatval($media1), floatval($media2));
    return $stack;

  }

  public function __construct($bitmex) {

    $this->bitmex = $bitmex;

  }
 
}

?>