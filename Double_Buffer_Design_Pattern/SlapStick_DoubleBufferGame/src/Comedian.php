<?php 

class Comedian extends Actor{
    private $name; 
    private ?Actor $facing = null;

    public function __construct($name){
        parent::__construct();
        $this->name = $name;
    }
    public function face(Actor $actor): void{
        $this->facing = $actor;
    }
    public function getName():string{
        return $this->name;
    }
    public function update(): void{
        if($this->wasSlapped()){
            echo "{$this->name} . was slapped, Slapping back \n";
            $this->facing->slap();
        }
        else{
           echo "{$this->name} was not slapped, doing nothing... \n";
        }
    }
}