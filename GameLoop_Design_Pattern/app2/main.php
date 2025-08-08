<?php 

require_once 'src/Game.php';
// --- Running the Demonstration ---

echo "======================================================\n";
echo "==      SCENARIO 1: FAST PC (with Interpolation)    ==\n";
echo "======================================================\n";
echo "Simulating a fast PC where each frame takes only 10ms to render.\n";

$fastGame = new Game();
$fastGame->run(100, 10);


echo "\n\n======================================================\n";
echo "==      SCENARIO 2: SLOW PC (with Interpolation)    ==\n";
echo "======================================================\n";
echo "Simulating a slow PC where each frame takes a long 50ms to render.\n";

$slowGame = new Game();
$slowGame->run(100, 50);