<?php 

require_once 'HeroineState.php';
require_once 'input.php';
require_once 'StandingState.php';
require_once 'HeroineV1.php';


class DuckingState extends HeroineState{
    private int $chargeTime = 0; 
    public function enter(HeroineV1 $heroine):void{
        echo "Heroine in now Ducking state.." . PHP_EOL;
    }
    public function handleStateInput(HeroineV1 $heroine, Input $input): ?HeroineState{
        if($input === Input::RELEASE_DOWN){
            return new StandingState();
        }
        return null;
    }
    public function updateState(HeroineV1 $heroine): void{
        $this->chargeTime++;
        echo ".. charging.. : " . $this->chargeTime . PHP_EOL;
        if($this->chargeTime>2){
            echo '.. Ready for attack.. ' . PHP_EOL;
        } 
    }
}