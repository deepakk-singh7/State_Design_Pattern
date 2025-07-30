<?php 

require_once 'HeroineState.php';
require_once 'input.php';
require_once 'HeroineV1.php';
require_once 'DivingState.php';


class JumpingState extends HeroineState{
    public function enter(HeroineV1 $heroine):void{
        echo "Heroine is now in Jumping state." . PHP_EOL;
    }
    public function handleStateInput(HeroineV1 $heroine, Input $input): HeroineState|null{
        if($input===Input::PRESS_DOWN){
            return new DivingState();
        }
        return null;
    }
}