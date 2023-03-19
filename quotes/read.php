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
echo("!!!!!!!!!!!!!!!!!!!!");
// query quotes
$stmt = $quotes->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0) {
    // quotes array
    $quotes_arr = array();
    $quotes_arr["quotes"] = array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract row
        extract($row);

        // create array
        $quote_item = array(
            "id" => $id,
            "quote" => $quote,
            "author" => $author,
            "category" => $category
        );

        array_push($quotes_arr["quotes"], $quote_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($quotes_arr);
}
else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no quotes found
    echo json_encode(array("message" => "No quotes found."));
}
?>
