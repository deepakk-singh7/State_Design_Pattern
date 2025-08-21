<?php

// Centralized Includes 
// Add the new Enums to the includes
require_once __DIR__ . '/../../src/enums/EntityType.php';
require_once __DIR__ . '/../../src/enums/Direction.php';
require_once __DIR__ . '/../../src/enums/ClientAction.php';
require_once __DIR__ . '/../../src/enums/GameConstants.php';

// Core classes
require_once __DIR__ . '/../../src/GameObject.php';
// Interface
require_once __DIR__ . '/../../src/interfaces/Collectable.php';
// Components
require_once __DIR__ . '/../../src/components/Component.php';
require_once __DIR__ . '/../../src/components/RenderDataComponent.php';
require_once __DIR__ . '/../../src/components/HealthComponent.php';
require_once __DIR__ . '/../../src/components/MovementComponent.php';
require_once __DIR__ . '/../../src/components/HealthPackComponent.php';
require_once __DIR__ . '/../../src/components/PoisonPackComponent.php';
require_once __DIR__ . '/../../src/components/SpeedBoostComponent.php';

// Systems
require_once __DIR__ . '/../../src/systems/CollisionSystem.php';
// Game
require_once __DIR__ . '/../../src/Game.php';


// --- 1. SETUP ---
session_start();
header('Content-Type: application/json');

// --- 2. LOAD GAME STATE ---
// Load the Game object from the session, or create a new one.
// const GAME_STATE = 'game_state';
$game = isset($_SESSION['game_state']) ? unserialize($_SESSION['game_state']) : new Game();

// --- 3. HANDLE CLIENT INPUT ---
$input = json_decode(file_get_contents('php://input'), true);
// $action = $input['action'] ?? 'getState';
// Use the ClientAction enum to safely handle the incoming action.
$actionString = $input['action'] ?? ClientAction::GetState->value;
$actionEnum = ClientAction::tryFrom($actionString) ?? ClientAction::GetState;

// Delegate the action to the Game object.
switch ($actionEnum) {
    case ClientAction::Move:
        $directionString = $input['data']['direction'] ?? '';
        $directionEnum = Direction::tryFrom($directionString);
        
        if ($directionEnum) {
            $game->movePlayer($directionEnum);
        }
        break;

    case ClientAction::Reset:
        $game->reset();
        break;

    case ClientAction::GetState:
        // do nothing here, just proceed to the update and response steps.
        break;
}


// --- 4. UPDATE GAME LOGIC ---
// Run all the game's systems for this "tick", mainly collisionSystem
$game->update();


// --- 5. SAVE AND RESPOND ---
// Save the updated Game object back to the session.
$_SESSION['game_state'] = serialize($game);
// Send the game state back to the client.
echo json_encode($game);