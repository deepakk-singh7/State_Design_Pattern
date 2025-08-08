<?php
require_once 'Entity.php';

class LightningBolt extends Entity
{
    // Properties are now time-based
    private float $speed = 75.0; // Moves 75 units per second
    private float $lifeInSeconds = 1.5; // Self-destructs after 1.5 seconds

    public function update(World $world, float $deltaTime): void
    {
        // New position = old position + (speed * time)
        $this->y += $this->speed * $deltaTime;

        // Decrease lifetime by the time elapsed
        $this->lifeInSeconds -= $deltaTime;
        
        if ($this->lifeInSeconds <= 0 || $this->y > 100) {
            // KILL itself when it goes off-screen or times out
            $world->killEntity($this);
        }
    }
}