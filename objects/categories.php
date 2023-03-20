<?php


class Categories {

    // database connection and table name
    private $conn;
    private $table_name = "categories";

    // object properties
    public $id;
    public $category;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function create($category){
        // // check if all parameters are present
        // if(empty($category)){
        //     return json_decode(json_encode(array('message' => 'Missing Required Parameters')));
        // }
        // insert query
        $query = "INSERT INTO " . $this->table_name . " (category)
                  VALUES (:category)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $category=htmlspecialchars(strip_tags($category));
    
        // bind values
        $stmt->bindParam(":category", $category);

        try {
            // execute query
            if($stmt->execute()){
               // get the ID of the inserted record
                $id = $this->conn->lastInsertId();
                // construct array of inserted data
                $data = array(
                    "id" => $id,
                    "category" => $category
                );
    
                // return JSON of inserted data
                return $data;
            }
        } catch (PDOException $e) {
                // Handle other types of PDOExceptions here
                echo json_decode(json_encode(array("Error: " . $e->getMessage())));
            }
      
        return false;
    }






    function update($id, $category){
        // check if all parameters are present
        if(empty($id) || empty($category)){
            return json_decode(json_encode(array('message' => 'Missing Required Parameters')));
        }
    
        // update query
        $query = "UPDATE " . $this->table_name . "
                SET
                    category=:category
                WHERE
                    id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $id=htmlspecialchars(strip_tags($id));
        $category=htmlspecialchars(strip_tags($category));
    
        // bind values
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":category", $category);

        try {
            $stmt->execute();
            if($stmt->rowCount() == 0){
                return json_decode(json_encode(array('message' => 'category_id not found')));
            } else {
                $data = array(
                    "id" => $id,
                    "category" => $category
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
                return json_decode(json_encode(array('message' => 'No Category Found')));
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
            return json_decode(json_encode(array('message' => 'category_id Not Found')));
        }
        
        // return fetched row data
        return $rows;
    }

    // read single category by ID
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
            return json_decode(json_encode(array('message' => 'category_id Not Found')));
        }
    
        // return fetched row data
        return $row;
    }

}