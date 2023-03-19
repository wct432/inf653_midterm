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

    // create quote
    function create(){
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    quote=:quote, author_id=:author_id, category_id=:category_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->quote=htmlspecialchars(strip_tags($this->quote));
        $this->author_id=htmlspecialchars(strip_tags($this->author_id));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));

        // bind values
        $stmt->bindParam(":quote", $this->quote);
        $stmt->bindParam(":author_id", $this->author_id);
        $stmt->bindParam(":category_id", $this->category_id);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    // read quotes
    function read(){
        // select all query
        // $query = "SELECT q.id, q.quote, a.author, c.category
        //         FROM " . $this->table_name . " q
        //             LEFT JOIN authors a ON q.author_id = a.id
        //             LEFT JOIN categories c ON q.category_id = c.id
        //         ORDER BY q.id DESC";
        $query = "SELECT * from quotes;";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
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
            return array('message' => 'No Quotes Found');
        }
    
        // return fetched row data
        return $row;
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
                $rows[] = $row;
            }
    
            // Check if row is empty
            if(empty($rows)) {
                // Return JSON object with message "No quotes found"
                return array('message' => 'No Quotes Found');
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
                $rows[] = $row;
            }
    
            // Check if row is empty
            if(empty($rows)) {
                // Return JSON object with message "No quotes found"
                return array('message' => 'No Quotes Found');
            }
        
            // return fetched row data
            return $rows;
        }

    // update the quote
    function update(){
        // update query
        $query = "UPDATE " . $this->table_name . "
                SET
                    quote=:quote, author_id=:author_id, category_id=:category_id
                WHERE
                    id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->quote=htmlspecialchars(strip_tags($this->quote));
        $this->author_id=htmlspecialchars(strip_tags($this->author_id));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));

        // bind values
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":quote", $this->quote);
        $stmt->bindParam(":author_id", $this->author_id);
        $stmt->bindParam(":category_id", $this->category_id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }


    // delete the quote
    function delete(){
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind id of record to delete
        $stmt->bindParam(1, $this->id);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

}
