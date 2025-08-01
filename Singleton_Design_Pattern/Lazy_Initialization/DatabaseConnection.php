<?php 

class DatabaseConnection {
    private $connection; 
    private static $instance = null; 

    private function __construct(){
        $this->connection = 'db connected!!! \n';
    }
    
    // initialize only when this function called... 
    public static function getInstance():DatabaseConnection{
        if(self::$instance===null){
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }

    public function getConnection(){
        return $this->connection;
    }
}