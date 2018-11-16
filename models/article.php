<?php
class Article{
    // database connection and table name
    private $conn;
    private $table_name = "articles";
    // object properties
    public $article_id;
    public $title;
    public $body;
    public $created_at;
    public $updated_at;
    // constructor with $db as database connection
    public function __construct($db){
      $this->conn = $db;
    }

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

    //create article
    function create(){
      //insert query
      $query = "INSERT INTO " . $this->table_name . "
                (title, body) VALUES(:title, :body)";
      //prepare query
      $stmt = $this->conn->prepare($query);
      //sanatize data
      $this->title=htmlspecialchars(strip_tags($this->title));
      $this->body=htmlspecialchars(strip_tags($this->body));
      //bind values
      $stmt-> bindParam(":title", $this->title);
      $stmt-> bindParam(":body", $this->body);
      //check if it can run
      if($stmt->execute()){
        return true;
      }
      return false;
    }
}
