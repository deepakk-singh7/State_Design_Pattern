<?php

/**
 * Data-Oriented PhysicsComponent
 */
class PhysicsComponent {

    /**
     * Constructor to demo the OOPs method
     */
    public function __construct(
        public float $vx, 
        public float $vy,
    ){}
    
    /**
     * Update positions based on velocities (operates on arrays)
     */
    public static function updatePositions(array &$x, array &$y, array $vx, array $vy, int $count): void {
        for ($i = 0; $i < $count; $i++) {
            $x[$i] += $vx[$i];
            $y[$i] += $vy[$i];
        }
    }
    
    /**
     * Handle boundary collisions (operates on arrays)
     */
    public static function handleBoundaryCollisions(array $x, array $y, array &$vx, array &$vy, 
                                                  int $width, int $height, int $count): void {
        for ($i = 0; $i < $count; $i++) {
            // if out of boundary, then reverse the direction.. 
            if ($x[$i] <= 0 || $x[$i] >= $width) {
                $vx[$i] *= -1;
            }
            if ($y[$i] <= 0 || $y[$i] >= $height) {
                $vy[$i] *= -1;
            }
        }
    }
    
    /**
     * Generate random velocity 
     */
    public static function generateRandomVelocity(): float {
        return (rand(0, 100) / 50) - 1;
    }
    
    /**
     * Apply damping/friction to all velocities [ other physics functions just for example]
     */
    public static function applyDamping(array &$vx, array &$vy, float $dampingFactor, int $count): void {
        for ($i = 0; $i < $count; $i++) {
            $vx[$i] *= $dampingFactor;
            $vy[$i] *= $dampingFactor;
        }
    }
}