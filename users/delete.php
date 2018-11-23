<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/db_handler.php';
include_once '../models/user.php';

// instantiate database and user object
$database = new DBhandler();
$db = $database->connect();

// initialize object
$user = new user();
$crud = new Crud($db, $user);

//get data
$content = file_get_contents("php://input");
$data = json_decode($content);

//make sure data is not empty
if(!empty($data->id)){
  $user->id = $data->id;

  //delete record
  if($crud->delete($user)){
    //response code 201
    http_response_code(201);
    //alert user
    echo json_encode(array("message" => "user was deleted"));
  }

  //tell user that it was not deleted
  else{

    //response code 503
    http_response_code(503);
    echo json_encode(array("message" => "Unable to delete user"));
  }
}
else{
  //data is incomplete
  http_response_code(400);
  echo json_encode(array("message" => "Unable to delete user. Something is missing"));
}
