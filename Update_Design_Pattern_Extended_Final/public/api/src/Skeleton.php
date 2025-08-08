<?php
require_once 'Entity.php';

class Skeleton extends Entity
{
    private bool $patrollingLeft = false;
    private float $speed = 11.0; // Patrols at 25 units per second

    public function update(World $world, float $deltaTime): void
    {
        $movement = $this->speed * $deltaTime;

        if ($this->patrollingLeft) {
            $this->x -= $movement;
            if ($this->x <= 0) {
                $this->x = 0; // Prevent going out of bounds
                $this->patrollingLeft = false;
            }
        } else {
            $this->x += $movement;
            if ($this->x >= 100) {
                $this->x = 100; // Prevent going out of bounds
                $this->patrollingLeft = true;
            }
        }
    }
}