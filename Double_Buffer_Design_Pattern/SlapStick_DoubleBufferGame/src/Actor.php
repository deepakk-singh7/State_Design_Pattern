<?php 

abstract class Actor{
    protected bool $currentSlapped = false; 
    protected bool $nextSlapped =false; 

    public function __construct(){
        $this->currentSlapped = false;
        $this->nextSlapped = false;
    }

    abstract public function update();

    public function slap(): void{
        $this->nextSlapped = true;
    }
    public function wasSlapped():bool{
        return $this->currentSlapped;
    }

    // no more reset(), it shuold be swap it nextSlapped buffer will be now currentSlapped and next will be empty for reading again..
    public function swap():void{
        $this->currentSlapped = $this->nextSlapped;
        $this->nextSlapped = false;
    }


}