<?php

require_once 'GameObject.php';
require_once 'Enums.php';

/**
 * A generic, high-performance object pool that manages the lifecycle of reusable objects.
 * It uses a "free list" implementation to achieve constant-time O(1) creation and recycling of objects.
 */
class ObjectPool {
    /**
     * The main array holding all pre-allocated objects, both active and inactive.
     * @var GameObject[]
     */
    private array $pool = [];

    // An associative array to quickly find an object by its ID.
    private array $idLookup = []; 

    /**
     * A pointer to the first available object in the free list.
     * When null, the pool is full.
     * @var GameObject|null
     */
    private ?GameObject $firstAvailable = null;

    /**
     * The fully qualified class name of the objects this pool manages (e.g., 'Particle').
     * @var string
     */
    private string $className;

    /**
     * The strategy to use when a new object is requested from a full pool.
     * @var string
     */
    private string $overflowStrategy;

    /**
     * A simple counter for the number of currently active objects.
     * @var int
     */
    private int $activeCount = 0;

    /**
     * ObjectPool constructor.
     * Initializes the pool by pre-allocating all objects and building the free list.
     *
     * @param string $className The class name of the objects to pool (e.g., 'Particle').
     * @param int $size The total number of objects to pre-allocate for this pool.
     * @param string $overflowStrategy The strategy to apply when the pool is full.
     */
    public function __construct(string $className, int $size, string $overflowStrategy) {
        $this->className = $className;
        $this->overflowStrategy = $overflowStrategy;
        
        // 1. Pre-allocate all objects for the entire lifetime of the pool.
        for ($i = 0; $i < $size; $i++) {
            $this->pool[$i] = new $className();
            // Populate the lookup map during creation.
            $this->idLookup[$this->pool[$i]->getId()] = $this->pool[$i];
        }

        // 2. Build the free list by chaining all the new objects together.
        $this->firstAvailable = $this->pool[0];
        for ($i = 0; $i < $size - 1; $i++) {
            $this->pool[$i]->setNext($this->pool[$i + 1]);
        }
        $this->pool[$size - 1]->setNext(null); // The last object terminates the list.
    }
    
    /**
     * Retrieves an object from the pool for use.
     * Handles the overflow strategy if the pool is full.
     *
     * @param mixed ...$args Variable arguments to be passed to the object's init() method.
     * @return GameObject|null The activated GameObject, or null if the pool is full and the strategy is 'ignore'.
     */
    public function create(...$args): ?GameObject {
        // Check if the pool is full.
        if ($this->firstAvailable === null) {
            if ($this->overflowStrategy === OverflowStrategy::IGNORE) {
                return null; // Simply give up.
            }
            if ($this->overflowStrategy === OverflowStrategy::RECLAIM) {
                // Forcibly find and recycle the first active object we encounter.
                $reclaimedObject = null;
                foreach($this->pool as $obj) {
                    if ($obj->inUse()) {
                        $reclaimedObject = $obj;
                        break;
                    }
                }
                if ($reclaimedObject === null) return null; // Should not happen in a full pool.
                $this->returnObject($reclaimedObject);
            }
        }
        
        // Retrieve the object from the front of the free list.
        $newObject = $this->firstAvailable;
        $this->firstAvailable = $newObject->getNext();
        
        // Initialize and activate it.
        $newObject->init(...$args);
        $this->activeCount++;
        
        return $newObject;
    }
    
    /**
     * Returns an active object back to the pool, making it available for reuse.
     * The object is added to the front of the free list.
     *
     * @param GameObject $object The object to return to the pool.
     * @return void
     */
    public function returnObject(GameObject $object): void {
        // Safety check to avoid returning an object that's already in the pool.
        if (!$object->inUse()) return; 

        // Add the object to the front of the free list.
        $object->setNext($this->firstAvailable);
        $this->firstAvailable = $object;
        
        // Reset the object's internal state to mark it as inactive.
        $object->reset(); 
        
        $this->activeCount--;
    }
    
    /**
     * Gets the current status of the pool's usage.
     *
     * @return array{total: int, active: int, available: int} An associative array with usage statistics.
     */
    public function getStatus(): array {
        return [
            'total' => count($this->pool),
            'active' => $this->activeCount,
            'available' => count($this->pool) - $this->activeCount
        ];
    }

    /**
     * Method to find a specific object instance by its unique ID.
     *
     * @param int $id The ID of the object to find.
     * @return GameObject|null The found object, or null.
     */
    public function getObjectById(int $id): ?GameObject {
        return $this->idLookup[$id] ?? null;
    }
}