<?php
class Crud {
  //Connection and table variables
  private $conn;
  private $table_name;
  private $query_params;
  private $value_params;

  //constructor to setup crud object
  public function __construct($db, $obj){
    $this->conn = $db;
    $this->table_name = $obj->table_name;
  }
  //create record
  function create($obj, $params){
    $this->stringify_query_params($params);
    //insert query
    $query = "INSERT INTO " . $this->table_name . "
    ($query_params) VALUES($value_params)";
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

  // read records
  function read($obj, $quantity){
    // select all query
    $query = "SELECT * FROM " . $this->table_name . " LIMIT " . $quantity;
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    // execute query
    $stmt->execute();
    return $stmt;
  }

  // Update records
  function update($obj, $params, $id){
    //update query
    $query = "UPDATE" . $this->table_name . "";
  }

  function stringify_query_params($p){
    //Silliness to convert array to workable strings to pass in sql query
    $params_1 = implode(',', $p);
    //prepends colon to each array item
    $params_2 = explode(",", (":".implode(",:", $p)));
    //creates string for query with colon
    $params_3 = implode(",", $params_2);
    $this->query_params = $params_1;
    $this->value_params = $params_3;
  }
}
