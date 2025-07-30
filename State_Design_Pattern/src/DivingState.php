<?php 

require_once 'HeroineState.php';
require_once 'input.php';
require_once 'HeroineV1.php';

class DivingState extends HeroineState{
    public function enter(HeroineV1 $heroine): void{
        echo "Heroine is now Diving.. " . PHP_EOL;
    }
    public function handleStateInput(HeroineV1 $heroine, Input $input): HeroineState|null{
        return null;
    }

}