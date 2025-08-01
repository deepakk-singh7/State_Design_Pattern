<?php

class DatabaseConnection{
    private $connection;
    private static $instance = null; 

    // flag for locking mech
    private static bool $islock = false;

    private function __construct(){
        $this->connection = 'Db connected\n'; 
    }

    // have to make this getInstance() method thread safety..
    //
    public static function getInstance():DatabaseConnection{

        // waif if another thread is creating the instance.. 
        while(self::$islock){
            usleep(1); // usleep(microseconds), sleep(seconds)
        }
        
        if(self::$instance===null){
            self::$islock = true;
            self::$instance = new DatabaseConnection();
            self::$islock = false;
        }
        return self::$instance;
    }

    public function getConnection():String{
        return $this->connection;
    }
}