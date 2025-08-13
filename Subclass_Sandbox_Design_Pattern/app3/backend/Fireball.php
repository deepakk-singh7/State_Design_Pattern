<?php
require_once __DIR__ . "/Superpower.php";

class Fireball extends Superpower {

    public function __construct(Hero $hero, int $playerNumber = 1) {
        parent::__construct(hero: $hero);
    }
    /**
     * Subclass sandbox implementation.
     * Returns a sequence of logs describing effects for the UI.
     * @return array
     */
    protected function activate(): array {
        $log = [];
        $log[] = "Fireball activated!";
        $log[] = $this->playSound("fire_cast.wav"); // Sound via base toy
        $log[] = $this->spawnParticles("fire_sparks"); // Particles via base toy
        $log[] = $this->move(5, 0, 0); // Controlled state mutation
        return $log;
    }
}
