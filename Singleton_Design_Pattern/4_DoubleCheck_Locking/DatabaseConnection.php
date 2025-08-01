<?php 

class DatabaseConnection {
    private $connection;
    private static $instance = null; 
    private static $mutex = null; 

    private function __construct(){
        $this->connection = 'Db connected!\n';
    }

    public static function getInstance():DatabaseConnection{
        // check if instance is not created.. 

        if(self::$instance===null){

            // initilize the mutex...
            if(self::$mutex===null){
                // self::$mutex = new SyncMutex();
            }
        }
    }

}