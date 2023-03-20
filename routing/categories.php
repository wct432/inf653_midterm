<?php

require_once("../objects/categories.php");

function get_category_result($path_segments, $method, $db){

// include_once('objects/quotes.php');
    $categories = new Categories($db);
    switch ($method) {
      case 'GET':
        // GET BY ID
        if (isset($_GET['id'])) {
          $result = $categories->read_single($_GET['id']);
        // ELSE NO PARAMS WERE PASSED, DEFAULT GET ALL AUTHORS
        } else {
          $result = $categories->read();
        }
        break;

      // POST LOGIC
      case 'POST':
        $data = json_decode(file_get_contents('php://input'));
        // check if all parameters are present
        if(empty($data->category)){
            $result = json_decode(json_encode(array('message' => 'Missing Required Parameters')));
        } else {
            $result = $categories->create($data->category);      
        }

        break;

      // PUT LOGIC
      case 'PUT':
        if (isset($path_segments[1])) {
          $data = json_decode(file_get_contents('php://input'));
          if(empty($data->id) || empty($data->category)){
            $result = json_decode(json_encode(array('message' => 'Missing Required Parameters')));
          } else {
           $result = $categories->update($data->id, $data->category); 
          }
        } else {
          http_response_code(400);
          $result = array('error' => 'Missing quote ID');
        }
        break;
      
      // DELETE LOGIC
      case 'DELETE':
        if (isset($path_segments[1])) {
          $data = json_decode(file_get_contents('php://input'));
          $result = $categories->delete($data->id);
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