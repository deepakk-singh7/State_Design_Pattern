<?php
require_once 'Entity.php';
require_once 'Minion.php';

class Spawner extends Entity
{
    private float $timer;
    private float $spawnRateInSeconds;

    // The constructor now expects the spawn rate in seconds
    public function __construct(float $x, float $y, float $spawnRateInSeconds = 4.0)
    {
        parent::__construct($x, $y);
        $this->spawnRateInSeconds = $spawnRateInSeconds;
        $this->timer = $this->spawnRateInSeconds;
    }

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