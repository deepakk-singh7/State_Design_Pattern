<?php 

require_once 'Actor.php';
class Stage{
    private $actors = [];
    private int $numActors = 3; 

    // Add a actor to the actors array
    public function addActor(Actor $actor, int $index): void{
        $this->actors[$index] = $actor;
    }

    // stage updating which lead to updating the state of each actors one by one.
    public function updateStage():void{
        for($index = 0 ; $index<$this->numActors ;$index++){
            $this->actors[$index]->update();
            $this->actors[$index]->reset();
        }
        echo "Stage Updated \n ";
    }
}