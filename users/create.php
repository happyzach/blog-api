<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db_handler.php';
include_once '../models/user.php';

// instantiate database and user object
$database = new DBhandler();
$db = $database->connect();

$user = new User();
$crud = new Crud($db, $user);

//get data
$content = file_get_contents("php://input");
$data = json_decode($content);

//make sure data is not empty
if(
  !empty($data->username) &&
  !empty($data->email) &&
  !empty($data->password)
  ){

    //set users values
    $user->username = $data->username;
    $user->email = $data->email;

    //check if email is taken
    if(
      $crud->check_presence($user, "email") ||
      $crud->check_presence($user, "username")
      ){
      //could not be created
      //response code 503
      http_response_code(503);
      echo json_encode(array("message" => "email or username is taken"));
      }

      else{
        //params to be entered into DB by user
        $params = array("username", "email", "password");

        //encrypt and set password
        $password = password_hash($data->password, PASSWORD_DEFAULT);
        $user->password = $password;

        //create user
          if($crud->create($user, $params)){
            //response code 201
            http_response_code(201);
            //alert user
            echo json_encode(array("message" => "user was created"));
          }
          else{
            //email or username is taken is taken
            //response code 503
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create user"));
          }
        }
  }
  else{
    //data is incomplete
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user. Something is missing"));
}
