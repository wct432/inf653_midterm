<?php

require_once("../objects/authors.php");

function get_author_result($path_segments, $method, $db){



// include_once('objects/quotes.php');
    $authors = new Authors($db);
    switch ($method) {
      case 'GET':
        // GET BY ID
        if (isset($_GET['id'])) {
          $result = $authors->read_single($_GET['id']);
        // ELSE NO PARAMS WERE PASSED, DEFAULT GET ALL AUTHORS
        } else {
          $result = $authors->read();
        }
        break;

      // POST LOGIC
      case 'POST':
        $data = json_decode(file_get_contents('php://input'));
        $result = $authors->create($data->author);
        break;

      // PUT LOGIC
      case 'PUT':
        if (isset($path_segments[1])) {
          $data = json_decode(file_get_contents('php://input'));
          $result = $authors->update($data->id, $data->author);
        } else {
          http_response_code(400);
          $result = array('error' => 'Missing quote ID');
        }
        break;
      
      // DELETE LOGIC
      case 'DELETE':
        if (isset($path_segments[1])) {
          $data = json_decode(file_get_contents('php://input'));
          $result = $authors->delete($data->id);
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