<?php
class DBhandler {
  //variables that hold
  private $host = "localhost";
  private $username = "root";
  private $password = "";
  private $dbname = "zblog_dev";
  public $conn;

  public function connect(){
    // Create connection
    try{
        $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
        $this->conn->exec("set names utf8");
    }catch(PDOException $exception){
        echo "Connection error: " . $exception->getMessage();
    }

    return $this->conn;

  }
}
