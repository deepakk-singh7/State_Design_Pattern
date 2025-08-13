<?php
/**
 * Main API Entry Point / Router
 */
header('Content-Type: application/json');
session_save_path(__DIR__ . '/../private_sessions');
session_start();

// --- CONFIG & INCLUDES ---
require_once 'config.php'; // Use the new config file
require_once 'src/Service/GameService.php';
require_once 'src/Controller/GameController.php';
require_once 'src/World.php';
require_once 'src/Entity.php';
require_once 'src/Skeleton.php';
require_once 'src/Statue.php';
require_once 'src/Spawner.php';
require_once 'src/Minion.php';
require_once 'src/LightningBolt.php';

// 1. Create the service with the global session state.
$gameService = new GameService($_SESSION);

// 2. Create the controller and give it the service.
$controller = new GameController($gameService);

// 3. Determine the action from the query string, defaulting to 'tick'.
$action = $_GET['action'] ?? 'tick';

// 4. Route the request to the correct controller method.
switch ($action) {
    case 'reset':
        $controller->reset();
        break;

    case 'tick':
    default:
        $controller->tick();
        break;
}
//php -S localhost:8000 -t public