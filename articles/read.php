<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/db_handler.php';
include_once '../models/article.php';

// instantiate database and article object
$database = new DBhandler();
$db = $database->connect();

// initialize object
$article = new Article();
$crud = new Crud($db, $article);

//limit amount of records being read
$query_limit = "5";

//get data
$content = file_get_contents("php://input");
$data = json_decode($content);

//make sure data is not empty
if(!empty($data->query_limit)){
    $query_limit = $data->query_limit;
  }
// query articles
$stmt = $crud->read($article, $query_limit);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // articles array
    $articles_arr=array();
    $articles_arr["records"]=array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);

        $article_item=array(
            "id" => $id,
            "title" => $title,
            "body" => $body
        );

        array_push($articles_arr["records"], $article_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show articles data in json format
    echo json_encode($articles_arr);
}
else{

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no articles found
    echo json_encode(
        array("message" => "No articles found.")
    );
}
