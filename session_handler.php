<?php
require_once '../models/session.php';
include_once '../config/db_handler.php';
class SessionsHandler {

  public $server_token;

  public function __construct(){
    $this->server_token = base64_encode("supersecretkey");
  }

  //check if logged in
  public function logged_in(){

    //get the token from the cookie
    if(isset($_COOKIE["auth_token"])){
      //set the token to a varialbe
      $token = $_COOKIE["auth_token"];

      // instantiate database
      $database = new DBhandler();
      $db = $database->connect();

      //create session and bind values
      $user_session = new Session();
      $user_session->token = $token;

      //set crud for session
      $crud = new Crud($db, $user_session);

      //check the database for the token
      if($crud->check_presence($user_session, "token")){
        $query = "SELECT * FROM " . $user_session->table_name . " WHERE token = :token";
        $stmt = $db->prepare($query);

        // sanitize
        $user_session->token=htmlspecialchars(strip_tags($user_session->token));

        // bind id of record to delete
        $stmt->bindParam(":token", $user_session->token);

        //check if it can execute
        if($stmt->execute()){
          //count rows return by statement
          $num = $stmt->rowCount();

          //check to see if any rows are returned
          if($num>0){
            $session_data = $stmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(200);

            //bind params to object
            $user_session->id = $session_data["id"];
            $user_session->token = $session_data["token"];
            $user_session->user_id = $session_data["user_id"];
            $user_session->created_at = $session_data["created_at"];

            //set timestamp for tomorrows date for server side time out
            $datetime = new DateTime('tomorrow');
            $timestamp = $datetime->format('Y-m-d G:i:s');

            //check against date
            if($timestamp >= $user_session->created_at){
              setcookie("auth_token", "", time()-3600);
              return true;
            }
            else{
              return false;
            }
          }
          else{
            return false;
          }
        }
        else{
          return false;
        }
      }
      else{
        return false;
      }
    }
    else{
      return false;
    }
  }

  //log in
  public function log_in($u, $s_token){
    // instantiate database
    $database = new DBhandler();
    $db = $database->connect();

    //create token for database
    $u_token = base64_encode($u->username . $u->password);
    $token = $u_token . "." . $s_token;

    //create session and bind values
    $user_session = new Session();
    $user_session->user_id = $u->id;
    $user_session->token = $token;

    //params for create fucntion in crud
    $params = array("user_id", "token");

    //set crud for sessions
    $crud = new Crud($db, $user_session);

    //create session
    if($crud->create($user_session, $params)){
      return $token;
    }
    else{
      //response code 503
      http_response_code(503);
      return json_encode(array("message" => "Unable to create session"));
    }
  }
}
