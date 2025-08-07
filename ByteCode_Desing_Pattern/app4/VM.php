<?php
require_once 'Instruction.php';
require_once 'Game.php';

class VM {
    private Game $game;
    private array $stack = [];
    private const STACK_SIZE = 128;

    public function __construct(Game $game) {
        $this->game = $game;
    }

    public function interpret(array $byteCode, int $casterId, string $spellName): void {
        $casterName = $this->game->getName($casterId);
        $this->game->addToLog("{$casterName} casts {$spellName}!");
        
        $this->stack = []; // Clear stack for new interpretation

        $count = count($byteCode);
        for ($i = 0; $i < $count; $i++) {
            $instruction = $byteCode[$i];

            switch ($instruction) {
                // --- Data ---
                case Instruction::LITERAL:
                    $i++; // Move to the next byte for the value
                    if ($i < $count) {
                        $this->push($byteCode[$i]);
                    }
                    break;

                // --- Getters ---
                case Instruction::GET_HEALTH:
                    $this->push($this->game->getHealth($this->pop()));
                    break;
                case Instruction::GET_WISDOM:
                    $this->push($this->game->getWisdom($this->pop()));
                    break;    
                case Instruction::GET_AGILITY:
                    $this->push($this->game->getAgility($this->pop()));
                    break;

                // --- Setters ---
                // For setters, the stack order is [value, wizardId]
                // We pop wizardId first, then value.
                case Instruction::SET_HEALTH:
                    $wizardId = $this->pop();
                    $value = $this->pop();
                    $oldValue = $this->game->getHealth($wizardId);
                    $this->game->setHealth($wizardId, $value);
                    $targetName = $this->game->getName($wizardId);
                    $diff = abs($oldValue - $value);
                    if ($value < $oldValue) $this->game->addToLog("{$targetName} takes {$diff} damage.");
                    else if ($value > $oldValue) $this->game->addToLog("{$targetName} heals for {$diff}.");
                    break;
                case Instruction::SET_WISDOM:
                    $wizardId = $this->pop();
                    $value = $this->pop();
                    $this->game->setWisdom($wizardId, $value);
                    break;    
                case Instruction::SET_AGILITY:
                    $wizardId = $this->pop();
                    $value = $this->pop();
                    $this->game->setAgility($wizardId, $value);
                    break;    

                // --- Arithmetic ---
                // For arithmetic, the stack order is [operand_A, operand_B]
                // We pop operand_B first, then operand_A.
                case Instruction::ADD:
                    $b = $this->pop();
                    $a = $this->pop();
                    $this->push($a + $b);
                    break;
                case Instruction::SUBTRACT:
                    $b = $this->pop();
                    $a = $this->pop();
                    $this->push($a - $b);
                    break;
                case Instruction::MULTIPLY:
                    $b = $this->pop();
                    $a = $this->pop();
                    $this->push($a * $b);
                    break;
                case Instruction::DIVIDE:
                    $b = $this->pop();
                    $a = $this->pop();
                    if ($b === 0) {
                        $this->game->addToLog("A spell fizzled due to a magical anomaly!");
                        $this->push(0); // Push a default value to prevent further stack errors
                    } else {
                        $this->push((int)($a / $b));
                    }
                    break;            
            }
        }
    }

    private function push(int $data): void {
        if (count($this->stack) >= self::STACK_SIZE) {
            throw new \Exception("Stack Overflow.");
        }
        $this->stack[] = $data;
    }

    private function pop(): int {
        if (empty($this->stack)) {
            // This is the error you were seeing.
            throw new \Exception("Stack Underflow.");
        }
        return array_pop($this->stack);
    }
}
// ```

// ### Why This Fixes the Problem

// The original code had some arithmetic operations like `$this->pop() + $this->pop()`. The order in which PHP executes these two `pop()` calls isn't guaranteed, which can corrupt the stack.

// The corrected code ensures that for every operation, values are popped off the stack one by one, in the correct LIFO order, and stored in variables before any calculation happens. This makes the VM stable and predictable.

// After replacing `VM.php` with this new code, your game should run without any erro