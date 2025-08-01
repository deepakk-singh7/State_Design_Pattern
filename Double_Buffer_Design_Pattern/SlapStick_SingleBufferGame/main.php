<?php 
require_once 'src/Stage.php';
require_once 'src/Comedian.php';

    // Setting up the stage and make comedians    
    $stage1 = new Stage();
    $harry1 = new Comedian("Harry");
    $baldy1 = new Comedian("Baldy");
    $chump1 = new Comedian("Chump");
    
    // configuring the facing :: harry -> baldy -> chump -> harry
    $harry1->face($baldy1);
    $baldy1->face($chump1);
    $chump1->face($harry1);
    
    // Adding actors to the stage
    echo "--- Test 1: Harry(0), Baldy(1), Chump(2) ---\n";
    $stage1->addActor($harry1, 0);  // Harry at index 0
    $stage1->addActor($baldy1, 1);  // Baldy at index 1
    $stage1->addActor($chump1, 2);  // Chump at index 2

    // echo "--- Test 2: Harry(2), Baldy(1), Chump(0) ---\n";
    // $stage1->addActor($harry1, 2);  // Harry at index 2
    // $stage1->addActor($baldy1, 1);  // Baldy at index 1
    // $stage1->addActor($chump1, 0);  // Chump at index 0
    
    $harry1->slap(); // Initial slap

    echo "=== FRAME 1 ===\n";
    $stage1->updateStage();

    echo "=== FRAME 2 ===\n";
    $stage1->updateStage();
        
    echo "=== FRAME 3 ===\n";
    $stage1->updateStage();
        
    echo "=== FRAME 4 ===\n";
    $stage1->updateStage();