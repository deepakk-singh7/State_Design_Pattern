<?php 

require_once 'src/Stage.php';
require_once 'src/Comedian.php';

// setting up the stage and make new comedians.. 
$stage = new Stage();
$Chump = new Comedian('Chump');
$Baldy = new Comedian('Baldy');
$Harry = new Comedian('Harry');

// setting up the facing configuration of actors :: harry -> baldy -> chump -> harry
$Harry->face($Baldy);
$Baldy->face($Chump);
$Chump->face($Harry);

// adding actors to the stage.. 

// echo "--- Test 1: Harry(0), Baldy(1), Chump(2) ---\n";
// $stage->addActor($Harry,0); // Harry at index 0
// $stage->addActor($Baldy,1); // Baldy at index 1
// $stage->addActor($Chump,2); // Chump at index 2
 
echo "--- Test 2: Harry(2), Baldy(1), Chump(0) ---\n";
$stage->addActor($Harry, 2);  // Harry at index 2
$stage->addActor($Baldy, 1);  // Baldy at index 1
$stage->addActor($Chump, 0);  // Chump at index 0

// start the process..

// initial step 
$Harry->slap();

echo "=== FRAME 1 ===\n";
$stage->update();

echo "\n=== FRAME 2 ===\n";
$stage->update();

echo "\n=== FRAME 3 ===\n";
$stage->update();

echo "\n=== FRAME 4 ===\n";
$stage->update();

