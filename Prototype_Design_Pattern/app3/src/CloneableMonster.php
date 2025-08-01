<?php 

abstract class CloneableMonster {
    
    public function __construct(protected float $health, protected float $speed, protected string $type) { // constructor property promotion.. 

    }
    abstract public function cloneMonster():CloneableMonster; // adding clone functionality to each of the concrete classes
    
    abstract public function attack();
    
    public function getInfo() {
        return "{$this->type} (Health: {$this->health}, Speed: {$this->speed})";
    }
}