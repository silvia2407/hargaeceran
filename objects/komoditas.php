<?php
class Komoditas{
 
    // database connection and table name
    private $conn;
    private $table_name = "komoditas";
 
    // object properties
    public $idkomoditas;
    public $komoditas;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    // read products
    function read(){

        // select all query
        $query = "SELECT
                    *
                FROM
                    " . $this->table_name . " 
                ORDER BY
                    komoditas ASC";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }
}
?>