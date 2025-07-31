<?php

//this class implements the Flyweight interface and stores the intrinsic state ie shape and type. 
class Robot implements RobotInterface {
    private string $type; 
    //a large, memory-heavy object.  
    private string $shape;

    public function __construct(string $type){
        $this->type = $type; 
        $this->shape = $this->createLargeShapeData();
    }

    private function createLargeShapeData(): string {
        return "very large size bit array.";
    }
    public function display(int $x, int $y):void{
        echo "Displaying '{$this->type}' robot with coordinates ({$x}, {$y}).\n" . '<br/>';
    }
}