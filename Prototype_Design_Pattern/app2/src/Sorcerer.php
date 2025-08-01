<?php 

require_once 'Monster.php';

class Sorcerer extends Monster{
    private float $mana; 

    public function __construct(float $health, float $speed,float $mana){
        parent::__construct($health,$speed,'Sorcerer');
        $this->mana=$mana;
    }

    protected function attack():void{
        echo "Sorcerer Attack \n ". PHP_EOL;
    }
    public function getInfo(): void{
        parent::getInfo();
    }

}