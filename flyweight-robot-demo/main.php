<?php

require_once('src/RobotInterface.php');
require_once('src/Robot.php');
require_once('src/RobotFactory.php');

// echo "Starting Robot World" . '<br/>';

// $factory = new RobotFactory();

// $robotTypes = ['human', 'dog', 'cat'];
// $robotsToCreate = 10000;
// $totalInstancesUsed = 0;

// foreach ($robotTypes as $type) {
//     echo "\n--- Creating 10,000 instances of '{$type}' robots ---" . '<br/>';
//     for ($i = 0; $i < $robotsToCreate; $i++) {
//         // get a shared robot object from the factory.
//         $robot = $factory->getRobot($type);

//         // call the display method with the unique, extrinsic state (random coordinates).
//         if ($i < 2) {
//              $robot->display(rand(1, 100), rand(1, 100));
//         }
//         $totalInstancesUsed++;
//     }
// }

// echo "Total robot instances used: " . number_format($totalInstancesUsed) . "\n";
// echo "Total actual robot objects created in memory: " . $factory->totalObjectCreated() . "\n";


class RobotWorldGame 
{
    private $factory;
    private $robotTypes;
    private $robotsToCreate;
    private $totalInstancesUsed;
    
    public function __construct($robotsToCreate = 10000) 
    {
        $this->factory = new RobotFactory();
        $this->robotTypes = ['human', 'dog', 'cat'];
        $this->robotsToCreate = $robotsToCreate;
        $this->totalInstancesUsed = 0;
    }
    
    public function startGame():void 
    {
        echo "Starting robot world game..." . '<br/>';
        
        foreach ($this->robotTypes as $type) {
            $this->createRobotsOfType($type);
        }
        
        $this->displayStatistics();
    }
    
    private function createRobotsOfType($type):void 
    {
        echo "creating {$this->robotsToCreate} instances of '{$type}' robots " . '<br/>';
        
        for ($i = 0; $i < $this->robotsToCreate; $i++) {
            // get a shared robot object from the factory
            $robot = $this->factory->getRobot($type);
            if ($i < 2) {
                $robot->display(rand(1, 100), rand(1, 100));
            }
            $this->totalInstancesUsed++;
        }
    }
    
    private function displayStatistics():void 
    {
        echo "Total robot instances used:: " . $this->totalInstancesUsed . '<br/>';
        echo "Total actual robot objects created in memory:: " . $this->factory->totalObjectCreated() .  '<br/>';
    }
    
    // getter methods 
    public function getTotalInstancesUsed():int 
    {
        return $this->totalInstancesUsed;
    }
    
    public function getTotalObjectsCreated():int 
    {
        return $this->factory->totalObjectCreated();
    }
}
