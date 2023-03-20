<?php


class Authors {

    // database connection and table name
    private $conn;
    private $table_name = "authors";

    // object properties
    public $id;
    public $author;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function create($author){
        // check if all parameters are present
        if(empty($author)){
            return array('message' => 'Missing Required Parameters');
        }
        // insert query
        $query = "INSERT INTO " . $this->table_name . " (author)
                  VALUES (:author)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $author=htmlspecialchars(strip_tags($author));
    
        // bind values
        $stmt->bindParam(":author", $author);

        try {
            // execute query
            if($stmt->execute()){
                // get the ID of the inserted record
                $id = $this->conn->lastInsertId();
                // construct array of inserted data
                $data = array(
                    "id" => $id,
                    "author" => $author
                );
    
                // return JSON of inserted data
                return $data;
            }
        } catch (PDOException $e) {
                // Handle other types of PDOExceptions here
                echo "Error: " . $e->getMessage();
            }
    
        echo("FAILURE");
        return false;
    }



    function update($id, $author){
        // check if all parameters are present
        if(empty($id) || empty($author)){
            return array('message' => 'Missing Required Parameters');
        }
    
        // update query
        $query = "UPDATE " . $this->table_name . "
                SET
                    author=:author
                WHERE
                    id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $id=htmlspecialchars(strip_tags($id));
        $author=htmlspecialchars(strip_tags($author));
    
        // bind values
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":author", $author);

        try {
            $stmt->execute();
            if($stmt->rowCount() == 0){
                return array('message' => 'author_id not found');
            } else {
                $data = array(
                    "id" => $id,
                    "author" => $author
                );
                // return JSON of inserted data
                return $data;
            }
        } catch (PDOException $e) {
                // Handle other types of PDOExceptions here
                echo "Error: " . $e->getMessage();
            }
        return false;
        }

    function delete($id) {
        // check if all parameters are present
        if(empty($id)){
            return array('message' => 'Missing Required Parameters');
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
            // echo($stmt->rowCount());
            if($stmt->rowCount() > 0) {
                return array('id' => $id);
            } else {
                return array('message' => 'No Authors Found');
            }
        } catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
            return false;
        }
    }


    // read authors
    function read(){
        $query = "SELECT * from " . $this->table_name . ";";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
            // fetch all rows returned by the query
        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        // Check if row is empty
        if(empty($rows)) {
            // Return JSON object with message "No quotes found"
            return array('message' => 'author_id Not Found');
        }
        
        // return fetched row data
        return $rows;
    }

    // read single author by ID
    function read_single($id){

        $query = "SELECT * from " . $this->table_name . " where id = ?";
    
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
            return array('message' => 'author_id Not Found');
        }
    
        // return fetched row data
        return $row;
    }

}