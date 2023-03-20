<?php

require_once("../objects/quotes.php");

function get_quote_result($path_segments, $method, $db){

// include_once('objects/quotes.php');
    $quotes = new Quotes($db);
    switch ($method) {
      case 'GET':
        // GET BY ID
        if (isset($_GET['id'])) {
          $result = $quotes->read_single($_GET['id']);
        } 
        // GET BY AUTHOR AND CATEGORY ID
        else if ((isset($_GET['author_id'])) & isset($_GET['category_id'])){
          $author_id = $_GET['author_id'];
          $category_id = $_GET['category_id'];
          $result = $quotes->get_by_auth_and_cat($author_id, $category_id);

        // GET BY AUTHOR ID
        } else if (isset($_GET['author_id'])) {
          $author_id = $_GET['author_id'];
          $result = $quotes->get_quotes_by_author($author_id);

        // GET BY CATEGORY ID
        } else if (isset($_GET['category_id'])){
          $category_id = $_GET['category_id'];
          $result = $quotes->get_quotes_by_category($category_id);

        // ELSE NO PARAMS WERE PASSED, DEFAULT GET EVERYTHING
        }else {
          $result = $quotes->read();
        }
        break;

      // POST LOGIC
      case 'POST':
        $data = json_decode(file_get_contents('php://input'));
        $result = $quotes->create($data->quote, $data->author_id, $data->category_id);
        break;

      // PUT LOGIC
      case 'PUT':
        if (isset($path_segments[1])) {
          $data = json_decode(file_get_contents('php://input'));
          $result = $quotes->update($data->id, $data->quote, $data->author_id, $data->category_id);
        } else {
          http_response_code(400);
          $result = array('error' => 'Missing quote ID');
        }
        break;
      
      // DELETE LOGIC
      case 'DELETE':
        if (isset($path_segments[1])) {
          $data = json_decode(file_get_contents('php://input'));
          $result = $quotes->delete($data->id);
        } else {
          http_response_code(400);
          $result = array('error' => 'Missing quote ID');
        }
        break;
      default:
        http_response_code(405);
        $result = array('error' => 'Invalid method');
    }

    $encoded_result = json_encode($result);
    echo($encoded_result);
    return $encoded_result;
}

?>