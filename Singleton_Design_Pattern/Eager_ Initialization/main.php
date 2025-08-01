<?php 

require_once 'DatabaseConnection.php';

$db1 = DatabaseConnection::getInstance();
var_dump($db1);
echo $db1->getConnection();

$db2 = DatabaseConnection::getInstance();
var_dump($db2);
echo $db2->getConnection();
