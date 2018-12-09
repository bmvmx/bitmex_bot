<?php


class MovingAverage {

  private $PERIODI = 200;
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

    $somma = 0; 
    $numCandele = count($candele);

    for($i=0; $i<$numCandele; $i++){
      $somma += $candele[$i]['close'];


    }

    return floatval($somma/$numCandele);

  }

  public function getLastTwoValues() {

    $candele = $this->bitmex->getCandles($this->TIMEFRAME, (($this->PERIODI)+1));

    $somma = 0; 
    $numCandele = count($candele);

    for($i=0; $i<($numCandele-1); $i++){
      $somma += $candele[$i]['close'];
    }   

    $media1 = $somma/($numCandele-1);


    $somma = 0; 
    for($i=1; $i<$numCandele; $i++){
      $somma += $candele[$i]['close'];

    }

    $media2 = $somma/($numCandele-1);
    

    $stack = array(floatval($media1), floatval($media2));
    return $stack;

  }

  public function __construct($bitmex) {

    $this->bitmex = $bitmex;

  }
 
}

?>