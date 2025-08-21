<?php

/**
 * Health Pack - increases player health
 */
class HealthPackComponent extends Component implements Collectable
{
    private int $healAmount;
    
    public function __construct(int $healAmount = 25)
    {
        $this->healAmount = $healAmount;
    }
    
    public function onCollision(GameObject $collector, GameObject $collected): void
    {
        // Heal the collector
        $healthComponent = $collector->getComponent(HealthComponent::class);
        if ($healthComponent instanceof HealthComponent) {
            $healthComponent->heal($collector, $this->healAmount);
        }
        
        // Respawn the health pack at a random location
        $this->respawnCollected($collected);
    }
    
    public function canBeCollectedBy(GameObject $collector): bool
    {
        // Only players can collect health packs, and only if they're not at full health
        return $collector->id === EntityType::Player->value && $collector->health < 100;
    }
    
    private function respawnCollected(GameObject $collected): void
    {
        $collected->x = rand(20, 360);
        $collected->y = rand(20, 360);
    }
}