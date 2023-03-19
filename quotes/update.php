<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/database.php';
include_once '../objects/quotes.php';

// instantiate database and quotes object
$database = new Database();
$db = $database->getConnection();
$quotes = new Quotes($db);

// get id of quote to be edited
$data = json_decode(file_get_contents("php://input"));

// check that the required data is present
if (!empty($data->id) && !empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
    // set quotes property values
    $quotes->id = $data->id;
    $quotes->quote = $data->quote;
    $quotes->author_id = $data->author_id;
    $quotes->category_id = $data->category_id;

    // update the quote
    if($quotes->update()){
        // set response code - 200 OK
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "Quote was updated."));
    }
    // if unable to update the quote, tell the user
    else{
        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to update quote."));
    }
}
else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to update quote. Required data is missing."));
}
?>
