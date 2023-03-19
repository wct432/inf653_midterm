<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and object files
include_once '../config/database.php';
include_once '../objects/quote.php';

// Instantiate database and quote object
$database = new Database();
$db = $database->getConnection();
$quote = new Quote($db);

// Get the quote ID from the URL parameter
$id = isset($_GET['id']) ? $_GET['id'] : die();

// Set the ID property of the quote to be deleted
$quote->id = $id;

// Delete the quote
if ($quote->delete()) {
    // Set response code - 200 OK and tell the user
    http_response_code(200);
    echo json_encode(array("message" => "Quote was deleted."));
} else {
    // Set response code - 503 Service Unavailable and tell the user
    http_response_code(503);
    echo json_encode(array("message" => "Unable to delete quote."));
}
?>
