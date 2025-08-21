<?php

/**
 * A behavior component that encapsulates all health-related logic.
 */
class HealthComponent extends Component
{
    /**
     * Modifies the GameObject's health by a given amount (healing).
     * @param GameObject $obj The game object to heal.
     * @param int $amount The amount of health to add.
     */
    public function heal(GameObject $obj, int $amount): void
    {
        // Increase the object's health, but cap it at a maximum of 100.
        $obj->health = min(100, $obj->health + $amount);
    }
    
    /**
     * Reduces the GameObject's health by a given amount (damage).
     * @param GameObject $obj The game object to damage.
     * @param int $amount The amount of health to subtract.
     */
    public function damage(GameObject $obj, int $amount): void
    {
        // Decrease the object's health, but don't let it go below 0.
        $obj->health = max(0, $obj->health - $amount);
    }
}