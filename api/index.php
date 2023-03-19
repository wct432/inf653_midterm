<?php

require_once("../objects/quotes.php");
require_once("../config/database.php");

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

// Switch on the path and HTTP method to determine the appropriate action
switch ($path_segments[0]) {
  case 'quotes':
    // include_once('objects/quotes.php');
    $quotes = new Quotes($db);
    switch ($method) {
      case 'GET':
        if (isset($_GET['id'])) {
          $result = $quotes->read_single($_GET['id']);
        } else if (isset($_GET['author_id'])) {
          // Get author_id value from query string
          $author_id = $_GET['author_id'];
          // Call function to retrieve all quotes from author_id
          $result = $quotes->get_quotes_by_author($author_id);
        } else if (isset($_GET['category_id'])){
          $category_id = $_GET['category_id'];
          $result = $quotes->get_quotes_by_category($category_id);
        }else {
          $result = $quotes->read();
        }
        break;
      case 'POST':
        $data = json_decode(file_get_contents('php://input'));
        $result = $quotes->create($data->quote, $data->author_id, $data->category_id);
        break;
      case 'PUT':
        if (isset($path_segments[1])) {
          $data = json_decode(file_get_contents('php://input'));
          $result = $quotes->update($path_segments[1], $data->quote, $data->author_id, $data->category_id);
        } else {
          http_response_code(400);
          $result = array('error' => 'Missing quote ID');
        }
        break;
      case 'DELETE':
        if (isset($path_segments[1])) {
          $result = $quotes->delete($path_segments[1]);
        } else {
          http_response_code(400);
          $result = array('error' => 'Missing quote ID');
        }
        break;
      default:
        http_response_code(405);
        $result = array('error' => 'Invalid method');
    }
    echo json_encode($result);
    break;

  case 'authors':
    // Handle requests for the authors endpoint
    // ...
    break;

  case 'categories':
    // Handle requests for the categories endpoint
    // ...
    break;

  default:
    http_response_code(404);
    echo json_encode(array('error' => 'Endpoint not found'));
    break;
}
