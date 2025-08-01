<?php 

require_once 'Actor.php';
class Comedian extends Actor{
    private ?Actor $facing = null;
    private $name; 

    // constructor to make a comedian
    public function __construct($name){
        parent::__construct();
        $this->name = $name;
    }
    // Make the actor facing to the this actor
    public function face(Actor $actor): void{
        $this->facing = $actor;
    }
    public function getName():string{
        return $this->name;
    }
    // Function to update this comedian, if slapped then he will call slap on its facing actor.
    public function update():void{
        if($this->wasSlapped()){
            echo "  {$this->name} was slapped! Slapping back...\n";
            $this->facing->slap();
        }else{
            echo "  {$this->name} was not slapped, doing nothing\n";
        }
    }

}