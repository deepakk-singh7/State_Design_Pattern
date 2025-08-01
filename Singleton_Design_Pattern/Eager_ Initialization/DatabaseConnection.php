<?php 

class DatabaseConnection {
    private $connection; // don't know the datatype of this.. 

    // private constructor

    private function __construct(){
        $this->connection = "connected to DB \n";
    }
    // eager initilization, that object will be created at the class initialization time only.. 
   private static $instance = new DatabaseConnection(); 
    // public method to get the instance object..

    public static function getInstance():DatabaseConnection{

        return self::$instance;
    }

    // getter()
    public function getConnection(){
        return $this->connection;
    }
}