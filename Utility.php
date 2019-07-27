<?php

class Utility {


  public function __construct() {

    

  }

  public function threeCandlesLong($periodi, $candles){
    $subArray = array();
    $tempArray = $candles;
    $firstPositive = "-1";

    for ($i = 0; $i < count($candles); $i++) {
        $tmp_MA = new EMA($periodi, $tempArray);

        if (!($tmp_MA->isPositive())) {
            if ($firstPositive == "-1") {
                $firstPositive = "0";
            } else if ($firstPositive == "1") {
                break;
            }
            array_push($subArray, $candles[$i]);

        } else {
            if ($firstPositive == "-1") {
                $firstPositive = "1";
            } else if ($firstPositive == "0") {
                break;
            }

            array_push($subArray, $candles[$i]);

        }

        array_shift($tempArray);
    }

    $candeleP = 1;
    for ($i = 0; $i < (count($subArray)-1); $i++) {  
      if ($subArray[$i]['close'] > $subArray[$i]['open'] && $subArray[$i]['close'] > $subArray[$i+1]['close']) {
            $candeleP += 1;
        } else {
            $candeleP = 1;
        }

        if ($candeleP == 3) {
            return true;
        }
    }

    return false;
  }

  public function threeCandlesShort($periodi, $candles){
    $subArray = array();
    $tempArray = $candles;
    $firstPositive = "-1";

    for ($i = 0; $i < count($candles); $i++) {
        $tmp_MA = new EMA($periodi, $tempArray);

        if (!($tmp_MA->isPositive())) {
            if ($firstPositive == "-1") {
                $firstPositive = "0";
            } else if ($firstPositive == "1") {
                break;
            }
            array_push($subArray, $candles[$i]);

        } else {
            if ($firstPositive == "-1") {
                $firstPositive = "1";
            } else if ($firstPositive == "0") {
                break;
            }

            array_push($subArray, $candles[$i]);

        }

        array_shift($tempArray);
    }

    $candeleP = 1;
    for ($i = 0; $i < (count($subArray)-1); $i++) {
        if ($subArray[$i]['close'] < $subArray[$i]['open'] && $subArray[$i]['close'] < $subArray[$i+1]['close']) {
          $candeleP += 1;
        } else {
            $candeleP = 1;
        }

        if ($candeleP == 3) {
            return true;
        }
    }

    return false;
  }

  public function setupLong($AMA8, $AMA13, $AMA21){
    $val8 = $AMA8->getLastValue();
    $val13 = $AMA13->getLastValue();
    $val21 = $AMA21->getLastValue();

    if(($val13/$val8) < 0.9975 && ($val21/$val13) < 0.9975){
      return true;
    }
    return false;
  }

  public function setupShort($AMA8, $AMA13, $AMA21){
    $val8 = $AMA8->getLastValue();
    $val13 = $AMA13->getLastValue();
    $val21 = $AMA21->getLastValue();

    if(($val8/$val13) < 0.9975 && ($val13/$val21) < 0.9975){
      return true;
    }
    return false;
  }

  public function buildAnchor($candles){
    $anchorArray = array();
    $indexStart = false;

    for($i=(count($candles)-1); $i>=0; $i--){

      if($i<3){
        break;
      }

      $ora = substr ($candles[$i]['timestamp'], 11, 2 );
      if(!$indexStart && ($ora == "03" || $ora == "07" || $ora == "11" || $ora == "15" || $ora == "19" || $ora == "23")){
        $indexStart = true;
      }

      if($indexStart){
        $candela4h = array();
        $candela4h['open'] = $candles[$i]['open'];
        $candela4h['close'] = $candles[$i-3]['close'];
        array_unshift($anchorArray, $candela4h);
        $i = $i-3;
      }

    }
    
    return $anchorArray;
  }

