<?php

// Connect to the database
$dbconn = pg_connect("host=localhost dbname=quotesdb user=postgres password=your_password");

// Check if the connection is successful
if (!$dbconn) {
  die("Connection failed: " . pg_last_error());
}

// Set the response content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Get the request data from the request body
  $data = json_decode(file_get_contents("php://input"), true);

  // Validate the request data
  if (empty($data['quote']) || empty($data['author_id']) || empty($data['category_id'])) {
    // Return a 400 Bad Request error message for missing parameters
    http_response_code(400);
    echo json_encode(['message' => 'Missing parameters.']);
    exit;
  }

  // Insert the quote into the database
  $result = pg_query_params($dbconn, "INSERT INTO quotes (quote, author_id, category_id) VALUES ($1, $2, $3) RETURNING id", [$data['quote'], $data['author_id'], $data['category_id']]);

  // Check if the quote was inserted successfully
  if ($result) {
    // Get the inserted quote's ID from the result
    $id = pg_fetch_result($result, 0);

    // Fetch the inserted quote from the database and return it in the response
    $result = pg_query_params($dbconn, "SELECT quotes.id, quotes.quote, authors.author, categories.category FROM quotes JOIN authors ON quotes.author_id = authors.id JOIN categories ON quotes.category_id = categories.id WHERE quotes.id = $1", [$id]);
    $quote = pg_fetch_assoc($result);
    echo json_encode($quote);
  } else {
    // Return a 500 Internal Server Error message for database errors
    http_response_code(500);
    echo json_encode(['message' => 'Failed to create quote.']);
  }

} else {
  // Return a 405 Method Not Allowed error message for unsupported HTTP methods
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed.']);
}

// Close the database connection
pg_close($dbconn);
