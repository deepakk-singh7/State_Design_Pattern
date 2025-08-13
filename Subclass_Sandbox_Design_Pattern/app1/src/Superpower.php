<?php 

require_once 'Hero.php';

abstract class Superpower {
    protected Hero $hero;

    public function __construct(Hero $hero) {
        $this->hero = $hero;
    }
    public function use(): void {
        $this->activate();
    }

    // 1. THE SANDBOX METHOD
    // This is abstract, forcing subclasses to implement their unique behavior here.
    abstract protected function activate(): void;


    // 2. PROVIDED OPERATIONS (Tools)

    protected function move(float $x, float $y, float $z): void {
        // The base class handles the logic of interacting with the hero's state.
        $this->hero->x += $x;
        $this->hero->y += $y;
        $this->hero->z += $z;
        echo "Hero moved by ($x, $y, $z). New position: Z = {$this->hero->z}\n";
    }

    protected function playSound(string $soundId, float $volume): void {
        echo "Playing sound '{$soundId}' at volume {$volume}.\n";
    }

    protected function spawnParticles(string $particleType, int $count): void {
        echo "Spawning {$count} particles of type '{$particleType}'.\n";
    }

    // 3. PROVIDED STATE GETTERS
    protected function getHeroZ(): float {
        return $this->hero->z;
    }
}