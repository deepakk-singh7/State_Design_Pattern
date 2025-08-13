<?php
/**
 * @file Contains the definition for the Skeleton entity.
 */
require_once 'Entity.php';

/**
 * An enemy entity that patrols back and forth horizontally across the world.
 */
class Skeleton extends Entity
{
    /**
     * A state flag that determines the current patrol direction.
     * @var bool
     */
    private bool $patrollingLeft = false;
    
    /**
     * The horizontal speed of the skeleton, in world units per second.
     * @var float
     */
    private float $speed = 15.0; // @10, it will collide with the lightningBolt [@35]

    /**
     * The width of the entity in the world's 0-100 unit system.
     * Based on CSS: 20px / 600px = 0.0333... * 100 = 3.333...
     */
    private const ENTITY_WIDTH_PERCENT = 3.3333333333333335;
    
    /**
     * The maximum allowed position for the skeleton's LEFT edge.
     */
    private const MAX_X_POSITION = 100 - self::ENTITY_WIDTH_PERCENT;
    // ----------------------------------------------------

    /**
     * Updates the Skeleton's position based on its patrol state.
     *
     * @param World $world The world instance.
     * @param float $deltaTime The time elapsed since the last update.
     * @return void
     */
    public function update(World $world, float $deltaTime): void
    {
        $movement = $this->speed * $deltaTime;

        if ($this->patrollingLeft) {
            $this->x -= $movement;
            $this->vx = -$this->speed; // Set velocity for client extrapolation
            
            if ($this->x <= 0) {
                $this->x = 0; // Prevent going out of bounds
                $this->patrollingLeft = false;
                $this->vx = $this->speed; // Update velocity immediately when direction changes
            }
        } else {
            $this->x += $movement;
            $this->vx = $this->speed; // Set velocity for client extrapolation
            
            if ($this->x >= self::MAX_X_POSITION) {
                $this->x = self::MAX_X_POSITION; // Prevent going out of bounds
                $this->patrollingLeft = true;
                $this->vx = -$this->speed; // Update velocity immediately when direction changes
            }
        }

        $this->vy = 0; // It doesn't move vertically
    }
}
