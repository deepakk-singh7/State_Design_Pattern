<?php 

require_once('Student.php');

$original = new Student("Deepak", 22, 22);

$cloneObj = $original->makeClone();

var_dump($cloneObj);