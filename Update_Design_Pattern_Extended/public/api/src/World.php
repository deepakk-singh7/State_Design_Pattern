<?php
/**
 * The World class definition.
 *
 * This file contains the World class, which is responsible for managing all
 * game entities, processing game ticks, and handling interactions like collisions.
 */

require_once 'Entity.php';

class World
{
    /**
     * The list of all active entities in the game world.
     * @var Entity[]
     */
    private array $entities = [];
    /**
     * A temporary buffer for new entities to be added at the end of a tick.
     * @var Entity[]
     */
    private array $newEntities = []; 
    /**
     * A temporary buffer for entities to be removed at the end of a tick.
     * @var Entity[]
     */
    private array $killedEntities = []; 

    /**
     * Adds an entity directly to the world.
     * @param Entity $entity The entity instance to add.
     * @return void
     */
    public function addEntity(Entity $entity): void
    {
        $this->entities[] = $entity;
    }

    /**
     * Queues a new entity to be spawned into the world. The entity will be added at the end of the current game tick.
     * @param Entity $entity The entity instance to spawn.
     * @return void
     */
    public function spawnEntity(Entity $entity): void
    {
        $this->newEntities[] = $entity;
    }

    /**
     * Queues an entity to be removed from the world. The entity will be removed at the end of the current game tick.
     * @param Entity $entity The entity instance to remove.
     * @return void
     */
    public function killEntity(Entity $entity): void
    {
        // Avoid adding the same entity to the kill list multiple times
        if (!in_array($entity, $this->killedEntities, true)) {
            $this->killedEntities[] = $entity;
        }
    }

    /**
     * Processes a single frame/tick of the game loop.
     * @return array The state of all entities, ready for JSON encoding.
     */
    public function tick(): array
    {
        // 1. Update each entity. They can request spawns/kills.
        foreach ($this->entities as $entity) {
            $entity->update($this);
        }
        
        // 2. Handle collisions after all entities have moved to their new positions.
        $this->handleCollisions();

        // 3. Process the buffers *after* the update loop is complete.
        $this->applyRemovals();
        $this->applyAdditions();

        // 4. Return data for the frontend.
        $entityData = [];
        foreach ($this->entities as $entity) {
            $entityData[] = $entity->getState();
        }
        return $entityData;
    }

    /**
     * Detects and handles collisions between entities.
     * @return void
     */
    private function handleCollisions(): void
    {
        // Filter entities into collidable groups.
        $bolts = array_filter($this->entities, fn($e) => $e instanceof LightningBolt);
        $targets = array_filter($this->entities, fn($e) => !($e instanceof LightningBolt || $e instanceof Statue));

        foreach ($bolts as $bolt) {
            foreach ($targets as $target) {
                // A simple distance check for collision.
                $distSq = pow($bolt->getX() - $target->getX(), exponent: 2) + pow($bolt->getY() - $target->getY(), 2);
                if ($distSq < 25) { // Collision radius
                    $this->killEntity($bolt);
                    $this->killEntity($target);
                }
            }
        }
    }
    
    /**
     * Processes the `$killedEntities` buffer, removing them from the main entity list.
     * @return void
     */
    private function applyRemovals(): void
    {
        if (empty($this->killedEntities)) {
            return;
        }

        $this->entities = array_udiff($this->entities, $this->killedEntities, 
            fn($a, $b) => spl_object_id($a) - spl_object_id($b)
        );
        
        $this->killedEntities = []; // Clear the buffer
    }

    /**
     * Processes the `$newEntities` buffer, adding them to the main entity list.
     * @return void
     */
    private function applyAdditions(): void
    {
        if (empty($this->newEntities)) {
            return;
        }

        $this->entities = array_merge($this->entities, $this->newEntities);
        $this->newEntities = []; // Clear the buffer
    }
}