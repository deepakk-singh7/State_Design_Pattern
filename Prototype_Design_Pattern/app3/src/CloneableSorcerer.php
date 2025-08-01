<?php 

require_once 'CloneableMonster.php';
class CloneableSorcerer extends CloneableMonster {
    private $mana;
    public function __construct($health, $speed, $mana) {
        parent::__construct($health, $speed, 'Sorcerer');
        $this->mana = $mana;
    }
    
    public function cloneMonster():CloneableSorcerer {
        return new CloneableSorcerer($this->health, $this->speed, $this->mana);
    }
    
    public function attack() {
        return "Sorcerer Attack";
    }
    public function getInfo() {
        return "Sorcerer (Health: {$this->health}, Speed: {$this->speed}, Mana: {$this->mana})";
    }
}