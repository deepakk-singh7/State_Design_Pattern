<?php
/**
 * PhysicsComponent
 * Handles all physics-related logic like position, velocity, and collision.
 */
class PhysicsComponent implements Component
{
    public function update(GameObject $gameObject)
    {
        // Apply gravity
        $gameObject->velocity['y'] += 1;
        // Update position based on velocity
        $gameObject->position['y'] += $gameObject->velocity['y'];

        echo "  - Physics: Position is now y = {$gameObject->position['y']}\n";
    }
}