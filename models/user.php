<?php
include_once '../crud.php';
class User {
  //Table name
  public  $table_name = "users";
  // object properties
  public $id;
  public $username;
  public $email;
  public $password;
  public $updated_at;
}
