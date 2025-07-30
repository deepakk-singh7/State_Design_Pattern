<?php 

// class HeroineV1;
require_once 'input.php';
require_once 'HeroineV1.php';


abstract class HeroineState{
    // method to enter into a specific state
    public function enter(HeroineV1 $heroine):void{}
    // method to handle Input by states
    abstract public function handleStateInput(HeroineV1 $heroine, Input $input):?HeroineState;
    // method to update states
    public function updateState(HeroineV1 $heroine):void{}

}