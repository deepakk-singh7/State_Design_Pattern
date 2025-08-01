<?php

require_once 'src/PlatformConfig.php';
require_once 'src/FileSytem.php';

// setting platform via cmd 

$platform = $argv[2]?? "PS3";
var_dump($argv);
// var_dump($platform);
PlatformConf::setPlatform($platform);

$fs = FileSystem::getInstance();
var_dump($fs);
$fs->writeFile('/path','content...');
echo $fs->readFile('/path...');
echo $fs->fileExist('/path..') . PHP_EOL;



