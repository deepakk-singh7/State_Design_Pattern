<?php
require_once 'Entity.php';

class Minion extends Entity
{
    private float $wanderSpeed = 15.0; // Wanders at a rate of 15 units per second

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