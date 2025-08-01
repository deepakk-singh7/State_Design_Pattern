<?php 

require_once 'CloneableMonster.php';
class CloneableDemon extends CloneableMonster {
    private $strength;
    
    public function __construct($health, $speed, $strength) {
        parent::__construct($health, $speed, 'Demon');
        $this->strength = $strength;
    }
    
    public function cloneMonster():CloneableDemon {
        return new CloneableDemon($this->health, $this->speed, $this->strength);
    }
    
    public function attack() {
        return "Demon Attack";
    }
    
    public function getInfo() {
        return "Demon (Health: {$this->health}, Speed: {$this->speed}, Strength: {$this->strength})";
    }
}