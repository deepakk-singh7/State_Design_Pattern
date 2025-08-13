<?php
require_once __DIR__ . "/Hero.php";
require_once __DIR__ . "/systems/AudioSystem.php";
require_once __DIR__ . "/systems/ParticleSystem.php";

/**
 * Subclass Sandbox base class.
 */
abstract class Superpower {
    /** @var Hero Shared reference to hero state (mutated via move()). */
    protected Hero $hero;

    /**
     * Base constructor injects state dependency (Hero).
     * @param Hero $hero Mutable game state (position/height).
     */
    public function __construct(Hero $hero) {
        $this->hero = $hero;
    }

    /**
     * Public entry point from outside world (controller/API).
     * Calls the sandbox method and returns log lines to frontend.
     * @return array<string> Ordered log output describing effects and state changes.
     */
    public function use(): array {
        return $this->activate();
    }

    /**
     * SANDBOX METHOD: subclasses implement their unique behavior here.
     * Must use only provided protected operations (the "toys").
     * @return array<string>
     */
    abstract protected function activate(): array;

    /**
     * Protected toy: route sound playback through base class.
     * Encapsulates AudioSystem.
     */
    protected function playSound(string $soundId): string {
        return AudioSystem::playSound($soundId);
    }

    /**
     * Protected toy: route particle spawning through base class.
     * Encapsulates ParticleSystem.
     */
    protected function spawnParticles(string $particleType): string {
        return ParticleSystem::spawn($particleType);
    }

    /**
     * Protected toy: controlled mutation of Hero position.
     * Centralizes rules for movement; subclasses cannot change Hero directly.
     * @param float $x
     *  @param float $y
     *  @param float $z
     * @return string
     */
    protected function move(float $x, float $y, float $z): string {
        $this->hero->x += $x;
        $this->hero->y += $y;
        $this->hero->z += $z;
        return "Hero moves to position ({$this->hero->x}, {$this->hero->y}, {$this->hero->z})";
    }

    /**
     * Protected getter: safe read-only access to state for branching logic.
     * @return float
     */
    protected function getHeroZ(): float {
        return $this->hero->z;
    }
}
