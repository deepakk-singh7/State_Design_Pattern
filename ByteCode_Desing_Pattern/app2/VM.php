<?php 

require_once 'Game.php';
require_once 'Instruction.php';

class VM
{
    private const MAX_STACK = 128;
    private array $stack = [];
    private Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function interpret(array $bytecode): void
    {
        echo "--- Executing Spell ---\n";
        for ($i = 0; $i < count($bytecode); $i++) {
            $instruction = $bytecode[$i];
            switch ($instruction) {
                case Instruction::SET_HEALTH:
                {
                    $amount = $this->pop();
                    $wizard = $this->pop();
                    $this->game->setHealth($wizard, $amount);
                    break;
                }

                case Instruction::LITERAL:
                {
                    $i++;
                    $value = $bytecode[$i];
                    $this->push($value);
                    break;
                }
                default:
                    echo "Unknown instruction: {$instruction}\n";
                    break;
            }
        }
        echo "--- Spell Finished ---\n";
    }

    private function push(int $value): void
    {
        if (count($this->stack) >= self::MAX_STACK) {
            throw new \Exception("Stack overflow!");
        }
        array_push($this->stack, $value);
    }

    private function pop(): int
    {
        if (empty($this->stack)) {
            throw new \Exception("Stack underflow!");
        }
        return array_pop($this->stack);
    }
}