<?php

/**
 * GraphicsComponent
 * Handles all rendering and animation logic.
 */
class GraphicsComponent implements Component
{
    public function update(GameObject $gameObject)
    {
        // Determine which sprite to draw based on state
        $sprite = 'standing.png';
        if ($gameObject->velocity['y'] !== 0) {
            $sprite = 'in_air.png';
        }
        echo "  - Graphics: Drawing sprite '{$sprite}' at position y = {$gameObject->position['y']}\n";
    }
}