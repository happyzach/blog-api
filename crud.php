<?php
class Crud {
  //Connection and table variables
  private $conn;
  private $table_name;
  private $query_params;
  private $value_params;
  private $stmt;

  //constructor to setup crud object
  public function __construct($db, $obj){
    $this->conn = $db;
    $this->table_name = $obj->table_name;
  }

  //create record
  function create($obj, $params){
    //turn params into workable string for query
    $this->stringify_query_params($params);

    //insert query
    $query = "INSERT INTO " . $this->table_name . "
    ($this->query_params) VALUES($this->value_params)";

    // prepare query strip html and bind params
    $this->prepare_queryData($obj, $query, $params);

    //check if it can run return true if it can
    if($this->stmt->execute()){
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

  // read records
  function read_one($obj){

    // select all query for single id
    $query = "SELECT * FROM " . $obj->table_name . " WHERE id = :id";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $obj->id=htmlspecialchars(strip_tags($obj->id));

    // bind id of record to delete
    $stmt->bindParam(":id", $obj->id);

    // execute query
    if($stmt->execute()){
       return $stmt;
    }
    return false;
  }

  // Update records
  function update($obj, $params){

    //start query variable
    $query = "UPDATE " . $this->table_name . " SET ";

    //create set params for update query
    foreach($params as $param){
      $query .= $param . " = :" . $param ;
      //puts comma after each value if it is not the last in the list
      if($param != end($params)){
        $query .= ",";
      }
    }

    //end query and set id
    $query .= " WHERE id = :id";

    // prepare query strip html and bind params
    $this->prepare_queryData($obj, $query, $params);

    //Bind the ID of the object
    $this->stmt-> bindParam(":id", $obj->id);

    //check if it can run return true if it can
    if($this->stmt->execute()){
      return true;
    }
    return false;
  }

  // Delete record
  function delete($obj){
    //check if it exists and is then able to delete it if so
    if($this->check_presence($obj, "id")){
      // delete query
      $query = "DELETE FROM " . $obj->table_name . " WHERE id = :id";

      // prepare query
      $stmt = $this->conn->prepare($query);

      // sanitize
      $obj->id=htmlspecialchars(strip_tags($obj->id));

      // bind id of record to delete
      $stmt->bindParam(":id", $obj->id);

      // execute query
      if($stmt->execute()){
        return true;
      }
      return false;
    }
    else{
      return false;
    }
  }

  function check_presence($obj, $param){

    //query to check count of param
    $query = "SELECT * FROM " . $obj->table_name . " WHERE " . $param .   " = ?" ;

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $obj->$param=htmlspecialchars(strip_tags($obj->$param));

    // execute query
    $stmt->execute([$obj->$param]);

    if($stmt->rowCount()>0){
      return true;
    }
    else{
      return false;
    }
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

  function prepare_queryData($o, $q, $p){
    //prepare query
    $stmt = $this->conn->prepare($q);
    //sanatize data and bind VALUES
    foreach($p as $param){
      $o->$param=htmlspecialchars(strip_tags($o->$param));
      //prepends colon to param for pdo binding
      $bind_param = ":".$param;
      $stmt-> bindParam($bind_param, $o->$param);
    }
    //set the stmt variable
    $this->stmt = $stmt;
  }
}
