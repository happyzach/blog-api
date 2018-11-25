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
$user = new User();
$crud = new Crud($db, $user);

//get data
$content = file_get_contents("php://input");
$data = json_decode($content);

//make sure data is not empty
if(!empty($data->id)){
  $user->id = $data->id;

  //query user id
  $stmt = $crud->read_one($user);
  $num = $stmt->rowCount();

  //check to see if there is a record
  if($num>0){
    //retrive contents
    $single_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // set response code - 200 OK
    http_response_code(200);

    // show users data in json format
    echo json_encode($single_user);
  }
  else{
    //data is incomplete
    http_response_code(400);
    echo json_encode(array("message" => "user not found"));
  }

}
