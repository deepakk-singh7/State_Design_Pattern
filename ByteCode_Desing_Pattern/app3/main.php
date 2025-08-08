<?php
/**
 * Main entry point for the Wizard Duel game.
 *
 * This script initializes the game state, compiles spells from JSON definitions,
 * and runs the main game loop, simulating a turn-based duel between two wizards
 * until a winner is determined.
 */

// Include necessary class definitions for the game components.
require_once 'src/Game.php';
require_once 'src/VM.php';
require_once 'src/Compiler.php';


echo "========= WIZARD DUEL BEGINS =========\n";

// create a new game 

$game = new Game();

// create new VM and give access to the game.. 

$vm = new VM($game);

// get the compiler.. 

$compiler = new Compiler();

// $gandalfSpells = [
//     'Wisdom Boost' => $compiler->compile(__DIR__ . '/spellData/wisdom_boost.json'),
//     'Lightning Bolt' => $compiler->compile(__DIR__ . '/spellData/lightning_bolt.json'),
// ];
// $dumbledoreSpells = [
//     'Fireball' => $compiler->compile(__DIR__ . '/spellData/fireball.json')
// ];


// Compile spells for Gandalf.
// Each spell is defined in a separate JSON file and compiled into executable bytecode.
// The resulting bytecode is stored in an associative array for later use.
$gandalfSpells = [
    'Wisdom Boost' => $compiler->compile(__DIR__ . '/spellData/wisdom_boost.json'),
    'Lightning Bolt' => $compiler->compile(__DIR__ . '/spellData/lightning_bolt.json'),
    'Heal Self' => $compiler->compile(__DIR__ . '/spellData/heal_self.json'),
    'Drain Wisdom' => $compiler->compile(__DIR__ . '/spellData/drain_wisdom.json'),
];

// Compile spells for Dumbledore.
$dumbledoreSpells = [
    'Fireball' => $compiler->compile(__DIR__ . '/spellData/fireball.json'),
    'Agility Boost' => $compiler->compile(__DIR__ . '/spellData/agility_boost.json'),
    'Arcane Missile' => $compiler->compile(__DIR__ . '/spellData/arcane_missile.json'),
    'Life Steal' => $compiler->compile(__DIR__ . '/spellData/life_steal.json'),
];

// Initilize the turn counter
$turn = 0;

// The main game loop continues as long as no wizard has been defeated.
while (!$game->isOver()) {
    $turn++;
    echo "\n======= TURN {$turn} =======\n";
    $game->printStatus();

    // Gandalf's turn (wizard 0)
    // A random spell is chosen from his spellbook.
    $spellName = array_rand($gandalfSpells);
    $spellBytecode = $gandalfSpells[$spellName];
    // print_r($spellBytecode). PHP_EOL;
    $vm->interpret($spellBytecode, 'Gandalf', $spellName);

    // Check if the game ended after Gandalf's turn to prevent the other wizard from taking a turn after being defeated.
    if ($game->isOver()) break;

    // Dumbledore's turn (wizard 1)
    // A random spell is chosen from his spellbook.
    $spellName = array_rand($dumbledoreSpells);
    $spellBytecode = $dumbledoreSpells[$spellName];
    $vm->interpret($spellBytecode, 'Dumbledore', $spellName);
}

// Announce the end of the duel and declare the winner.
echo "\n======= DUEL ENDED =======\n";
$game->printStatus();
echo "The winner is... " . $game->getWinner() . "!\n";


