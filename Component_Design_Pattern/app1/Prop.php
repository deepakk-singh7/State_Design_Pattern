<?php 

require_once 'Decoration.php';
class Prop extends Decoration{ 
    // Inherit render 
    // have to duplicate the logic from the Zone 

    public function checkCollision():void{
        echo "Checking collision for {$this->name} \n ";
    }

}