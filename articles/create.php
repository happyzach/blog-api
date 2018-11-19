<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db_handler.php';
include_once '../models/article.php';

// instantiate database and article object
$database = new DBhandler();
$db = $database->connect();

$article = new Article();
$crud = new Crud($db, $article);

//get data
$content = file_get_contents("php://input");
$data = json_decode($content);

//make sure data is not empty
if(
  !empty($data->title) &&
  !empty($data->body)
  ){
  //params to be entered into DB by user
  $params = array("title", "body");

  //set articles values
  $article->title = $data->title;
  $article->body = $data->body;

  //create article
  if($crud->create($article, $params)){
    //response code 201
    http_response_code(201);
    //alert user
    echo json_encode(array("message" => "Article was created"));
  }

  //tell user that it was not created
  else{

    //response code 503
    http_response_code(503);
    echo json_encode(array("message" => "Unable to create article"));
  }
}
else{
  //data is incomplete
  http_response_code(400);
  echo json_encode(array("message" => "Unable to create article. Something is missing"));
}
