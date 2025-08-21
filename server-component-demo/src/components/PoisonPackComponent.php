<?php 

/**
 * Poison Pack - decreases player health
 */
class PoisonPackComponent extends Component implements Collectable
{
    private int $damageAmount;
    
    public function __construct(int $damageAmount = 20)
    {
        $this->damageAmount = $damageAmount;
    }
    
    public function onCollision(GameObject $collector, GameObject $collected): void
    {
        // Damage the collector
        $healthComponent = $collector->getComponent(HealthComponent::class);
        if ($healthComponent instanceof HealthComponent) {
            $healthComponent->damage($collector, $this->damageAmount);
        }
        
        // Respawn the poison pack
        $this->respawnCollected($collected);
    }
    
    public function canBeCollectedBy(GameObject $collector): bool
    {
        // Only players can collect poison packs
        return $collector->id === EntityType::Player->value;
    }
    
    private function respawnCollected(GameObject $collected): void
    {
        $collected->x = rand(20, 360);
        $collected->y = rand(20, 360);
    }
}