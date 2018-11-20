<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/db_handler.php';
include_once '../models/article.php';

// instantiate database and article object
$database = new DBhandler();
$db = $database->connect();

// initialize object
$article = new Article();
$crud = new Crud($db, $article);

//get data
$content = file_get_contents("php://input");
$data = json_decode($content);

//make sure data is not empty
if(!empty($data->id)){
  $article->id = $data->id;

  //query article id
  $stmt = $crud->read_one($article);
  $num = $stmt->rowCount();

  //check to see if there is a record
  if($num>0){
    //retrive contents
    $single_article = $stmt->fetch(PDO::FETCH_ASSOC);

    // set response code - 200 OK
    http_response_code(200);

    // show articles data in json format
    echo json_encode($single_article);
  }
  else{
    //data is incomplete
    http_response_code(400);
    echo json_encode(array("message" => "Article not found"));
  }

}
