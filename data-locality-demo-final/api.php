<?php

require_once __DIR__ . '/config/AppConfig.php';
require_once __DIR__ . '/lib/Dot.php'; // OOP 
require_once __DIR__ . '/lib/DotSystem.php'; // DOD

session_start();
header('Content-Type: application/json');

// --- Initialize state if it doesn't exist in the session ---
if (!isset($_SESSION[Keys::DOTS_INITIALIZED])) {
    
    // 1: OOP mode: Array of Objects 
    $oopDots = [];
    for ($i = 0; $i < Simulation::NUM_DOTS; $i++) {
        $oopDots[] = new Dot(
            rand(0, Simulation::WIDTH),
            rand(0, Simulation::HEIGHT),
            new PhysicsComponent((rand(0, 100) / 50) - 1, (rand(0, 100) / 50) - 1)
        );
    }
    $_SESSION[Keys::INEFFICIENT_DOTS] = $oopDots;
    /**
     * oopsDots 
     * [x, y, *PhysicsComponent] // [1000,1004,1008]
     *              |
     *          [ vx, vy] // [3000,3008]
     */

    // 2: DOD mode: Pure data arrays managed by stateless systems
    $dodData = DotSystem::initialize(
        Simulation::NUM_DOTS, 
        Simulation::WIDTH, 
        Simulation::HEIGHT
    ); // [x, y, vx, vy] // [1000, 1004, 1008, 1012]
    $_SESSION[Keys::EFFICIENT_DOTS] = $dodData;

    $_SESSION[Keys::DOTS_INITIALIZED] = true;
}

// --- Process the request based on the mode ---
$mode = $_GET[Keys::MODE] ?? Mode::Inefficient->value;
$startTime = microtime(true);
$positions = [];

if ($mode === Mode::Inefficient->value) {
    // OOP Pattern: Objects manage their own state and behavior
    /** @var Dot[] $dots */
    $dots = $_SESSION[Keys::INEFFICIENT_DOTS];
    
    // update the properties of each dot
    foreach ($dots as $dot) {
        $dot->update(Simulation::WIDTH, Simulation::HEIGHT);
    }
    // extract the positions from the updated dot.
    foreach ($dots as $dot) {
        $positions[] = [Keys::X => $dot->x, Keys::Y => $dot->y];
    }
    // store back in sesssion data.. 
    $_SESSION[Keys::INEFFICIENT_DOTS] = $dots;

} else {
    // DOD Pattern: Systems operate on pure data
    $dotData = $_SESSION[Keys::EFFICIENT_DOTS]; // [x, y, vx, vy]
    
    // Single function call that processes all data efficiently
    DotSystem::update($dotData, Simulation::WIDTH, Simulation::HEIGHT);
    
    // Extract only the data we need for response
    $positions = DotSystem::getPositions($dotData); // [x, y]
    
    // Store updated data back
    $_SESSION[Keys::EFFICIENT_DOTS] = $dotData;
}

$endTime = microtime(true);
$serverTime = ($endTime - $startTime) * 1000; // in milliseconds

// Optional: Add physics stats for DOD mode
$response = [
    Keys::POSITIONS => $positions,
    Keys::SERVER_TIME => $serverTime,
];


// --- Send JSON response ---
echo json_encode($response);