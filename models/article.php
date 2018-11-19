<?php
include '../crud.php';
class Article{
  //Table name
  public  $table_name = "articles";
  // object properties
  public $article_id;
  public $title;
  public $body;
  public $created_at;
  public $updated_at;


    // read articles
    function read(){
      // select all query
      $query = "SELECT * FROM " . $this->table_name;
      // prepare query statement
      $stmt = $this->conn->prepare($query);
      // execute query
      $stmt->execute();
      return $stmt;
    }
}
