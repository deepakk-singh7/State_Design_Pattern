<?php
/**
 * Main Game Loop Controller.
 *
 * This script manages the game state via PHP sessions. It handles game
 * initialization, frame-by-frame updates (ticks), and state persistence.
 * It serves the current game state as a JSON response to the client.
 */

// Set the content type of the response to JSON, so clients handle it correctly.
header('Content-Type: application/json');

// Define a custom, secure path for session files.
session_save_path(__DIR__ . '/../../private_sessions');

/**
 * --------------------------------------------------------------------------
 * AUTOLOAD CLASSES
 * --------------------------------------------------------------------------
 *
 * Include all necessary entity and world class definitions.
 */
require_once 'src/World.php';
require_once 'src/Entity.php';
require_once 'src/Skeleton.php';
require_once 'src/Statue.php';
require_once 'src/Spawner.php';
require_once 'src/Minion.php';
require_once 'src/LightningBolt.php';

// Start or resume the PHP session to manage game state across requests.
session_start();

/**
 * GAME ACTIONS (e.g., RESET)
 * Handle specific actions passed via GET parameters.
 */
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['world']);
    unset($_SESSION['frame']);
    // Send a confirmation status and terminate the script.
    echo json_encode(['status' => 'reset']);
    exit;
}

/**
 * INITIALIZE GAME STATE
 * If no game world exists in the session, create and initialize a new one.
 * This block runs only on the first request of a new game session.
 */
if (!isset($_SESSION['world'])) {
    $world = new World();
    
    // Add the initial set of entities to the world.
    $world->addEntity(new Skeleton(10, 50));
    $world->addEntity(new Statue(90, 20));   // Will shoot bolts
    $world->addEntity(new Spawner(5, 5, 1)); // Will spawn minions

    // Serialize the initial world state and store it in the session.
    $_SESSION['world'] = serialize($world);
    $_SESSION['frame'] = 0;
}

/**
 * --------------------------------------------------------------------------
 * MAIN GAME LOOP
 * --------------------------------------------------------------------------
 *
 * For every subsequent request, load the state, process a game tick,
 * and save the new state.
 */

// Load the game state from the session by unserializing the World object.
$world = unserialize($_SESSION['world']);
$_SESSION['frame']++;

// Get delta time from the client, with a sensible default for the first frame.
// dt is in seconds (e.g., 0.016 for 60 FPS).
// Get delta time from the client.
$rawDt = isset($_GET['dt']) ? (float)$_GET['dt'] : 0.016;

// Sanitize the value: ensure it's never negative.
$deltaTime = max(0, $rawDt); 

// Process one game frame (tick). passing the delta time to the simulation. All entity updates and interactions happen here.
// The tick() method returns an array of data[entitites].
$entitiesData = $world->tick($deltaTime); 

// Save the new, updated game state back into the session for the next frame.
$_SESSION['world'] = serialize($world);

/**
 * SEND RESPONSE
 * Output the current game state as a JSON object.
 */
echo json_encode([
    'frame' => $_SESSION['frame'],
    'entities' => $entitiesData
]);

// Command to run :: php -S localhost:8000 -t public