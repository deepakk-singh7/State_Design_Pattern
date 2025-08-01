<?php

abstract class Monster {
    private float $health; 
    private float $speed; 
    private string $type; 

    public function __construct(float $health, float $speed, $type){
        $this->health=$health;
        $this->speed = $speed;
        $this->type = $type;
    }
    abstract protected function attack():void;

    public function getInfo():void{
        echo 'Type : ' . $this->health . '. Speed : '. $this->speed . '. Health : '. $this->type . PHP_EOL;
    }
}