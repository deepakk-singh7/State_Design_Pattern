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
        
        $this->stack = []; // clear stack for new interpretation

        // replace target placeholders with actual wizard IDs
        $processedBytecode = $this->processTargetPlaceholders($byteCode, $casterId);

        $count = count($processedBytecode);
        for ($i = 0; $i < $count; $i++) {
            $instruction = $processedBytecode[$i];

            switch ($instruction) {
                // --- Data ---
                case Instruction::LITERAL:
                    $i++; // Move to the next byte for the value
                    if ($i < $count) {
                        $this->push($processedBytecode[$i]);
                    }
                    break;

                // --- Getters ---
                case Instruction::GET_HEALTH:
                    $wizardId = $this->pop();
                    $health = $this->game->getHealth($wizardId);
                    $this->push($health);
                    break;
                case Instruction::GET_WISDOM:
                    $wizardId = $this->pop();
                    $wisdom = $this->game->getWisdom($wizardId);
                    $this->push($wisdom);
                    break;    
                case Instruction::GET_AGILITY:
                    $wizardId = $this->pop();
                    $agility = $this->game->getAgility($wizardId);
                    $this->push($agility);
                    break;

                // --- Setters ---
                case Instruction::SET_HEALTH:
                    $wizardId = $this->pop();
                    $value = $this->pop();
                    $oldValue = $this->game->getHealth($wizardId);
                    $this->game->setHealth($wizardId, $value);
                    $targetName = $this->game->getName($wizardId);
                    $diff = abs($oldValue - $value);
                    if ($value < $oldValue) {
                        $this->game->addToLog("{$targetName} takes {$diff} damage.");
                    } else if ($value > $oldValue) {
                        $this->game->addToLog("{$targetName} heals for {$diff}.");
                    }
                    break;
                case Instruction::SET_WISDOM:
                    $wizardId = $this->pop();
                    $value = $this->pop();
                    $oldValue = $this->game->getWisdom($wizardId);
                    $this->game->setWisdom($wizardId, $value);
                    $targetName = $this->game->getName($wizardId);
                    $diff = $value - $oldValue;
                    if ($diff > 0) {
                        $this->game->addToLog("{$targetName} gains {$diff} wisdom.");
                    } else if ($diff < 0) {
                        $this->game->addToLog("{$targetName} loses {abs($diff)} wisdom.");
                    }
                    break;    
                case Instruction::SET_AGILITY:
                    $wizardId = $this->pop();
                    $value = $this->pop();
                    $oldValue = $this->game->getAgility($wizardId);
                    $this->game->setAgility($wizardId, $value);
                    $targetName = $this->game->getName($wizardId);
                    $diff = $value - $oldValue;
                    if ($diff > 0) {
                        $this->game->addToLog("{$targetName} gains {$diff} agility.");
                    } else if ($diff < 0) {
                        $this->game->addToLog("{$targetName} loses {abs($diff)} agility.");
                    }
                    break;    

                // --- Arithmetic ---
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
                        $this->push(0);
                    } else {
                        $this->push((int)($a / $b));
                    }
                    break;            
            }
        }
    }

    /**
     * Replaces target placeholders with actual wizard IDs
     * -1 = self (caster), -2 = opponent
     */
    private function processTargetPlaceholders(array $byteCode, int $casterId): array {
        $processed = [];
        $opponentId = ($casterId === 0) ? 1 : 0;

        for ($i = 0; $i < count($byteCode); $i++) {
            $instruction = $byteCode[$i];
            $processed[] = $instruction;

            // If this is a LITERAL instruction, check the next value for placeholders
            if ($instruction === Instruction::LITERAL && $i + 1 < count($byteCode)) {
                $i++; // Move to the value
                $value = $byteCode[$i];
                
                if ($value === -1) {
                    // Replace -1 with caster ID
                    $processed[] = $casterId;
                } elseif ($value === -2) {
                    // Replace -2 with opponent ID
                    $processed[] = $opponentId;
                } else {
                    // Keep original value
                    $processed[] = $value;
                }
            }
        }

        return $processed;
    }

    private function push(int $data): void {
        if (count($this->stack) >= self::STACK_SIZE) {
            throw new \Exception("Stack Overflow.");
        }
        $this->stack[] = $data;
    }

    private function pop(): int {
        if (empty($this->stack)) {
            throw new \Exception("Stack Underflow.");
        }
        return array_pop($this->stack);
    }
}