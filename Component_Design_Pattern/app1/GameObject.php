<?php 

abstract class GameObject {
    protected string $name; 
    function __construct($name){
        $this->name = $name;
    }
}