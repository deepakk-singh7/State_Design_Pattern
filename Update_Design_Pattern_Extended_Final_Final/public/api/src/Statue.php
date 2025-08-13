<?php
/**
 * @file Contains the definition for the Statue entity.
 */
require_once 'Entity.php';
require_once 'LightningBolt.php';

/**
 * A stationary turret-like entity that periodically shoots LightningBolt projectiles.
 */
class Statue extends Entity
{
    /**
     * The fixed time interval between shooting projectiles, in seconds.
     * @var float
     */
    private float $shootCooldown = 5.0; 
    /**
     * A countdown timer that tracks the time until the next shot.
     * Initialized at declaration to ensure it has a value after unserialization.
     * @var float
     */
    private float $shootTimer = 2.0;

    /**
     * Statue constructor.
     *
     * @param float $x The horizontal position.
     * @param float $y The vertical position.
     */
    public function __construct(float $x = 0, float $y = 0)
    {
        parent::__construct($x, $y);
    }

    /**
     * Counts down the shoot timer and fires a projectile when ready.
     *
     * @param World $world The world instance.
     * @param float $deltaTime The time elapsed since the last update.
     * @return void
     */
    public function update(World $world, float $deltaTime): void
    {
        // Decrease the shoot timer by the time elapsed this frame.
        $this->shootTimer -= $deltaTime;

        // If the cooldown period has passed...
        if ($this->shootTimer <= 0) {
            // // ...create a new LightningBolt at the statue's position.
            $bolt = new LightningBolt($this->x, $this->y);
            $world->spawnEntity($bolt);
            
            // ...and reset the timer for the next shot.
            $this->shootTimer = $this->shootCooldown; 
        }
    }
}