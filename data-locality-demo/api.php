<?php

require_once __DIR__ . '/config/AppConfig.php';
require_once __DIR__ . '/lib/Dot.php';

session_start();
header('Content-Type: application/json');

// --- Initialize state if it doesn't exist in the session ---
if (!isset($_SESSION[Keys::DOTS_INITIALIZED])) {
    // Inefficient mode: Array of Objects
    $inefficientDots = [];
    for ($i = 0; $i < Simulation::NUM_DOTS; $i++) {
        $inefficientDots[] = new Dot(
            rand(0, Simulation::WIDTH),
            rand(0, Simulation::HEIGHT),
            new PhysicsComponent((rand(0, 100) / 50) - 1, (rand(0, 100) / 50) - 1)
        );
    }
    $_SESSION[Keys::INEFFICIENT_DOTS] = $inefficientDots;

    // Efficient mode: Struct of Arrays
    $efficientState = [
        Keys::X => [], Keys::Y => [], Keys::VX => [], Keys::VY => []
    ];
    for ($i = 0; $i < Simulation::NUM_DOTS; $i++) {
        $efficientState[Keys::X][$i] = rand(0, Simulation::WIDTH);
        $efficientState[Keys::Y][$i] = rand(0, Simulation::HEIGHT);
        $efficientState[Keys::VX][$i] = (rand(0, 100) / 50) - 1;
        $efficientState[Keys::VY][$i] = (rand(0, 100) / 50) - 1;
    }
    $_SESSION[Keys::EFFICIENT_DOTS] = $efficientState;

    $_SESSION[Keys::DOTS_INITIALIZED] = true;
}


// --- Process the request based on the mode ---
$mode = $_GET[Keys::MODE] ?? Mode::Inefficient->value;
$startTime = microtime(true);
$positions = [];

if ($mode === Mode::Inefficient->value) {
    /** @var Dot[] $dots */
    $dots = $_SESSION[Keys::INEFFICIENT_DOTS];
    foreach ($dots as $dot) {
        $dot->update(Simulation::WIDTH, Simulation::HEIGHT);
    }
    foreach ($dots as $dot) {
        $positions[] = [Keys::X => $dot->x, Keys::Y => $dot->y];
    }
    $_SESSION[Keys::INEFFICIENT_DOTS] = $dots;

} else { // Efficient Mode
    $dots = $_SESSION[Keys::EFFICIENT_DOTS];
    
    // Unpack arrays into local variables for faster access
    $x = $dots[Keys::X];
    $y = $dots[Keys::Y];
    $vx = $dots[Keys::VX];
    $vy = $dots[Keys::VY];

    // Loop using the local variables
    for ($i = 0; $i < Simulation::NUM_DOTS; $i++) {
        $x[$i] += $vx[$i];
        $y[$i] += $vy[$i];
        if ($x[$i] <= 0 || $x[$i] >= Simulation::WIDTH) $vx[$i] *= -1;
        if ($y[$i] <= 0 || $y[$i] >= Simulation::HEIGHT) $vy[$i] *= -1;
    }

    // Re-pack the updated arrays back into the main state array
    $dots[Keys::X] = $x;
    $dots[Keys::Y] = $y;
    $dots[Keys::VX] = $vx;
    $dots[Keys::VY] = $vy;
    $_SESSION[Keys::EFFICIENT_DOTS] = $dots;

    // Prepare JSON response
    for ($i = 0; $i < Simulation::NUM_DOTS; $i++) {
        $positions[] = [Keys::X => $x[$i], Keys::Y => $y[$i]];
    }
}

$endTime = microtime(true);
$serverTime = ($endTime - $startTime) * 1000; // in milliseconds

// --- Send JSON response ---
echo json_encode([
    Keys::POSITIONS => $positions,
    Keys::SERVER_TIME => $serverTime,
]);