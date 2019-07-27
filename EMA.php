<?php


class EMA {

  private $PERIODI;
  private $CANDLES;

  public function isPositive(){
    $arrayEMA = $this->exponentialMovingAverage();

    if($arrayEMA[0] > $arrayEMA[1]){
      return true;
    }
    return false;
  }

  public function getLastValue(){
    $arrayEMA = $this->exponentialMovingAverage();
    return $arrayEMA[0];
  }

  public function exponentialMovingAverage(): array
  {
    $numbers = $this->CANDLES;
    $n = $this->PERIODI;

    $numbers=array_reverse($numbers);
    $m   = count($numbers);
    $α   = 2 / ($n + 1);
    $EMA = [];

    // Start off by seeding with the first data point
    $EMA[] = $numbers[0]['close'];

    // Each day after: EMAtoday = α⋅xtoday + (1-α)EMAyesterday
    for ($i = 1; $i < $m; $i++) {
        $EMA[] = ($α * $numbers[$i]['close']) + ((1 - $α) * $EMA[$i - 1]);
    }
    $EMA=array_reverse($EMA);
    return $EMA;
  }

  public function __construct($periodi, $candles) {

    $this->PERIODI = $periodi;
    $this->CANDLES = $candles;


  }
 
}

?>