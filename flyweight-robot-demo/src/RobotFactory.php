<?php 

// creates and manages flyweight objects, returns an existing instance or creates a new one. 
class RobotFactory {
    private array $robots = [];
    // the function to create new instance or return the existing one. 
    public function getRobot(string $type): RobotInterface {

        if(!isset($this->robots[$type])){
            $this->robots[$type] = new Robot($type);
        }else {
            // reuse existing type of robot object.  
        }
        return $this->robots[$type];
    }

    public function totalObjectCreated():int{
        return count($this->robots);
    }

}