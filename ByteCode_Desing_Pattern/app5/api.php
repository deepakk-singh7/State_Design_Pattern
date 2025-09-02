<?php

require_once 'Game.php';
require_once 'VM.php';
require_once 'Compiler.php';
require_once 'Instruction.php';

session_start();

/**
 * Compiles all available spells from their JSON definitions.
 * @return array A multi-dimensional array where both players [0] and [1]
 * get the exact same complete list of compiled spells.
 */
function get_all_spells(): array {
    $compiler = new Compiler();
    
    // Define ALL spell files with correct paths and extensions
    $spellFiles = [
        'Fireball' => __DIR__ . '/spellData/fireball.json',
        'Heal Self' => __DIR__ . '/spellData/heal_self.json',
        'Wisdom Boost' => __DIR__ . '/spellData/wisdom_boost.json',
        'Agility Boost' => __DIR__ . '/spellData/agility_boost.json',
        'Lightning Bolt' => __DIR__ . '/spellData/lightning_bolt.json',
        'Drain Wisdom' => __DIR__ . '/spellData/drain_wisdon.json',
        'Life Steal' => __DIR__ . '/spellData/life_steal.json',
        'Arcane Missile' => __DIR__ . '/spellData/arcane_missile.json',
    ];
    
    $allSpells = [];
    
    // Compile ALL spells into one shared array
    foreach ($spellFiles as $spellName => $filePath) {
        try {
            if (file_exists($filePath)) {
                $allSpells[$spellName] = $compiler->compile($filePath);
            } else {
                error_log("Spell file not found: $filePath");
            }
        } catch (Exception $e) {
            error_log("Failed to compile spell '$spellName': " . $e->getMessage());
        }
    }
    
    // Both players get the EXACT SAME array of ALL spells
    return [
        0 => $allSpells,  // Player 0 gets ALL spells
        1 => $allSpells,  // Player 1 gets ALL spells 
    ];
}

$action = $_GET['action'] ?? 'status';

if ($action === 'start' || !isset($_SESSION['game'])) {
    // Clear any existing session data to ensure fresh start
    unset($_SESSION['game']);
    unset($_SESSION['spells']);
    unset($_SESSION['current_turn']);
    
    $game = new Game();
    $_SESSION['game'] = serialize($game);
    $_SESSION['spells'] = get_all_spells();
    $_SESSION['current_turn'] = 0;
}

$game = unserialize($_SESSION['game']);
$spells = $_SESSION['spells'];
$currentTurn = $_SESSION['current_turn'] ?? 0;
$vm = new VM($game);

switch ($action) { 
    case 'start':
    $state = $game->getState();
    $state['spells'] = [
        0 => array_keys($spells[0]),
        1 => array_keys($spells[1]),
    ];
    $state['currentTurn'] = $currentTurn;
    
    echo json_encode($state);
    break;

    case 'cast_spell':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $casterId = $input['casterId'] ?? null;
        $spellName = $input['spellName'] ?? null;

        if ($casterId === null || $spellName === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid spell cast request - missing casterId or spellName.']);
            exit;
        }

        if (!in_array($casterId, [0, 1])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid caster ID.']);
            exit;
        }

        if (!isset($spells[$casterId][$spellName])) {
            http_response_code(400);
            echo json_encode(['error' => "Spell '$spellName' not found for this caster."]);
            exit;
        }

        if ($casterId !== $currentTurn) {
            http_response_code(400);
            echo json_encode(['error' => 'Not your turn!']);
            exit;
        }

        if ($game->isOver()) {
            http_response_code(400);
            echo json_encode(['error' => 'Game is already over!']);
            exit;
        }

        try {
            $bytecode = $spells[$casterId][$spellName];
            $vm->interpret($bytecode, $casterId, $spellName);

            if (!$game->isOver()) {
                $_SESSION['current_turn'] = 1 - $currentTurn;
            }

            $_SESSION['game'] = serialize($game);

            $state = $game->getState();
            $state['spells'] = [
                0 => array_keys($spells[0]),
                1 => array_keys($spells[1]),
            ];
            $state['currentTurn'] = $_SESSION['current_turn'];

            echo json_encode($state);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Spell execution failed: ' . $e->getMessage()]);
        }
        break;

    case 'status':
        $state = $game->getState();
        $state['spells'] = [
            0 => array_keys($spells[0]),
            1 => array_keys($spells[1]),
        ];
        $state['currentTurn'] = $currentTurn;
        echo json_encode($state);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action specified.']);
        break;
}
?>