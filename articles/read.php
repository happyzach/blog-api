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
$article = new Article($db);

// query articles
$stmt = $article->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // products array
    $articles_arr=array();
    $articles_arr["records"]=array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $article_item=array(
            "article_id" => $article_id,
            "title" => $title,
            "body" => $body
        );

        array_push($articles_arr["records"], $article_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show products data in json format
    echo json_encode($articles_arr);
}
else{

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no products found
    echo json_encode(
        array("message" => "No articles found.")
    );
}
