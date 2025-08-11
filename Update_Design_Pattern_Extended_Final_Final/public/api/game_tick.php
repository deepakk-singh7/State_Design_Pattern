<?php
/**
 * Main Game Tick Controller (SIMPLIFIED - "TICK ONCE" MODEL).
 * This script advances the simulation by one fixed step each time it's called.
 */
header('Content-Type: application/json');
session_save_path(__DIR__ . '/../../private_sessions');
session_start();

// --- AUTOLOAD CLASSES ---
require_once 'src/World.php';
require_once 'src/Entity.php';
require_once 'src/Skeleton.php';
require_once 'src/Statue.php';
require_once 'src/Spawner.php';
require_once 'src/Minion.php';
require_once 'src/LightningBolt.php';

// --- CONSTANTS ---
// The server's tick rate should match the client's call frequency.
// For a 5Hz update rate from the client, each tick is 1/5th of a second.
define('FIXED_TIMESTEP', 1.0 / 5.0); // 0.2 seconds

// --- GAME ACTIONS (RESET) ---
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    session_unset();
    session_destroy();
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
}

// --- MAIN LOGIC (TICK ONCE) ---
// 1. Load the world from the session.
$world = unserialize($_SESSION['world']);
$_SESSION['frame']++;

// 2. Update the world by ONE fixed step. No more loops.
$world->tick(FIXED_TIMESTEP);

// 3. Save the new state back to the session.
$_SESSION['world'] = serialize($world);

// 4. Extract the final state of all entities AFTER the tick.
$entitiesData = [];
foreach ($world->getEntities() as $entity) {
    $entitiesData[] = $entity->getState();
}

// --- SEND RESPONSE ---
// The response includes the precise server timestamp for client extrapolation.
echo json_encode([
    'frame' => $_SESSION['frame'],
    'entities' => $entitiesData,
    'timestamp' => microtime(true) // This is correct!
]);