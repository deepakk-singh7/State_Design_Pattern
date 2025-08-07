<?php 
require_once 'Game.php';
require_once 'VM.php';
// --- Let's run it! ---

// 1. Create an instance of our game engine API
$game = new Game();

// 2. Create an instance of our VM, giving it access to the game.
$vm = new VM($game);

// 3. Define a spell as a sequence of bytecode instructions. [directly bytecode]
// This spell will max out wisdom, play a sound, then spawn particles.
$spell = [
    Instruction::SET_WISDOM,
    Instruction::PLAY_SOUND,
    Instruction::SPAWN_PARTICLES
];

// 4. Give the spell to the VM to interpret!
$vm->interpret($spell);