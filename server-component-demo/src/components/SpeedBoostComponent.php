<?php

class SpeedBoostComponent extends Component implements Collectable
{
    public function onCollision(GameObject $collector, GameObject $collected): void
    {
        // Set collector health to maximum (100)
        $collector->health = 100;
        
        // Respawn the speed boost pack
        $collected->x = rand(20, 360);
        $collected->y = rand(20, 360);
    }
    
    public function canBeCollectedBy(GameObject $collector): bool
    {
        // Only players can collect, and only if health is not already max
        return $collector->id === EntityType::Player->value && $collector->health < 100;
    }
}