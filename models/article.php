<?php
include_once '../crud.php';
class Article{
  //Table name
  public  $table_name = "articles";
  // object properties
  public $id;
  public $title;
  public $body;
  public $created_at;
  public $updated_at;
}
