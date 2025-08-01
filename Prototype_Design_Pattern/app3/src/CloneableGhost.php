<?php 

require_once 'CloneableMonster.php';
class CloneableGhost extends CloneableMonster {
    public function __construct($health, $speed) {
        parent::__construct($health, $speed, 'Ghost');
    }
    public function cloneMonster():CloneableGhost {
        return new CloneableGhost($this->health, $this->speed);
    }
    
    public function attack() {
        return "Ghost Attack";
    }
}