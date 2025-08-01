<?php 

require_once 'Monster.php';
class Ghost extends Monster {

    public function __construct(int $health, float $speed){
        parent::__construct($health,$speed,'Ghost');
    }
    protected function attack():void{
        echo "Ghost Attack \n ". PHP_EOL;
    }
    public function getInfo(): void{
        parent::getInfo();
    }
}