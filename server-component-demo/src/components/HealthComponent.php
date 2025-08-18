<?php
/**
 * A behavior component that encapsulates all health-related logic.
 */
class HealthComponent extends Component
{
    /**
     * Modifies the GameObject's health by a given amount.
     * @param GameObject $obj The game object to heal.
     * @param int $amount The amount of health to add.
     */
    public function heal(GameObject $obj, int $amount)
    {
        // Increase the object's health, but cap it at a maximum of 100.
        $obj->health = min(100, $obj->health + $amount);
    }
}