<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
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

//params to be entered into DB by user
$params = array();

//Check for id passed to update record
if(!empty($data->id)){

  //set article id
  $article->id = $data->id;

  //check for individual params and add to param list
  if(!empty($data->title)){
    array_push($params, "title");
    $article->title = $data->title;
  }
  if(!empty($data->body)){
    array_push($params, "body");
    $article->body = $data->body;
  }

  //update updated_at to today
  $timestamp = date('Y-m-d G:i:s');
  array_push($params, "updated_at");
  $article->updated_at = $timestamp;

  //update article
  if($crud->update($article, $params)){
    //response code 201
    http_response_code(201);
    //alert user
    echo json_encode(array("message" => "Article was updated"));
  }
  //tell user that it was not created
  else{
    //response code 503
    http_response_code(503);
    echo json_encode(array("message" => "Unable to update article"));
  }
}
else{
  //data is incomplete
  http_response_code(400);
  echo json_encode(array("message" => "Unable to create article. no article given to update"));
}
