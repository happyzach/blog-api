<?php
include_once '../crud.php';
class Session{
  //Table name
  public  $table_name = "sessions";
  // object properties
  public $id;
  public $token;
  public $user_id;
  public $created_at;
}
