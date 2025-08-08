<?php
require_once 'Entity.php';
require_once 'LightningBolt.php';

class Statue extends Entity
{
    private float $shootCooldown = 2.0; // Shoots every 5 seconds

    // Initialize the property at declaration to solve the unserialize() issue.
    // This ensures it always has a value.
    private float $shootTimer = 2.0;

    // The constructor is now simpler as it doesn't need to set the timer.
    public function __construct(float $x = 0, float $y = 0)
    {
        parent::__construct($x, $y);
    }

    public function update(World $world, float $deltaTime): void
    {
        // This line will no longer cause an error.
        $this->shootTimer -= $deltaTime;

        if ($this->shootTimer <= 0) {
            // PRODUCE a new entity
            $bolt = new LightningBolt($this->x, $this->y);
            $world->spawnEntity($bolt);
            
            // Reset timer for the next shot
            $this->shootTimer = $this->shootCooldown; 
        }
    }
}