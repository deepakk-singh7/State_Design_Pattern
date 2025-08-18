<?php

// Centralized Includes
require_once __DIR__ . '/../../src/GameObject.php';
require_once __DIR__ . '/../../src/components/Component.php';
require_once __DIR__ . '/../../src/components/RenderDataComponent.php';
require_once __DIR__ . '/../../src/components/HealthComponent.php';
require_once __DIR__ . '/../../src/components/MovementComponent.php';

session_start();
header('Content-Type: application/json');

/**
 * Factory function to create a new, fresh game state.
 * This is where objects are "assembled" from components.
 * @return array The complete initial game state.
 */
function initializeGameState(): array
{
    // Create the player object and attach its behavior components.
    $player = new GameObject('player', 180, 180);
    $player->health = 0; 
    $player->addComponent(new RenderDataComponent('#3498db', 30));
    $player->addComponent(new MovementComponent());
    $player->addComponent(new HealthComponent());

    // Create the health pack object. It only needs a render component.
    $healthPack = new GameObject('health_pack', rand(20, 360), rand(20, 360));
    $healthPack->addComponent(new RenderDataComponent('#2ecc71', 20));

    // // Return the state as an associative array.
    // return ['player' => $player, 'health_pack' => $healthPack];
    // CHANGED: Return a simple, indexed array of all objects in the game.
    return [$player, $healthPack];
}

/**
 * A generic helper function for AABB collision detection.
 * @return bool True if the objects are colliding.
 */
function checkCollision(GameObject $a, GameObject $b): bool
{
    $aRender = $a->getComponent(RenderDataComponent::class);
    $bRender = $b->getComponent(RenderDataComponent::class);

    if (!$aRender || !$bRender) return false;
    // This check is now more specific, which will remove the warning.
    if ($aRender instanceof RenderDataComponent && $bRender instanceof RenderDataComponent) {
        return ($a->x < $b->x + $bRender->size && $a->x + $aRender->size > $b->x &&
                $a->y < $b->y + $bRender->size && $a->y + $aRender->size > $b->y);
    }
    return false;
}

/**
 * NEW: This is our "Collision System" or "Handler".
 * It contains all the game's collision rules.
 * @param array $objects The list of all game objects.
 */
function handleCollisions(array &$objects): void
{
    $objectCount = count($objects);
    for ($i = 0; $i < $objectCount; $i++) {
        for ($j = $i + 1; $j < $objectCount; $j++) {
            $object1 = $objects[$i];
            $object2 = $objects[$j];

            if (checkCollision($object1, $object2)) {
                // --- GAME RULES ARE NOW HERE ---

                // Rule: If a 'player' collides with a 'health_pack'...
                if (($object1->id === 'player' && $object2->id === 'health_pack') || 
                    ($object2->id === 'player' && $object1->id === 'health_pack')) {
                    
                    // Identify which is the player and which is the pack.
                    $player = ($object1->id === 'player') ? $object1 : $object2;
                    $pack = ($object1->id === 'health_pack') ? $object1 : $object2;

                    // Apply the effects.
                    $player->getComponent(HealthComponent::class)->heal($player, 25);
                    $pack->x = rand(20, 360);
                    $pack->y = rand(20, 360);
                }
                
                // FUTURE: Add more rules here, e.g., if a 'player' hits an 'enemy'.
            }
        }
    }
}

//  Main Script Logic

// Load the game state from the session. If it doesn't exist (first run), initialize it.
$gameState = $_SESSION['game_state'] ?? initializeGameState();
// Get the raw JSON input sent from the client.
$input = json_decode(file_get_contents('php://input'), true);
// Determine the action the client wants to perform (e.g., 'move', 'reset').
$action = $input['action'] ?? 'getState';

// Get references to the objects from the game state array.
// $player = $gameState['player'];
// $healthPack = $gameState['health_pack'];
$player = null;
foreach ($gameState as $object) {
    if ($object->id === 'player') {
        $player = $object;
        break;
    }
}

// This is the main action router. It processes the client's command.
switch ($action) {
    case 'move':
        if($player){
        $direction = $input['data']['direction'] ?? '';
        // Find the player's MovementComponent and tell it to move the player.
        $player->getComponent(MovementComponent::class)->move($player, $direction);
        }
        break;
    case 'reset':
        // If the client wants to reset, just create a new game state.
        $gameState = initializeGameState();
        break;
}


// Re-assign references in case the state was reset.
// $player = $gameState['player'];
// $healthPack = $gameState['health_pack'];

// Get the render components to access their 'size' property for the collision check.
// $pRender = $player->getComponent(RenderDataComponent::class);
// $hRender = $healthPack->getComponent(RenderDataComponent::class);

// Game Rules and State Updates

// Perform an AABB collision check between the player and the health pack.
// if ($pRender && $hRender && $player->x < $healthPack->x + $hRender->size && $player->x + $pRender->size > $healthPack->x &&
//     $player->y < $healthPack->y + $hRender->size && $player->y + $pRender->size > $healthPack->y) {
    
//     // If a collision occurs, find the player's HealthComponent and tell it to heal.
//     $player->getComponent(HealthComponent::class)->heal($player, 25);
    
//     // Move the health pack to a new random position.
//     $healthPack->x = rand(20, 360);
//     $healthPack->y = rand(20, 360);
// }

handleCollisions($gameState);

// Save the (potentially modified) game state back into the session for the next request.
$_SESSION['game_state'] = $gameState;
// Send the complete, updated game state back to the client as a JSON string.
echo json_encode($gameState);