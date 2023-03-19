<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/quotes.php';

// instantiate database and quotes object
$database = new Database();
$db = $database->getConnection();
$quotes = new Quotes($db);

// get ID of quote to be read
$id = isset($_GET['id']) ? $_GET['id'] : die();

// read the details of quote to be read
$quote = $quotes->readSingle($id);

// check if the quote was found
if($quote) {
    // create array
    $quote_arr = array(
        "id" => $quote['id'],
        "quote" => $quote['quote'],
        "author" => $quote['author'],
        "category" => $quote['category']
    );

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($quote_arr);
}
else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user quote does not exist
    echo json_encode(array("message" => "Quote does not exist."));
}
?>
