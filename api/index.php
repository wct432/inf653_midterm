<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
  header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
  exit();
} 

require_once("../objects/quotes.php");
require_once("../config/database.php");
require_once("../routing/quotes.php");
require_once("../routing/authors.php");
require_once("../routing/categories.php");

// Define the base path for the API
define('BASE_PATH', '/api/');

// Set the content type header for JSON responses
header('Content-Type: application/json');

// Check if the request URI starts with the API base path
if (strpos($_SERVER['REQUEST_URI'], BASE_PATH) !== 0) {
  http_response_code(404);
  echo json_encode(array('error' => 'Endpoint not found'));
  exit();
}

// Remove the API base path from the request URI
$request_uri = substr($_SERVER['REQUEST_URI'], strlen(BASE_PATH));

// Split the request URI into an array of path segments
$path_segments = explode('/', $request_uri);

// Determine the HTTP method of the request
$method = $_SERVER['REQUEST_METHOD'];

// instantiate database object
$database = new Database();
$db = $database->getConnection();

function isJson($string) {
  json_decode($string);
  return json_last_error() === JSON_ERROR_NONE;
}

// Switch on the path and HTTP method to determine the appropriate action
switch ($path_segments[0]) {

  case 'quotes':
   get_quote_result($path_segments, $method, $db);
    break;

  case 'authors':
    get_author_result($path_segments, $method, $db);
    break;

  case 'categories':
    get_category_result($path_segments, $method, $db);
    break;

  default:
    http_response_code(404);
    echo json_encode(array('error' => 'Endpoint not found'));
    break;
}