  public function engulfingCandleLong($candles){
    $MA21 = new EMA(21, $candles);

    if($candles[0]['close'] > $candles[0]['open'] && $candles[1]['close'] < $candles[1]['open'] && $candles[0]['open'] > $MA21->getLastValue()){
      if($candles[0]['open'] <= $candles[1]['close'] && $candles[0]['close'] > $candles[1]['open']){
        $diffOCLast = $candles[0]['close'] - $candles[0]['open'];
        $diffHLLast = $candles[0]['high'] - $candles[0]['low'];
        $diffOCPrev = $candles[1]['close'] - $candles[1]['open'];
        $diffHLPrev = $candles[1]['high'] - $candles[1]['low'];

        if(abs($diffOCLast/$diffHLLast) >= 0.5 && abs($diffOCPrev/$diffHLPrev) >= 0.5){
          return true;
        }
      }
    }

    return false;
  }

  public function signalCandleShort($candles, $MA8, $MA13, $MA21){
    $diffOC = $candles[0]['close'] - $candles[0]['open'];
    $diffHL = $candles[0]['high'] - $candles[0]['low'];
    $diffCL = $candles[0]['close'] - $candles[0]['low'];

    if($candles[0]['open'] > $MA8->getLastValue() && $candles[0]['open'] < $MA21->getLastValue()){
      if($candles[0]['close'] < $MA8->getLastValue()){
        if(abs($diffCL/$diffHL) <= 0.25 && abs($diffOC/$diffHL) >= 0.5){
          return true;
        }        
      }
    }

    return false;

  }

  public function signalCandleLong($candles, $MA8, $MA13, $MA21){
    $diffOC = $candles[0]['close'] - $candles[0]['open'];
    $diffHL = $candles[0]['high'] - $candles[0]['low'];
    $diffCH = $candles[0]['close'] - $candles[0]['high'];

    if($candles[0]['open'] < $MA8->getLastValue() && $candles[0]['open'] > $MA21->getLastValue()){
      if($candles[0]['close'] > $MA8->getLastValue()){
        if(abs($diffCH/$diffHL) <= 0.25 && abs($diffOC/$diffHL) >= 0.5){
          return true;
        }        
      }
    }

    return false;

  }

  public function engulfingCandleShort($candles){
    $MA21 = new EMA(21, $candles);

    if($candles[0]['close'] < $candles[0]['open'] && $candles[1]['close'] > $candles[1]['open'] && $candles[0]['open'] < $MA21->getLastValue()){
      if($candles[0]['open'] >= $candles[1]['close'] && $candles[0]['close'] < $candles[1]['open']){
        
        $diffOCLast = $candles[0]['close'] - $candles[0]['open'];
        $diffHLLast = $candles[0]['high'] - $candles[0]['low'];
        $diffOCPrev = $candles[1]['close'] - $candles[1]['open'];
        $diffHLPrev = $candles[1]['high'] - $candles[1]['low'];
       
        if(abs($diffOCLast/$diffHLLast) >= 0.5 && abs($diffOCPrev/$diffHLPrev) >= 0.5){
          return true;
        }
      }
    }
    return false;
  }

  public function pinCandleLong($candles){
    $MA8 = new EMA(8, $candles);
    $MA13 = new EMA(13, $candles);
    $MA21 = new EMA(21, $candles);

    if($candles[0]['close'] > $MA8->getLastValue() && $candles[0]['low'] <= $MA13->getLastValue()){
      $diffOC = $candles[0]['close'] - $candles[0]['open'];
      $diffHL = $candles[0]['high'] - $candles[0]['low'];
      //$diffHC = $candles[0]['high'] - $candles[0]['close'];
      
      if(abs($diffOC/$diffHL) <= 0.3){
          return true;
      }
  
    }

    return false;

  }

  public function pinCandleShort($candles){
    $MA8 = new EMA(8, $candles);
    $MA13 = new EMA(13, $candles);
    $MA21 = new EMA(21, $candles);

    if($candles[0]['close'] < $MA8->getLastValue() && $candles[0]['high'] >= $MA13->getLastValue()){
      $diffOC = $candles[0]['close'] - $candles[0]['open'];
      $diffHL = $candles[0]['high'] - $candles[0]['low'];
      //$diffHC = $candles[0]['high'] - $candles[0]['close'];
      
      if(abs($diffOC/$diffHL) <= 0.3){
          return true;
      }
  
    }

    return false;

  }
 
}

?>