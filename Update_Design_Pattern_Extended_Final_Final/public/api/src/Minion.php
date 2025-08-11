<?php
/**
 * @file Contains the definition for the Minion entity.
 */
require_once 'Entity.php';

/**
 * A simple enemy entity that wanders around the world randomly.
 */
class Minion extends Entity
{

    /**
     * The speed at which the minion wanders, in world units per second.
     * @var float
     */
    private float $wanderSpeed = 15.0; 

    /**
     * Updates the Minion's position with random movement.
     *
     * @param World $world The world instance.
     * @param float $deltaTime The time elapsed since the last update.
     * @return void
     */
    public function update(World $world, float $deltaTime): void
    {
        // Apply random movement scaled by speed and delta time
        $this->x += rand(-1, 1) * $this->wanderSpeed * $deltaTime;
        $this->y += rand(-1, 1) * $this->wanderSpeed * $deltaTime;

        // Clamp position to world bounds (0-100)
        $this->x = max(0, min(100, $this->x));
        $this->y = max(0, min(100, $this->y));
    }
}