<?php 

abstract class Actor {
    protected bool $slapped = false; 

    public function __construct(){
        $this->slapped = false;
    }

    // abstract method to update an actor
    abstract public function update();
    
    public function reset(): void{
        $this->slapped = false;
    }
    public function wasSlapped():bool{
        return $this->slapped;
    }
    public function slap():void{
        $this->slapped = true;
    }

}