<?php
/**
 * The World class definition.
 */
require_once 'Entity.php';

class World
{
    private array $entities = [];
    private array $newEntities = []; 
    private array $killedEntities = []; 

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function addEntity(Entity $entity): void
    {
        $this->entities[] = $entity;
    }

    public function spawnEntity(Entity $entity): void
    {
        $this->newEntities[] = $entity;
    }

    public function killEntity(Entity $entity): void
    {
        if (!in_array($entity, $this->killedEntities, true)) {
            $this->killedEntities[] = $entity;
        }
    }

    /**
     * Updates the world state using a more stable order of operations.
     */
    public function tick(float $deltaTime): void
    {
        // --- THIS ORDER IS CRITICAL ---

        // 1. Add entities that were spawned in the PREVIOUS frame.
        $this->applyAdditions();

        // 2. Remove entities that were killed in the PREVIOUS frame.
        $this->applyRemovals();
        
        // 3. Update all entities that are currently alive.
        //    This is where new entities might be added to the spawn/kill lists for the NEXT frame.
        foreach ($this->entities as $entity) {
            $entity->update($this, $deltaTime);
        }
                
        // 4. Check for collisions that occurred during this frame's update.
        //    The results (killed entities) will be processed in the next frame's applyRemovals().
        $this->handleCollisions();
    }

    private function handleCollisions(): void
    {
        // This logic remains the same.
        $bolts = array_filter($this->entities, fn($e) => $e instanceof LightningBolt);
        $targets = array_filter($this->entities, fn($e) => !($e instanceof LightningBolt || $e instanceof Statue));

        foreach ($bolts as $bolt) {
            foreach ($targets as $target) {
                // Using a smaller radius for more precise collision
                $distSq = pow($bolt->getX() - $target->getX(), 2) + pow($bolt->getY() - $target->getY(), 2);
                if ($distSq < 9) { // A radius of 3 (3*3=9)
                    $this->killEntity($bolt);
                    $this->killEntity($target);
                }
            }
        }
    }
    
    private function applyRemovals(): void
    {
        if (empty($this->killedEntities)) return;
        $this->entities = array_udiff($this->entities, $this->killedEntities, 
            fn($a, $b) => spl_object_id($a) - spl_object_id($b)
        );
        $this->killedEntities = [];
    }

    private function applyAdditions(): void
    {
        if (empty($this->newEntities)) return;
        $this->entities = array_merge($this->entities, $this->newEntities);
        $this->newEntities = [];
    }
}