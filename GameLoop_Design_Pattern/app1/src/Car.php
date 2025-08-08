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

    // Renders the car at its last known, fully updated position [without interpolation].
    public function render(): void {
        echo "RENDER: Drawing car at last updated position: " . number_format($this->position, 2) . "\n";
    }
}