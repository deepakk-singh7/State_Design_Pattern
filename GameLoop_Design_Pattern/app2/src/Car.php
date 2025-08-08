<?php

// A simple helper function to get the current time in milliseconds.
function getCurrentTimeMillis(): float {
    return microtime(true) * 1000;
}

// Represents car object.
class Car {
    private float $position = 0.0; // Car's position in pixels
    private float $speed = 200.0;  // Pixels per second

    // Updates the car's position based on a fixed time step.
    public function update(float $timeStep): void {
        $distanceMoved = $this->speed * $timeStep;
        $this->position += $distanceMoved;
        echo "    UPDATE: Car moved " . number_format($distanceMoved, 2) . " pixels. New position: " . number_format($this->position, 2) . "\n";
    }

    /**
     * Renders the car at an interpolated position for smooth visuals.
     * @param float $interpolation A value between 0.0 and 1.0 representing how far we are into the next frame.
     * @param float $timeStep The fixed time step (in seconds) used for calculating velocity for one step.
     */
    public function render(float $interpolation, float $timeStep): void {
        // Calculate the "in-between" position based on the last update and the car's velocity.
        $interpolatedPosition = $this->position + ($this->speed * $timeStep * $interpolation);
        echo "RENDER: Drawing car at interpolated position: " . number_format($interpolatedPosition, 2) . " (Interpolation: " . number_format($interpolation, 2) . ")\n";
    }
}