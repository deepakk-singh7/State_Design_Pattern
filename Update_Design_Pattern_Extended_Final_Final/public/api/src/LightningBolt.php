<?php
/**
 * @file Contains the definition for the LightningBolt entity.
 */
require_once 'Entity.php';

/**
 * A projectile entity that travels in a straight line and expires after a set time.
 */
class LightningBolt extends Entity
{
    /**
     * The speed at which the bolt travels, in world units per second.
     * @var float
     */
    private float $speed = 75.0; 
    /**
     * The maximum duration the bolt can exist, in seconds, before self-destructing.
     * @var float
     */
    private float $lifeInSeconds = 1.5; 

    /**
     * Updates the LightningBolt's position and checks its lifetime.
     *
     * @param World $world The world instance.
     * @param float $deltaTime The time elapsed since the last update.
     * @return  void
     */
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