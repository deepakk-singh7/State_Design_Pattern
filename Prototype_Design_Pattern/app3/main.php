<?php 

require_once 'src/CloneableGhost.php';
require_once 'src/CloneableDemon.php';
require_once 'src/CloneableSorcerer.php';
require_once 'src/MonsterSpawner.php';

// creating prototype monsters with specific configurations : 6 tyeps of prototype monsters
$weakGhostPrototype = new CloneableGhost(10, 2);    
$strongGhostPrototype = new CloneableGhost(25, 5);  
$regularDemonPrototype = new CloneableDemon(20, 4, 8);
$bossDemonPrototype = new CloneableDemon(50, 6, 15);  
$apprenticePrototype = new CloneableSorcerer(8, 2, 5);
$masterPrototype = new CloneableSorcerer(15, 3, 20);


// creating spawners using these prototypes

$weakGhostSpawner = new MonsterSpawner($weakGhostPrototype);
$strongGhostSpawner = new MonsterSpawner($strongGhostPrototype);
$regularDemonSpawner = new MonsterSpawner($regularDemonPrototype);
$bossDemonSpawner = new MonsterSpawner($bossDemonPrototype);
$apprenticeSpawner = new MonsterSpawner($apprenticePrototype);
$masterSpawner = new MonsterSpawner($masterPrototype);

// use spawners to create monsters

echo "spawning weak ghosts:\n";
$weakGhost1 = $weakGhostSpawner->spawnMonster();
$weakGhost2 = $weakGhostSpawner->spawnMonster();
$weakGhost3 = $weakGhostSpawner->spawnMonster();

// echo $weakGhost1 . PHP_EOL . $weakGhost2 . PHP_EOL . $weakGhost3 . PHP_EOL;

echo "\nspawning strong ghosts:\n";
$strongGhost1 = $strongGhostSpawner->spawnMonster();
$strongGhost2 = $strongGhostSpawner->spawnMonster();

// echo $strongGhost1 . PHP_EOL . $strongGhost2 . PHP_EOL;

echo "\nspawning boss demons:\n";
$bossDemon1 = $bossDemonSpawner->spawnMonster();
$bossDemon2 = $bossDemonSpawner->spawnMonster();
// echo $bossDemon1 . PHP_EOL . $bossDemon2 . PHP_EOL . PHP_EOL;


