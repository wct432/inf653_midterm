<?php


class Quotes {

    // database connection and table name
    private $conn;
    private $table_name = "quotes";

    // object properties
    public $id;
    public $quote;
    public $author_id;
    public $category_id;
    public $author_name;
    public $category_name;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }


    function create($quote, $author_id, $category_id){
        $query = "INSERT INTO " . $this->table_name . " (quote, author_id, category_id) 
                  VALUES (:quote, :author_id, :category_id)
                  RETURNING id";

    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $quote=htmlspecialchars(strip_tags($quote));
        $author_id=htmlspecialchars(strip_tags($author_id));
        $category_id=htmlspecialchars(strip_tags($category_id));
    
        // bind values
        $stmt->bindParam(":quote", $quote);
        $stmt->bindParam(":author_id", $author_id);
        $stmt->bindParam(":category_id", $category_id);
    

        try {
            // execute query
            if($stmt->execute()){
               // get the ID of the inserted record
                $id = $this->conn->lastInsertId();
                // construct array of inserted data
                $data = array(
                    "id" => $id,
                    "quote" => $quote,
                    "author_id" => $author_id,
                    "category_id" => $category_id
                );

                // return data
                return $data;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23503') {
                // Check if the error code is 23503, which is the error code for foreign key violation
                $errorInfo = $e->errorInfo;
                if (strpos($errorInfo[2], 'quotes_author_id_fkey') !== false) {
                    return json_decode(json_encode(array('message' => 'author_id Not Found')));
                } elseif (strpos($errorInfo[2], 'quotes_category_id_fkey') !== false) {
                    return json_decode(json_encode(array('message' => 'category_id Not Found')));
                } else {
                    echo "Foreign key violation error: " . $e->getMessage();
                }
            } else {
                // Handle other types of PDOExceptions here
                echo "Error: " . $e->getMessage();
            }

    }

}
    



    function update($id, $quote, $author_id, $category_id){
    
        // update query
        $query = "UPDATE " . $this->table_name . "
                SET
                    quote=:quote, author_id=:author_id, category_id=:category_id
                WHERE
                    id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $id=htmlspecialchars(strip_tags($id));
        $quote=htmlspecialchars(strip_tags($quote));
        $author_id=htmlspecialchars(strip_tags($author_id));
        $category_id=htmlspecialchars(strip_tags($category_id));
    
        // bind values
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":quote", $quote);
        $stmt->bindParam(":author_id", $author_id);
        $stmt->bindParam(":category_id", $category_id);
    
        try {
            $stmt->execute();
            if($stmt->rowCount() == 0){
                return json_decode(json_encode(array('message' => 'No Quotes Found')));
            } else {
                $data = array(
                    "id" => (int)$id,
                    "quote" => $quote,
                    "author_id" => (int)$author_id,
                    "category_id" => (int)$category_id
                );
                // return JSON of inserted data
                return $data;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23503') {
                // Check if the error code is 23503, which is the error code for foreign key violation
                $errorInfo = $e->errorInfo;
                if (strpos($errorInfo[2], 'quotes_author_id_fkey') !== false) {
                    return json_decode(json_encode(array('message' => 'author_id Not Found')));
                } elseif (strpos($errorInfo[2], 'quotes_category_id_fkey') !== false) {
                    return json_decode(json_encode(array('message' => 'category_id Not Found')));
                } else {
                    return json_decode(json_encode(array("Foreign key violation error: " . $e->getMessage())));
                }
            } else {
                // Handle other types of PDOExceptions here
                json_decode(json_encode(array("Error: " . $e->getMessage())));
            }
        return false;
        }
    
    }




function delete($id) {
    // check if all parameters are present
    if(empty($id)){
        return json_decode(json_encode(array('message' => 'Missing Required Parameters')));
        }
    // delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // bind parameter
    $stmt->bindParam(":id", $id);

    try {
        // execute query
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return array('id' => $id);
        } else {
            return json_decode(json_encode(array('message' => 'No Quotes Found')));
        }
    } catch(PDOException $exception) {
        echo "Error: " . $exception->getMessage();
        return false;
    }
}



    // read quotes
    function read(){
        $query = "SELECT * from quotes;";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        // fetch all rows returned by the query
        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = array(
            'id' => $row['id'],
            'quote' => $row['quote'],
            'author' => $row['author_id'],
            'category' => $row['category_id']
            );
        }

        // Check if row is empty
        if(empty($rows)) {
            // Return JSON object with message "No quotes found"
            return arrayjson_decode(json_encode(array('message' => 'No Quotes Found')));
        }
        
        // return fetched row data
        return $rows;
    }

    // read single quote by ID
    function read_single($id){

        $query = "SELECT * from quotes where id = ?";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // bind id of quote to be read
        $stmt->bindParam(1, $id);
    
        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
  
        // Check if row is empty
        if(empty($row)) {
            // Return JSON object with message "No quotes found"
            return json_decode(json_encode(array('message' => 'No Quotes Found')));
        } else {
          $data = array(
              'id' => $row['id'],
              'quote' => $row['quote'],
              'author' => $row['author_id'],
              'category' => $row['category_id']
              );
          
          return $data;
        }

    }


    function get_quotes_by_author($author_id){
    
            $query = "SELECT * FROM quotes WHERE author_id = ?";
        
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // bind id of quote to be read
            $stmt->bindParam(1, $author_id);
        
            // execute query
            $stmt->execute();
        
            // fetch all rows returned by the query
            $rows = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = array(
                'id' => $row['id'],
                'quote' => $row['quote'],
                'author' => $row['author_id'],
                'category' => $row['category_id']
                );
            }
    
            // Check if row is empty
            if(empty($rows)) {
                // Return JSON object with message "No quotes found"
                return json_decode(json_encode(array('message' => 'No Quotes Found')));
            }
            
            // return fetched row data
            return $rows;
        }


    function get_quotes_by_category($category_id){
    
            $query = "SELECT * FROM quotes WHERE category_id = ?";
        
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // bind id of quote to be read
            $stmt->bindParam(1, $category_id);
        
            // execute query
            $stmt->execute();
        
            // fetch all rows returned by the query
            $rows = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = array(
                'id' => $row['id'],
                'quote' => $row['quote'],
                'author' => $row['author_id'],
                'category' => $row['category_id']
                );
            }
    
            // Check if row is empty
            if(empty($rows)) {
                // Return JSON object with message "No quotes found"
                return json_decode(json_encode(array('message' => 'No Quotes Found')));
            }
        
            return $rows;
        }

    function get_by_auth_and_cat($author_id, $category_id){
    
            $query = "SELECT * FROM quotes 
                    WHERE author_id = ? 
                    AND category_id = ?";
        
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // bind id of author
            $stmt->bindParam(1, $author_id);
        
            // bind id of category
            $stmt->bindParam(2, $category_id);
            
            // execute query
            $stmt->execute();
        
                   // fetch all rows returned by the query
        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = array(
            'id' => $row['id'],
            'quote' => $row['quote'],
            'author' => $row['author_id'],
            'category' => $row['category_id']
            );
        }
    
            // Check if row is empty
            if(empty($rows)) {
                // Return JSON object with message "No quotes found"
                return json_decode(json_encode(array('message' => 'No Quotes Found')));
            }
        
            // return fetched row data
            return $rows;
        }

    


    

}
