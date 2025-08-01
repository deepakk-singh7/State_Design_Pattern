<?php 

require_once 'FileSytem.php';
class Xbox360FileSystem extends FileSystem{
    public function readFile(string $path):string{
        echo 'Reading ... Xbox360FileSytem..'; 
        return 'Xbox360FileSystem content......';
    }
    public function writeFile(string $path, string $content):void{
        echo 'Writing... Xbox360...' . PHP_EOL;
    }

    public function deleteFile(string $path): bool{
        echo 'Deleting... Xbox360...' . PHP_EOL;
        return true;
    }

    public function fileExist(string $path): bool{
        echo 'Yes.. FileExits.. Xbox360..'. PHP_EOL;
        return true;
    }
}