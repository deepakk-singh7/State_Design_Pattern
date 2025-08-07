<?php 

require_once 'Game.php';
require_once 'VM.php';
require_once 'Instruction.php';
// --- Let's run it! ---



// This spell will set wizard 0's health to 10.
// Read it as: Push 0, Push 10, Call SET_HEALTH.
// $spell = [
//     Instruction::LITERAL, 0,    // Argument: wizardId
//     Instruction::LITERAL, 10,   // Argument: amount
//     Instruction::SET_HEALTH     // The instruction to call
// ];


// --- The Parser ---

/**
 * Parses a text-based spell file into a bytecode array.
 *
 * @param string $filename The path to the spell file.
 * @return int[] The resulting bytecode array.
 * @throws Exception if the file is not found or contains an unknown instruction.
 */
function parseSpell(string $filename): array
{
    echo "--- Parsing Spell File: {$filename} ---\n";

    if (!file_exists($filename)) {
        throw new \Exception("Spell file not found: {$filename}");
    }

    // Step 1: Use Reflection to dynamically get all defined instructions.
    // This creates a map like: ['SET_HEALTH' => 0, 'LITERAL' => 5, ...]
    $refl = new ReflectionClass('Instruction');
    // var_dump($refl) . PHP_EOL;
    $instructionMap = $refl->getConstants();
    // var_dump($instructionMap) . PHP_EOL; 

    // Step 2: Read the file line by line.
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    $bytecode = [];
    foreach ($lines as $line) {
        $line = trim($line);

        // Step 3: Convert the line into a bytecode value.
        if (is_numeric($line)) {
            // It's a literal number, so add it directly.
            $bytecode[] = (int)$line;
        } elseif (isset($instructionMap[$line])) {
            // It's a named instruction, so look up its value.
            $bytecode[] = $instructionMap[$line];
        } else {
            // The instruction is unknown.
            throw new \Exception("Unknown instruction in spell file: {$line}");
        }
    }
    
    echo "--- Parsing Finished ---\n";
    return $bytecode;
}


// --- Let's run it! ---

$game = new Game();
$vm = new VM($game);

// The spell is now read and parsed from the text file.
$spell = parseSpell(__DIR__ . '/spell.txt');

$vm->interpret($spell);