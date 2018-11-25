<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/db_handler.php';
include_once '../models/user.php';

// instantiate database and user object
$database = new DBhandler();
$db = $database->connect();

// initialize object
$user = new User();
$crud = new Crud($db, $user);

//limit amount of records being read
$query_limit = "100";

//get data
$content = file_get_contents("php://input");
$data = json_decode($content);

//make sure data is not empty
if(!empty($data->query_limit)){
    $query_limit = $data->query_limit;
  }
// query users
$stmt = $crud->read($user, $query_limit);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // users array
    $users_arr=array();
    $users_arr["records"]=array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);

        $user_item=array(
            "id" => $id,
            "username" => $username,
            "email" => $email
        );

        array_push($users_arr["records"], $user_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show users data in json format
    echo json_encode($users_arr);
}
else{

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no users found
    echo json_encode(
        array("message" => "No users found.")
    );
}
