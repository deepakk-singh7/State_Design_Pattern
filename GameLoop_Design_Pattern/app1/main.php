<?php 
require_once 'src/Game.php';


echo "========================================\n";
echo "==      SCENARIO 1: FAST PC           ==\n";
echo "========================================\n";
echo "Simulating a fast PC where each frame takes only 10ms to render.\n";

// An instance of Game class for the fast PC scenario.
$fastGame = new Game();
// We call the run method on the fastGame object.
$fastGame->run(100, 10);


echo "\n\n========================================\n";
echo "==      SCENARIO 2: SLOW PC           ==\n";
echo "========================================\n";
echo "Simulating a slow PC where each frame takes a long 50ms to render.\n";

// A separate instance of Game class for the slow PC scenario.
$slowGame = new Game();
// We call the run method on the slowGame object.
$slowGame->run(100, 50);