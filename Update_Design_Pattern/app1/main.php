<?php 
require_once 'src/World.php';
require_once 'src/Skeleton.php';
require_once 'src/Statue.php';
// 1. Create the world
$world = new World();

// 2. Create entities and add them to the world
$world->addEntity(new Skeleton());
$world->addEntity(new Statue(100, 50)); // Place the statue somewhere else

// 3. Start the game!
$world->gameLoop();