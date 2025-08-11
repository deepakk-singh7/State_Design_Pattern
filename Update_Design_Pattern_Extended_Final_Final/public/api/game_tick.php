<?php
/**
 * Main Game Loop Controller (FIXED-STEP VERSION).
 */
header('Content-Type: application/json');
session_save_path(__DIR__ . '/../../private_sessions');

// --- AUTOLOAD CLASSES ---
require_once 'src/World.php';
require_once 'src/Entity.php';
require_once 'src/Skeleton.php';
require_once 'src/Statue.php';
require_once 'src/Spawner.php';
require_once 'src/Minion.php';
require_once 'src/LightningBolt.php';

// --- CONSTANTS ---
// The fixed time step for our simulation (e.g., 50 updates per second)
// define('MS_PER_UPDATE', 1.0 / 50.0); // 0.02 seconds
define('MS_PER_UPDATE', 1.0 / 1.0); // A much slower 10 updates per second

session_start();

// --- GAME ACTIONS (RESET) ---
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['world']);
    unset($_SESSION['frame']);
    unset($_SESSION['lag']); // Also reset lag
    echo json_encode(['status' => 'reset']);
    exit;
}

// --- INITIALIZE GAME STATE ---
if (!isset($_SESSION['world'])) {
    $world = new World();
    $world->addEntity(new Skeleton(10, 50));
    $world->addEntity(new Statue(90, 20));
    $world->addEntity(new Spawner(5, 5, 1));

    $_SESSION['world'] = serialize($world);
    $_SESSION['frame'] = 0;
    $_SESSION['lag'] = 0.0;
}

// --- MAIN GAME LOOP (CATCH-UP LOGIC) ---
$world = unserialize($_SESSION['world']);
$_SESSION['frame']++;

// The client tells us how much real time has passed
$rawDt = isset($_GET['dt']) ? (float)$_GET['dt'] : 0.0;
$deltaTime = max(0, $rawDt); 

// Accumulate lag with the real time passed
$lag = $_SESSION['lag'] ?? 0.0;
$lag += $deltaTime;

// "Catch-up" loop: Update the simulation in fixed steps
while ($lag >= MS_PER_UPDATE) {
    // IMPORTANT: Update the world using the FIXED step
    $world->tick(MS_PER_UPDATE);
    $lag -= MS_PER_UPDATE;
}

// Store the remaining lag for the next request
$_SESSION['lag'] = $lag;
$_SESSION['world'] = serialize($world);

// Extract the final state of all entities AFTER the ticks
$entitiesData = [];
foreach ($world->getEntities() as $entity) {
    $entitiesData[] = $entity->getState();
}

// --- SEND RESPONSE ---
echo json_encode([
    'frame' => $_SESSION['frame'],
    'entities' => $entitiesData,
    'interpolation_alpha' => $lag / MS_PER_UPDATE // Send leftover lag as a percentage
]);
// php -S localhost:8000 -t public