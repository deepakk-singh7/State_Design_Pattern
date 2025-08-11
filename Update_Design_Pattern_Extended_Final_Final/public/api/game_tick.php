<?php
/**
 * Main Game Loop Controller (FIXED-STEP VERSION for 5 Hz updates).
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
// Fixed time step for simulation (5 updates per second)
// define('MS_PER_UPDATE', 1.0 / 5.0); // 5 update per second | 0.2 seconds per update
// define('MS_PER_UPDATE', 1.0 / 1.0);  // 1 update per second
// define('MS_PER_UPDATE', 1.0 / 2.0);  // 2 updates per second
// define('MS_PER_UPDATE', 1.0 / 10.0); // 10 updates per second
define('MS_PER_UPDATE', 1.0 / 60.0);  // 60 Hz

session_start();

// --- GAME ACTIONS (RESET) ---
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['world']);
    unset($_SESSION['frame']);
    unset($_SESSION['last_update_time']);
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
    $_SESSION['last_update_time'] = microtime(true);
}

// --- MAIN GAME LOOP (TIME-BASED CATCH-UP) ---
$world = unserialize($_SESSION['world']);
$currentTime = microtime(true);
$lastUpdateTime = $_SESSION['last_update_time'] ?? $currentTime;

// Calculate how much time has passed since last update
$accumulator = $currentTime - $lastUpdateTime;

// Update the world in fixed time steps
$updatesThisFrame = 0;
$maxUpdatesPerFrame = 3; // Prevent spiral of death

while ($accumulator >= MS_PER_UPDATE && $updatesThisFrame < $maxUpdatesPerFrame) {
    $world->tick(MS_PER_UPDATE);
    $accumulator -= MS_PER_UPDATE;
    $updatesThisFrame++;
    $_SESSION['frame']++;
}

// Store the remaining time for next frame
$_SESSION['last_update_time'] = $currentTime - $accumulator;
$_SESSION['world'] = serialize($world);

// Extract the final state of all entities
$entitiesData = [];
foreach ($world->getEntities() as $entity) {
    $entitiesData[] = $entity->getState();
}

// --- SEND RESPONSE ---
echo json_encode([
    'frame' => $_SESSION['frame'],
    'entities' => $entitiesData,
    'timestamp' => $currentTime, // Server timestamp for client extrapolation
    'updates_this_frame' => $updatesThisFrame // Debug info
]);
?>