<?php
include_once '../config/db_handler.php';
class Crud {
  //Connection and table variables
  private $conn;
  private $table_name;

  //constructor to setup crud object
  public function __construct($db, $obj){
    $this->conn = $db;
    $this->table_name = $obj->table_name;
  }
  //create record
  function create($obj, $params){
    //Silliness to convert array to workable strings to pass in sql query
    $params_1 = implode(',', $params);
    //prepends colon to each array item
    $params_2 = explode(",", (":".implode(",:", $params)));
    //creates string for query with colon
    $params_3 = implode(",", $params_2);
    //insert query
    $query = "INSERT INTO " . $this->table_name . "
    ($params_1) VALUES($params_3)";
    //prepare query
    $stmt = $this->conn->prepare($query);
    //sanatize data and bind VALUES
    foreach($params as $param){
      $obj->$param=htmlspecialchars(strip_tags($obj->$param));
      //prepends colon to param for pdo binding
      $bind_param = ":".$param;
      $stmt-> bindParam($bind_param, $obj->$param);
    }
    //check if it can run return true if it can
    if($stmt->execute()){
      return true;
    }
    return false;
  }
}
