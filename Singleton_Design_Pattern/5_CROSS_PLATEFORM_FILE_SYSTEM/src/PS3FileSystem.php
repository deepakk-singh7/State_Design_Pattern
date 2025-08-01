<?php 

require_once 'FileSytem.php';
class PS3FileSystem extends FileSystem{

    public function readFile(string $path):string{
        echo 'Reading ... PS3FileSytem..'; 
        return 'PS3FileSystem content......';
    }
    public function writeFile(string $path, string $content):void{
        echo 'Writing... PS3...' . PHP_EOL;
    }

    public function deleteFile(string $path): bool{
        echo 'Deleting... PS3...' . PHP_EOL;
        return true;
    }

    public function fileExist(string $path): bool{
        echo 'Yes.. FileExits.. PS3..'. PHP_EOL;
        return true;
    }
}