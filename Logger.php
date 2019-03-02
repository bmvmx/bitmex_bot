<?php

class Logger {

  private $last;
  private $bandWidth;
  private $bandaH;
  private $bandaL;

  public function __construct($last, $bandWidth, $bandaH, $bandaL) {

    $this->last = $last;
    $this->bandWidth = $bandWidth;
    $this->bandaH = $bandaH;
    $this->bandaL = $bandaL;

  }

  public function logData(){
    error_log("Prezzo last: " .$this->last);
    error_log("Larghezza banda: " .$this->bandWidth);
    error_log("Banda superiore: " .$this->bandaH);
    error_log("Banda inferiore: " .$this->bandaL);
  }

}

?>