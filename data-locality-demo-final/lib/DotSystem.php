<?php

require_once __DIR__ . '/PhysicsComponent.php';
class DotSystem {
    
    /**
     * Initialize dot data arrays
     */
    public static function initialize(int $count, int $width, int $height): array {
        $data = [
            'x' => [],
            'y' => [],
            'vx' => [],
            'vy' => [],
            'count' => $count
        ];
        
        for ($i = 0; $i < $count; $i++) {
            $data['x'][$i] = rand(0, $width);
            $data['y'][$i] = rand(0, $height);
            $data['vx'][$i] = PhysicsComponent::generateRandomVelocity();
            $data['vy'][$i] = PhysicsComponent::generateRandomVelocity();
        }
        
        return $data; // associative array.. 
    }
    
    /**
     * Update all dots : All operations work on pure data arrays
     */
    public static function update(array &$dotData, int $width, int $height): void {
        $count = $dotData['count'];
        
        // Step 1: Update positions based on velocities
        PhysicsComponent::updatePositions(
            $dotData['x'], 
            $dotData['y'], 
            $dotData['vx'], 
            $dotData['vy'], 
            $count
        );
        
        // Step 2: Handle boundary collisions
        PhysicsComponent::handleBoundaryCollisions(
            $dotData['x'], 
            $dotData['y'], 
            $dotData['vx'], 
            $dotData['vy'], 
            $width, 
            $height, 
            $count
        );
    }
    
    /**
     * Get positions for rendering (returns only what's needed)
     */
    public static function getPositions(array $dotData): array {
        $positions = [];
        for ($i = 0; $i < $dotData['count']; $i++) {
            $positions[] = [
                'x' => $dotData['x'][$i],
                'y' => $dotData['y'][$i],
            ];
        }
        return $positions;
    }
    
}