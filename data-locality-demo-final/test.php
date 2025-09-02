<?php

require_once __DIR__ . '/lib/Dot.php'; 
require_once __DIR__ . '/lib/DotSystem.php';
require_once __DIR__ . '/lib/PhysicsComponent.php';

// --- CONFIGURATION ---
const NUM_DOTS = 50000; 
const WIDTH = 800;
const HEIGHT = 600;
const SIMULATION_STEPS = 1000; // Run the update loop many times => number of renders.. 

// --- OOP TEST ---
$oopDots = [];
// create the temp Dot objects.. 
for ($i = 0; $i < NUM_DOTS; $i++) {
    $oopDots[] = new Dot(
        rand(0, WIDTH), rand(0, HEIGHT),
        new PhysicsComponent((rand(0, 100) / 50) - 1, (rand(0, 100) / 50) - 1)
    );
}
// oopsDots [] => 50,000 dots objects.. 
$oopStartTime = microtime(true);
for ($i = 0; $i < SIMULATION_STEPS; $i++) {
    foreach ($oopDots as $dot) {
        // update is calling 50,000 * 1000 => 50 millions times.. 
        $dot->update(WIDTH, HEIGHT);
    }
}
$oopEndTime = microtime(true);
$oopTotalTime = ($oopEndTime - $oopStartTime) * 1000;

echo "--- Performance Test ---\n";
echo "Dots: " . NUM_DOTS . ", Steps: " . SIMULATION_STEPS . "\n\n";
echo sprintf("OOP (Inefficient) Total Time: %.2f ms\n", $oopTotalTime);


// --- DOD TEST ---
// this will create in 50,000 iterations.. 
$dodData = DotSystem::initialize(NUM_DOTS, WIDTH, HEIGHT);

$dodStartTime = microtime(true);
for ($i = 0; $i < SIMULATION_STEPS; $i++) {
    // here the update is one single opeation so total iterations will be 10,000 iterations only... 
    // ie this update will call only 10k times 
    DotSystem::update($dodData, WIDTH, HEIGHT);
}
// Note :: Why increase the performance in DOD as compared to OOPs
// 1: Decrease in functions calls -> We replace 50M function call(update()) to 10k only... 
// 2: Lower Memory Uses and Object Overhead:: 
    // In OOPs, we are creating 50k dots objects which has referance to 50k of PhysicsComponent object [total = 100k], each need its own Memory 
     // While in DOD, we create only 4 array, size is large, 4*50k but the overhead will be less coz creating object is more expensive operation then array.. 
// 3: More Direct Data Access 
    // In OOPs :: first $this, then $this->x and $this->y and then $this->PhysicsComponent [pointer chessing] also $this->PC->vx and $this->PC->vy...
    // In DOD :: Its just simple array lookup ie $x[$i], $vs[$i]....      
// 4: CPU Cache Locality	 ?????     
$dodEndTime = microtime(true);
$dodTotalTime = ($dodEndTime - $dodStartTime) * 1000;

echo sprintf("DOD (Efficient) Total Time:   %.2f ms\n\n", $dodTotalTime);

$difference = $oopTotalTime - $dodTotalTime;
$percentage = ($difference / $oopTotalTime) * 100;
echo sprintf("Difference: %.2f ms (DOD is %.2f%% faster)\n", $difference, $percentage);
