<?php

require_once 'src/Game.php';
require_once 'src/VM.php';
require_once 'src/Compiler.php';

// First, create the spell files on disk so the script can read them.
// file_put_contents(filename: 'fireball.spell', data: "# Deals 15 damage to the opponent (wizard 1)\nLITERAL 1\nGET_HEALTH\nLITERAL 15\nSUBTRACT\nLITERAL 1\nSET_HEALTH");
// file_put_contents(filename: 'wisdom_boost.spell', data: "# Increases own (wizard 0) wisdom by 5\nLITERAL 0\nGET_WISDOM\nLITERAL 5\nADD\nLITERAL 0\nSET_WISDOM");
// file_put_contents(filename: 'lightning_bolt.spell', data: "# Deals damage equal to own wisdom + own agility to the opponent (wizard 1)\nLITERAL 1\nGET_HEALTH\nLITERAL 0\nGET_WISDOM\nLITERAL 0\nGET_AGILITY\nADD\nSUBTRACT\nLITERAL 1\nSET_HEALTH");

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

$gandalfSpells = [
    'Wisdom Boost' => $compiler->compile(__DIR__ . '/spellData/wisdom_boost.json'),
    'Lightning Bolt' => $compiler->compile(__DIR__ . '/spellData/lightning_bolt.json'),
    'Heal Self' => $compiler->compile(__DIR__ . '/spellData/heal_self.json'),
    'Power Surge' => $compiler->compile(__DIR__ . '/spellData/power_surge.json'),
    'Ice Shard' => $compiler->compile(__DIR__ . '/spellData/ice_shard.json'),
    'Drain Wisdom' => $compiler->compile(__DIR__ . '/spellData/drain_wisdom.json'),
];

$dumbledoreSpells = [
    'Fireball' => $compiler->compile(__DIR__ . '/spellData/fireball.json'),
    'Agility Boost' => $compiler->compile(__DIR__ . '/spellData/agility_boost.json'),
    'Arcane Missile' => $compiler->compile(__DIR__ . '/spellData/arcane_missile.json'),
    'Life Steal' => $compiler->compile(__DIR__ . '/spellData/life_steal.json'),
    'Meteor' => $compiler->compile(__DIR__ . '/spellData/meteor.json'),
];


$turn = 0;
while (!$game->isOver()) {
    $turn++;
    echo "\n======= TURN {$turn} =======\n";
    $game->printStatus();

    // Gandalf's turn (wizard 0)
    $spellName = array_rand($gandalfSpells);
    $spellBytecode = $gandalfSpells[$spellName];
    // print_r($spellBytecode). PHP_EOL;
    $vm->interpret($spellBytecode, 'Gandalf', $spellName);

    if ($game->isOver()) break;

    // Dumbledore's turn (wizard 1)
    $spellName = array_rand($dumbledoreSpells);
    $spellBytecode = $dumbledoreSpells[$spellName];
    $vm->interpret($spellBytecode, 'Dumbledore', $spellName);
}

echo "\n======= DUEL ENDED =======\n";
$game->printStatus();
echo "The winner is... " . $game->getWinner() . "!\n";

// // Clean up the created spell files
// unlink('fireball.spell');
// unlink('wisdom_boost.spell');
// unlink('lightning_bolt.spell');

