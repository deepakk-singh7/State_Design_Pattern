<?php 

require_once 'GameObject.php';
class Zone extends GameObject{
    public function checkCollision(){
        echo "Checking collision for {$this->name}\n";
    }
}