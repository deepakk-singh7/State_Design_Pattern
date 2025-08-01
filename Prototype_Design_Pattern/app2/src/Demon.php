<?php 

require_once 'Monster.php';

class Demon extends Monster{
    private float $strength;

    public function __construct(float $health, float $speed, $strength){
        parent::__construct($health,$speed,'Demon');
        $this->strength=$strength;
    }
    
    protected function attack():void{
        echo "Ghost Attack \n ". PHP_EOL;
    }

    public function getInfo(): void{
        parent::getInfo();
    }
    
}