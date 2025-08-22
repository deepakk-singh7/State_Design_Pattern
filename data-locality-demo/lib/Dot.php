<?php
require_once __DIR__ . '/PhysicsComponent.php';

class Dot {
    public function __construct(
        public float $x,
        public float $y,
        public PhysicsComponent $physics
    ) {}

    /**
     * Updates the dot's position based on its velocity and world boundaries.
     */
    public function update(int $width, int $height): void {
        $this->x += $this->physics->vx;
        $this->y += $this->physics->vy;

        if ($this->x <= 0 || $this->x >= $width) {
            $this->physics->vx *= -1;
        }
        if ($this->y <= 0 || $this->y >= $height) {
            $this->physics->vy *= -1;
        }
    }
}