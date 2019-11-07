<?php
class Database{
 
    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "hargaeceran";
    private $username = "hargaeceran";
    private $password = "hargaeceran";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            //return 'masuk';
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
            //return 'gagal';
        }
 
        return $this->conn;
    }
    //return $this->getConnection();
}
?>