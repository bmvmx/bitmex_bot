<?php


class DBHandler {

  private $conn = null;
 

  public function __construct() {

    $this->conn = new mysqli("localhost", "root", "", "test");

  }

  function getField($field) {
    $sql = "select " .$field ." from posizione_aperta where indice = 0";
    return $this->conn->query($sql)->fetch_assoc()[$field];
  }

  function updateField($field, $numero){
    $sql = "update posizione_aperta set " .$field ." = " .$numero ." where indice = 0";
    if($this->conn->query($sql) == true){
      echo "update success";
    } 
  }


 
}

?>