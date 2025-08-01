<?php 

require_once 'DatabaseConnection.php';

$db1 = DatabaseConnection::getInstance();
$db2 = DatabaseConnection::getInstance(); 

echo $db1->getConnection();
echo $db2->getConnection();