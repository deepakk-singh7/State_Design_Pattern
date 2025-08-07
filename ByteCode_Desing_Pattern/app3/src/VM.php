<?php 

require_once 'Instruction.php';
require_once 'Game.php';

class VM {
    private Game $game; 

    private const STACK_SIZE = 128;
    private array $stack = [];

    public function __construct(Game $game) {
        $this->game = $game;
    }

    // The interpret function 
    public function interpret(array $byteCode, string $casterName, string $spellName): void {
        echo "---\n {$casterName} cast {$spellName} spell -----\n" . PHP_EOL;
        $length = count($byteCode);
        for ($i = 0; $i < $length; $i++) {
            $instruction = $byteCode[$i];

            switch ($instruction) {
                // Data
                case Instruction::LITERAL:
                    if ($i + 1 >= $length) {
                        throw new \Exception("Invalid bytecode: LITERAL missing value");
                    }
                    $i++;
                    // Changed to use the push() method for consistency
                    $this->push($byteCode[$i]); 
                    break;
                case Instruction::GET_HEALTH:
                    $wizardId = $this->pop();
                    if ($wizardId < 0 || $wizardId >= 2) {
                        throw new \Exception("Invalid wizard ID: {$wizardId}");
                    }
                    $health = $this->game->getHealth($wizardId);
                    $this->push($health);
                    break;
                case Instruction::GET_WISDOM:
                    $this->push($this->game->getWisdom($this->pop()));
                    break;    
                case Instruction::GET_AGILITY:
                    $this->push($this->game->getAgility($this->pop()));
                    break;
                // Setters
                case Instruction::SET_HEALTH:
                    $wizardId = $this->pop();    // Pop wizard ID first
                    $health = $this->pop();      // Then pop health value
                    $this->game->setHealth($wizardId, $health);
                    break;
                case Instruction::SET_WISDOM:
                    $wizardId = $this->pop();
                    $wisdom = $this->pop();
                    $this->game->setWisdom($wizardId, $wisdom);
                    break;    
                case Instruction::SET_AGILITY:
                    $wizardId = $this->pop();
                    $agility = $this->pop();
                    $this->game->setAgility($wizardId, $agility);
                    break;    
                // Arithmetic
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
                    // Added a check for division by zero
                    if ($b === 0) {
                        throw new \Exception("Division by zero.");
                    }
                    $this->push((int)($a / $b));
                    break;            
            }
        }
    }

    // BUG FIX 2: Moved push() and pop() INSIDE the class definition

    private function push(int $data): void {
        if (self::STACK_SIZE === count($this->stack)) {
            throw new \Exception("Stack Overflow.");
        }
        // BUG FIX 3: Changed self::$stack to $this->stack
        $this->stack[] = $data;
    }

    private function pop(): int {
        if (count($this->stack) === 0) {
            throw new \Exception("Stack Underflow.");
        }
        // BUG FIX 3: Changed self::$stack to $this->stack
        return array_pop($this->stack);
    }
}