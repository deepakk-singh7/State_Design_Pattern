<?php 
require_once('Prototype_Design_Pattern/app2/src/GhostSpawner.php');
require_once('Prototype_Design_Pattern/app2/src/DemonSpawner.php');

$ghostSpawner = new GhostSpawner();
$demonSpawner = new DemonSpawner();

$ghost1 = $ghostSpawner->spawnMonster(); // health 15, speed 3
$ghost2 = $ghostSpawner->spawnMonster(); // health 15, speed 3
$demon1 = $demonSpawner->spawnMonster(); //health 20, speed 4, strength 8

echo "Traditional spawning results:\n";
echo "Ghost 1: " . $ghost1->getInfo() . "\n";
echo "Ghost 2: " . $ghost2->getInfo() . "\n";
echo "Demon 1: " . $demon1->getInfo() . "\n";

// what if we want a weak ghost or strong demon? We will need more spawner classes... => duplicate code, hard to maintain.. 
