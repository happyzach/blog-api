<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../session_handler.php';

//pull in session handler
$sess = new SessionsHandler();

if($sess->logged_in()){
  //if they are already logged in
  echo json_encode(array('message' => 'You are already logged in'));
}
else{
  // if not log them in
  include_once '../config/db_handler.php';
  include_once '../models/user.php';

  // instantiate database
  $database = new DBhandler();
  $db = $database->connect();

  $user = new User();
  $crud = new Crud($db, $user);


  //get data
  $content = file_get_contents("php://input");
  $data = json_decode($content);

  //give the client a server side token
  $_SERVER["HTTP_SERVER_TOKEN"] = $sess->server_token;

  //check for email and password
  if(
    !empty($data->email) &&
    !empty($data->password)
  ){
    //set users values
    $user->email = $data->email;

    //check if email is taken
    if(
      $crud->check_presence($user, "email")
    ){
      //email exists in database so we need to pull in that user
      $query = "SELECT * FROM " . $user->table_name . " WHERE email = :email";
      $stmt = $db->prepare($query);

      // sanitize
      $user->email=htmlspecialchars(strip_tags($user->email));

      // bind id of record to delete
      $stmt->bindParam(":email", $user->email);

      if($stmt->execute()){
        //count rows return by statement
        $num = $stmt->rowCount();

        if($num>0){
          $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

          http_response_code(200);

          //bind params to object
          $user->id           = $user_data["id"];
          $user->username     = $user_data["username"];
          $user->email        = $user_data["email"];
          $user->password     = $user_data["password"];
          $user->created_at   = $user_data["created_at"];
          $user->updated_at   = $user_data["updated_at"];

          //verify password
          if(password_verify($data->password, $user->password)){
            //password matches now log the user in
            //login by creating a session
            $token = $sess->log_in($user, $_SERVER["HTTP_SERVER_TOKEN"]);

            //create cookie for auth token
            $cookie_name = "auth_token";
            $cookie_value = $token;

            //86400 = 1 day
            setcookie($cookie_name, $cookie_value, time() + (86400), "/");

            http_response_code(200);
            echo json_encode(array("message" => "Successfully signed in."));

          }
          else{
            //password not correct
            http_response_code(400);
            echo json_encode(array("message" => "Username or Password is incorrect!"));
          }

        }
      }
      else{
        //something went wrong getting and assigning user
        http_response_code(503);
        echo json_encode(array("message" => "Something went wrong getting user"));
      }

    }
    else{
      //could not be created email doesn't exist
      http_response_code(503);
      echo json_encode(array("message" => "email does not exist"));
    }
  }
  else{
    //user did not provide enough info
    http_response_code(400);
    echo json_encode(array("message" => "Unable to login must provide email and password"));
  }
}
