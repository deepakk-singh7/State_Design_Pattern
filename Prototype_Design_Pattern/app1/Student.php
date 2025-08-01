<?php 

require_once('Prototype.php');
class Student implements Prototype {
    // private string $name; 
    // private int $rollNumber; 
    // private int $age;

    public function __construct(private string $name, private int $rollNumber, private int $age){}

    public function makeClone():Prototype{
        return new Student($this->name, $this->rollNumber, $this->age);
    }
}