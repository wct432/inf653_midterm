<?php

class Database {
    private $host = "dpg-cgb2ts1mbg55nqkr88tg-a.oregon-postgres.render.com"; // for example: postgresql.example.com
    private $port = "5432"; // for example: 5432
    private $db_name = "quotesdb_o5h8";
    private $username = "quotesdb_o5h8_user";
    private $password = "S6PyFwmW4aZtM2ascAoh5ISxOzocEdCO";
    public $conn;

    public function getConnection() {
        // $this->conn = null;

        // try {
        //     $this->conn = new PDO("pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
        //     $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // } catch(PDOException $exception) {
        //     echo "Connection error: " . $exception->getMessage();
        // }


        // return $this->conn;

        if($this-> conn){
            return $this->conn;
        }else{

            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};";

            try {
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->conn;
            } catch(PDOException $e){
                echo 'Connection Error: ' . $e->getMessage();
            }
        }
    }
}
