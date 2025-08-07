<?php 
require_once 'Game.php';
require_once 'Instruction.php';
// Our first, very simple Virtual Machine!
class VM
{
    private Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function interpret(array $bytecode): void
    {
        echo "--- Executing Spell ---\n";
        foreach ($bytecode as $instruction) {
            switch ($instruction) {
                case Instruction::SET_HEALTH:
                    $this->game->setHealth(0, 100);
                    break;
                case Instruction::SET_WISDOM:
                    $this->game->setWisdom(0, 100);
                    break;
                case Instruction::SET_AGILITY:
                    $this->game->setAgility(0, 100);
                    break;
                case Instruction::PLAY_SOUND:
                    $this->game->playSound(55); // SOUND_BANG
                    break;
                case Instruction::SPAWN_PARTICLES:
                    $this->game->spawnParticles(10); // PARTICLE_FLAME
                    break;
                default:
                    echo "Unknown instruction: {$instruction}\n";
                    break;
            }
        }
        echo "--- Spell Finished ---\n";
    }
}