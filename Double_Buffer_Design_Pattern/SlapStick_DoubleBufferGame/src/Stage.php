<?php 

require_once 'Actor.php';
class Stage{
    private $actors = [];
    private int $numActors = 3;

    public function addActor(Actor $actor, int $index): void{
        $this->actors[$index] = $actor;
    }

    public function update():void{
        # updating all the actors.. 
        // print_r($this->actors);
        echo "Updating all actors \n";
        for($i = 0 ; $i<$this->numActors;$i++){
            $this->actors[$i]->update();
        }
        # swap all buffers 
        for($i = 0; $i < $this->numActors ; $i++){
            $this->actors[$i]->swap();
        }
        echo "Stage update completed \n";
    }
}