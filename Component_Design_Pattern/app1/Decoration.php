<?php 

require_once 'GameObject.php';
class Decoration extends GameObject{
    public function render(){
        echo "rendering the {$this->name}\n";
    }
}