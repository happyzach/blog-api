<?php
include_once '../config/db_handler.php';
class Crud {
  private $conn;
  private $table_name;

  public function __construct($db, $obj){
    $this->conn = $db;
    $this->table_name = $obj->table_name;
  }
  //create record
  function create($obj, $params){
    //Silliness to convert array to workable strings to pass in sql query
    $params_1 = implode(',', $params);
    $params_2 = explode(",", (":".implode(",:", $params)));
    $params_2 = implode(",", $params_2);
    //insert query
    $query = "INSERT INTO " . $this->table_name . "
    ($params_1) VALUES($params_2)";
    //prepare query
    $stmt = $this->conn->prepare($query);
    //sanatize data
    $obj->title=htmlspecialchars(strip_tags($obj->title));
    $obj->body=htmlspecialchars(strip_tags($obj->body));
    //bind values
    $stmt-> bindParam(":title", $obj->title);
    $stmt-> bindParam(":body", $obj->body);
    //check if it can run
    if($stmt->execute()){
      return true;
    }
    return false;
  }
}
