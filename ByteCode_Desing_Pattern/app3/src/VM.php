<?php 

/**
 * VM (Virtual Machine) for the Wizard Duel Game.
 *
 * This file defines the stack-based virtual machine responsible for interpreting
 * and executing spell bytecode. It interacts directly with the Game state.
 */

require_once 'Instruction.php';
require_once 'Game.php';

class VM {

    /**
     * A reference to the main game object.
     * @var Game
     */
    private Game $game; 

    // The maximum number of values that can be stored on the stack.
    private const STACK_SIZE = 128;

    /**
     * The internal stack for the VM's operations.
     * Values (literals, stats) are pushed onto and popped off this stack.
     *
     * @var int[]
     */
    private array $stack = [];


    /**
     * VM constructor.
     *
     * Injects the Game dependency, giving the VM access to the game state.
     *
     * @param Game $game The active game instance.
     */
    public function __construct(Game $game) {
        $this->game = $game;
    }

    /**
     * Interprets and executes a given array of bytecode.
     *
     * This is the main loop of the VM. It iterates through each instruction
     * and performs the corresponding action.
     *
     * @param int[]  $byteCode   The array of compiled instructions to execute.
     * @param string $casterName The name of the wizard casting the spell.
     * @param string $spellName  The name of the spell being cast.
     * @return void
     * @throws \Exception If the bytecode is invalid or a runtime error occurs (e.g., division by zero).
     */
    public function interpret(array $byteCode, string $casterName, string $spellName): void {
        echo "---\n {$casterName} cast {$spellName} spell -----\n" . PHP_EOL;
        $length = count($byteCode);
        for ($i = 0; $i < $length; $i++) {
            $instruction = $byteCode[$i];

            switch ($instruction) {
                // --- Data Retrieval Instructions ---
                case Instruction::LITERAL:
                    if ($i + 1 >= $length) {
                        throw new \Exception("Invalid bytecode: LITERAL missing value");
                    }
                    $i++;
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
                // --- State Modification Instructions ---
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
                // --- Arithmetic Instructions ---
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

    /**
     * Pushes a value onto the stack.
     *
     * @param int $data The integer value to push.
     * @return void
     * @throws \Exception If the stack is full (Stack Overflow).
     */
    private function push(int $data): void {
        if (self::STACK_SIZE === count($this->stack)) {
            throw new \Exception("Stack Overflow.");
        }
        $this->stack[] = $data;
    }

    /**
     * Pops a value from the stack.
     *
     * @return int The integer value from the top of the stack.
     * @throws \Exception If the stack is empty (Stack Underflow).
     */

    private function pop(): int {
        if (count($this->stack) === 0) {
            throw new \Exception("Stack Underflow.");
        }
        return array_pop($this->stack);
    }
}