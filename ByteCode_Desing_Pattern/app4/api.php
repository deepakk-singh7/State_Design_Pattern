<?php
// This ensures that the only output is the JSON you explicitly echo.
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Allow requests from any origin for local development
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'Game.php';
require_once 'VM.php';
require_once 'Compiler.php';
require_once 'Instruction.php';

function get_all_spells(): array {
    $compiler = new Compiler();
    return [
        0 => [ // Gandalf's Spells
            'Wisdom Boost' => $compiler->compile(__DIR__ . '/spellData/wisdom_boost.json'),
            'Lightning Bolt' => $compiler->compile(__DIR__ . '/spellData/lightning_bolt.json'),
            'Heal Self' => $compiler->compile(__DIR__ . '/spellData/heal_self.json'),
            'Drain Wisdom' => $compiler->compile(__DIR__ . '/spellData/drain_wisdom.json'),
        ],
        1 => [ // Dumbledore's Spells
            'Fireball' => $compiler->compile(__DIR__ . '/spellData/fireball.json'),
            'Agility Boost' => $compiler->compile(__DIR__ . '/spellData/agility_boost.json'),
            'Arcane Missile' => $compiler->compile(__DIR__ . '/spellData/arcane_missile.json'),
            'Life Steal' => $compiler->compile(__DIR__ . '/spellData/life_steal.json'),
        ]
    ];
}

$action = $_GET['action'] ?? 'status';

if ($action === 'start' || !isset($_SESSION['game'])) {
    $game = new Game();
    $_SESSION['game'] = serialize($game);
    $_SESSION['spells'] = get_all_spells();
    $_SESSION['current_turn'] = 0; // Track whose turn it is (0 or 1)
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

        if ($casterId === null || $spellName === null || !isset($spells[$casterId][$spellName])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid spell cast request.']);
            exit;
        }

        // Check if it's the correct player's turn
        if ($casterId !== $currentTurn) {
            http_response_code(400);
            echo json_encode(['error' => 'Not your turn!']);
            exit;
        }

        // Check if game is already over
        if ($game->isOver()) {
            http_response_code(400);
            echo json_encode(['error' => 'Game is already over!']);
            exit;
        }

        // Execute the spell
        $bytecode = $spells[$casterId][$spellName];
        $vm->interpret($bytecode, $casterId, $spellName);

        // Switch to next player's turn (only if game is not over)
        if (!$game->isOver()) {
            $_SESSION['current_turn'] = 1 - $currentTurn;
        }

        $_SESSION['game'] = serialize($game);

        // Get the current game state and add the spell list before sending
        $state = $game->getState();
        $state['spells'] = [
            0 => array_keys($spells[0]),
            1 => array_keys($spells[1]),
        ];
        $state['currentTurn'] = $_SESSION['current_turn'];

        echo json_encode($state);
        break;

    case 'ai_turn':
        // Optional: Allow AI to make a move if it's AI's turn
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit;
        }

        if ($game->isOver()) {
            http_response_code(400);
            echo json_encode(['error' => 'Game is already over!']);
            exit;
        }

        // For now, let's assume player 1 is AI (you can modify this logic)
        if ($currentTurn === 1) {
            $opponentSpells = $spells[1];
            $opponentSpellNames = array_keys($opponentSpells);
            $opponentSpellName = $opponentSpellNames[array_rand($opponentSpellNames)];
            $opponentBytecode = $opponentSpells[$opponentSpellName];
            $vm->interpret($opponentBytecode, 1, $opponentSpellName);

            // Switch turn back to player
            if (!$game->isOver()) {
                $_SESSION['current_turn'] = 0;
            }
        }

        $_SESSION['game'] = serialize($game);

        $state = $game->getState();
        $state['spells'] = [
            0 => array_keys($spells[0]),
            1 => array_keys($spells[1]),
        ];
        $state['currentTurn'] = $_SESSION['current_turn'];

        echo json_encode($state);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action specified.']);
        break;
}