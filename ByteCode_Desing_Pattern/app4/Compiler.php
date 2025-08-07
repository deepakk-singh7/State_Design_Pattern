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
        // echo $fileName . PHP_EOL;
        // 1. Check for the file's existence.
        if (!file_exists($fileName)) {
            throw new \Exception("Spell file not found: {$fileName}\n");
        }

        // 2. Read the entire file content.
        $jsonContent = file_get_contents($fileName);
        if ($jsonContent === false) {
            throw new \Exception("Could not read spell file: {$fileName}\n");
        }

        // 3. Decode the JSON string into a PHP associative array.
        $spellData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON in spell file: {$fileName} - " . json_last_error_msg());
        }

        // 4. Check if the 'instructions' key exists.
        // if (!isset($spellData['instructions']) || !is_array($spellData['instructions'])) {
        //      throw new \Exception("No instructions found in spell file: {$fileName}\n");
        // }
        // Validate required fields
        if (!isset($spellData['instructions']) || !is_array($spellData['instructions'])) {
            throw new \Exception("No instructions found in spell file: {$fileName}");
        }

        if (empty($spellData['instructions'])) {
            throw new \Exception("Empty instructions in spell file: {$fileName}");
        }

        // Validate each instruction
        foreach ($spellData['instructions'] as $index => $instruction) {
            if (!isset($instruction['code']) || !is_string($instruction['code'])) {
                throw new \Exception("Invalid instruction at index {$index} in {$fileName}: missing or invalid 'code'");
            }
            
            $instructionName = strtoupper($instruction['code']);
            if ($instructionName === 'LITERAL' && !isset($instruction['value'])) {
                throw new \Exception("LITERAL instruction at index {$index} missing 'value' in {$fileName}");
            }
        }

        $bytecode = [];
        // 5. Loop through the instructions array.
        foreach ($spellData['instructions'] as $instruction) {
            $instructionName = strtoupper($instruction['code']);

            if (isset(self::$instructionMap[$instructionName])) {
                // Add the instruction's numeric code to the bytecode.
                $bytecode[] = self::$instructionMap[$instructionName];

                // If it's a LITERAL, add its value as the next item in the bytecode.
                if ($instructionName === 'LITERAL' && isset($instruction['value'])) {
                    $bytecode[] = (int)$instruction['value'];
                }
            }
        }

        return $bytecode;
    }
}