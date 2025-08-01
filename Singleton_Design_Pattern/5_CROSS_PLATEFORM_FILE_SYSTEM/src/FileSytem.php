<?php 

require_once 'PlatformConfig.php';
require_once 'PS3FileSystem.php';
require_once 'WiiFileSystem.php';
require_once 'Xbox360FileSystem.php';
abstract class FileSystem {
    private static ?FileSystem $instance = null; 

    protected function __construct(){}

    public static function getInstance():FileSystem{

        if(self::$instance===null){
        $platform = PlatformConf::getPlatform();

        switch($platform){
            case 'PS3':
                self::$instance = new PS3FileSystem();
                break;
            case 'XBOX360':
                self::$instance = new Xbox360FileSystem();
                break;
            case 'WII':
                self::$instance = new WiiFileSystem();
                break;    

            }
        }
        return self::$instance;
    }    
    abstract public function readFile(string $path):string;
    abstract public function writeFile(string $path, string $content):void;

    abstract public function deleteFile(string $path):bool;
    abstract public function fileExist(string $path):bool;



}