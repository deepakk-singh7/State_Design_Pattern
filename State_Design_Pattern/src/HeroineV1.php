<?php 

require_once 'HeroineState.php';
require_once 'StandingState.php';
require_once 'input.php';
class HeroineV1{
    // Referance to the abstract state class.
    private HeroineState $state;
    
    public function __construct(){
        // Heroine always starts in Standing State
        $this->state = new StandingState();
        $this->state->enter($this);
    }
    // Handling input coming from the client, it will trigger handleInput() in concrete class and might cause the state change.
    public function handleInput(Input $input):void{
        echo "\n Input : " . $input->value . " ";
        $newState = $this->state->handleStateInput($this,$input);

        // if state change, set it the new state
        if($newState!==null){
            $this->state = $newState;
            $this->state->enter($this);
        }else{
            echo "No state change. ". PHP_EOL;
        }
    }
    // Handling updates, it might trigger some actions based on the state data and conditions. 
    public function update(): void {
        $this->state->updateState($this);
    }
}