<?php
require_once 'Instruction.php';

class Compiler {
    private static ?array $instructionMap = null;
    
    public function __construct(){
        if (self::$instructionMap === null) {
            $refl = new \ReflectionClass('Instruction');
            self::$instructionMap = $refl->getConstants();
        }
    }

    /**
     * Compiles a spell from a JSON file into bytecode.
     */
    public function compile(string $fileName): array {
        if (!file_exists(filename: $fileName)) {
            throw new \Exception("Spell file not found: {$fileName}\n");
        }

        $jsonContent = file_get_contents($fileName);
        if ($jsonContent === false) {
            throw new \Exception("Could not read spell file: {$fileName}\n");
        }

        $spellData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON in spell file: {$fileName} - " . json_last_error_msg());
        }

            return $this->compileSimpleFormat($spellData, $fileName);
    }

    /**
     * Compiles the spell format
     */
    private function compileSimpleFormat(array $spellData, string $fileName): array {
        if (!isset($spellData['effects']) || !is_array($spellData['effects'])) {
            throw new \Exception("No effects found in spell file: {$fileName}");
        }

        if (empty($spellData['effects'])) {
            throw new \Exception("Empty effects in spell file: {$fileName}");
        }

        $bytecode = [];

        foreach ($spellData['effects'] as $index => $effect) {
            if (!isset($effect['target']) || !isset($effect['stat']) || !isset($effect['value'])) {
                throw new \Exception("Invalid effect at index {$index} in {$fileName}: missing target, stat, or value");
            }

            $target = strtolower($effect['target']);
            $stat = strtolower($effect['stat']);
            $value = (int)$effect['value'];
            $operation = isset($effect['operation']) ? strtolower($effect['operation']) : 'add';

            // Validate target
            if (!in_array($target, ['self', 'opponent'])) {
                throw new \Exception("Invalid target '{$target}' at index {$index} in {$fileName}. Must be 'self' or 'opponent'");
            }

            // Validate stat
            if (!in_array($stat, ['health', 'wisdom', 'agility'])) {
                throw new \Exception("Invalid stat '{$stat}' at index {$index} in {$fileName}. Must be 'health', 'wisdom', or 'agility'");
            }

            // Generate bytecode for this effect
            $this->generateEffectBytecode($bytecode, $target, $stat, $value, $operation);
        }

        return $bytecode;
    }

    /**
     * Generates bytecode for a single effect
     */
    private function generateEffectBytecode(array &$bytecode, string $target, string $stat, int $value, string $operation): void {
        // Push target placeholder (will be replaced at runtime)
        $bytecode[] = self::$instructionMap['LITERAL'];
        $bytecode[] = ($target === 'self') ? -1 : -2; // Use negative values as placeholders

        // Get current stat value
        $getInstruction = 'GET_' . strtoupper($stat);
        $bytecode[] = self::$instructionMap[$getInstruction];

        // Apply operation SET, ADD, SUBTRACT
        if ($operation === 'set') {
            // For set operation, just push the new value
            $bytecode[] = self::$instructionMap['LITERAL'];
            $bytecode[] = $value;
        } else {
            // For add/subtract, push the value and perform operation
            $bytecode[] = self::$instructionMap['LITERAL'];
            $bytecode[] = $value;
            
            if ($operation === 'add') {
                $bytecode[] = self::$instructionMap['ADD'];
            } else { // subtract
                $bytecode[] = self::$instructionMap['SUBTRACT'];
            }
        }

        // Push target again for setter
        $bytecode[] = self::$instructionMap['LITERAL'];
        $bytecode[] = ($target === 'self') ? -1 : -2;

        // Set the stat
        $setInstruction = 'SET_' . strtoupper($stat);
        $bytecode[] = self::$instructionMap[$setInstruction];
    }
}