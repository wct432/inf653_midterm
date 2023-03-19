<?php

// include database and object files
include_once '../config/database.php';
include_once '../objects/quotes.php';

// instantiate database and quotes object
$database = new Database();
$db = $database->getConnection();
$quotes = new Quotes($db);

// query quotes
$stmt = $quotes->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // quotes array
    $quotes_arr=array();
    $quotes_arr["records"]=array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // extract row
        extract($row);

        $quote_item=array(
            "id" => $id,
            "quote" => $quote,
            "author" => $author_name,
            "category" => $category_name
        );

        array_push($quotes_arr["records"], $quote_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show quotes data in json format
    echo json_encode($quotes_arr);
}

// no quotes found will be here
else{

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no quotes found
    echo json_encode(
        array("message" => "No quotes found.")
    );
}
?>
