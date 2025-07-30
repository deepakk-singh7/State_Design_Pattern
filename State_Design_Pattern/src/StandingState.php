<?php 

require_once 'HeroineState.php';
require_once 'input.php';
require_once 'HeroineV1.php';
require_once 'DuckingState.php';
require_once 'JumpingState.php';


class StandingState extends HeroineState{
    public function enter(HeroineV1 $heroine):void{
        echo "Heroine is now in Standing state." . PHP_EOL;
    }
    public function handleStateInput(HeroineV1 $heroine, Input $input): HeroineState|null{
        if($input === Input::PRESS_B){
            return new JumpingState();
        }
        if($input === Input::PRESS_DOWN){
            return new DuckingState();
        }
        return null;
    }
}
