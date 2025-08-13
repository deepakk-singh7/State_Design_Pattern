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
    private float $speed = 35.0;
    
    /**
     * The maximum duration the bolt can exist, in seconds, before self-destructing.
     * @var float
     */
    private float $lifeInSeconds = 2.5;

    /**
     * Constructor - set initial velocity
     */
    public function __construct(float $x = 0, float $y = 0)
    {
        parent::__construct($x, $y);
        
        // Set velocity immediately when created
        $this->vx = 0;
        $this->vy = $this->speed;
    }

    /**
     * Updates the LightningBolt's position and checks its lifetime.
     *
     * @param World $world The world instance.
     * @param float $deltaTime The time elapsed since the last update.
     * @return void
     */
    public function update(World $world, float $deltaTime): void
    {
        // Move the bolt
        $this->y += $this->speed * $deltaTime;
        
        // Set velocity for client extrapolation (constant movement)
        $this->vx = 0;
        $this->vy = $this->speed;

        // Decrease lifetime by the time elapsed
        $this->lifeInSeconds -= $deltaTime;

        if ($this->lifeInSeconds <= 0 || $this->y > 100) {
            // Kill itself when it goes off-screen or times out
            $world->killEntity($this);
        }
    }
}