<?php 

require_once 'FileSytem.php';
class WiiFileSystem extends FileSystem{
    public function readFile(string $path):string{
        echo 'Reading ... WiiFileSytem..'; 
        return 'WiiFileSystem content......';
    }
    public function writeFile(string $path, string $content):void{
        echo 'Writing... Wii...' . PHP_EOL;
    }

    public function deleteFile(string $path): bool{
        echo 'Deleting... Wii...' . PHP_EOL;
        return true;
    }

    public function fileExist(string $path): bool{
        echo 'Yes.. FileExits.. Wii..'. PHP_EOL;
        return true;
    }
}