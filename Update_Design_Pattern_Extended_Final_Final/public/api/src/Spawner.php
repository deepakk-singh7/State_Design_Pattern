<?php
/**
 * @file Contains the definition for the Spawner entity.
 */
require_once 'Entity.php';
require_once 'Minion.php';

/**
 * A factory-like entity that periodically creates new Minion entities.
 */
class Spawner extends Entity
{
    /**
     * A countdown timer that tracks time until the next spawn.
     * @var float
     */
    private float $timer;
    /**
     * The interval between spawns, in seconds.
     * @var float
     */
    private float $spawnRateInSeconds;

    /**
     * Spawner constructor.
     *
     * @param float $x The horizontal position.
     * @param float $y The vertical position.
     * @param float $spawnRateInSeconds The time interval between spawning minions.
     */
    public function __construct(float $x, float $y, float $spawnRateInSeconds = 4.0)
    {
        parent::__construct($x, $y);
        $this->spawnRateInSeconds = $spawnRateInSeconds;
        $this->timer = $this->spawnRateInSeconds;
    }

    /**
     * Counts down the timer and spawns a Minion when it reaches zero.
     *
     * @param World $world The world instance.
     * @param float $deltaTime The time elapsed since the last update.
     * @return void
     */
    public function update(World $world, float $deltaTime): void
    {
        // Count down the timer in seconds
        $this->timer -= $deltaTime;
        
        if ($this->timer <= 0) {
            // PRODUCE a new Minion at a random position near the spawner
            $minionX = $this->x + rand(-5, 5);
            $minionY = $this->y + rand(-5, 5);
            $world->spawnEntity(new Minion($minionX, $minionY));

            // Reset timer
            $this->timer = $this->spawnRateInSeconds; 
        }
    }
}