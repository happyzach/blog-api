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
  function create($obj){
    //insert query
    $query = "INSERT INTO " . $this->table_name . "
    (title, body) VALUES(:title, :body)";
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
