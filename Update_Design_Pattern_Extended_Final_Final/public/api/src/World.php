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

    // Public getter to retrieve the current list of entities.
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

    public function tick(float $deltaTime): void // Note: This no longer returns data
    {
        foreach ($this->entities as $entity) {
            $entity->update($this, $deltaTime);
        }
                
        $this->handleCollisions();
        $this->applyRemovals();
        $this->applyAdditions();
        // The responsibility of getting state is moved outside the tick
    }

    private function handleCollisions(): void
    {
        $bolts = array_filter($this->entities, fn($e) => $e instanceof LightningBolt);
        $targets = array_filter($this->entities, fn($e) => !($e instanceof LightningBolt || $e instanceof Statue));

        foreach ($bolts as $bolt) {
            foreach ($targets as $target) {
                $distSq = pow($bolt->getX() - $target->getX(), 2) + pow($bolt->getY() - $target->getY(), 2);
                if ($distSq < 25) { 
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