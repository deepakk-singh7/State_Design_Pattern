<?php

/**
 * An interface for any object that can be managed by our ObjectPool.
 */
interface GameObject {
    /**
     * Resets the object to its default, inactive state.
     * This is called when an object is returned to the pool.
     */
    public function reset(): void;
    
    /**
     * Initializes a recycled object with new properties.
     * @param mixed ...$args A variable number of arguments (e.g., x, y coordinates).
     */
    public function init(...$args): void;
    
    /**
     * Runs one frame of the object's logic (e.g., updating its position).
     * @return bool Returns true on the exact frame the object's life ends, false otherwise.
     */
    public function animate(): bool;

    /**
     * Checks if the object is currently active and in use.
     * @return bool True if active, false if it's available in the pool.
     */
    public function inUse(): bool;

    /**
     * Gets the unique identifier for this specific object instance.
     * @return int The unique ID.
     */
    public function getId(): int;
    
    /**
     * Packages the object's current state into an array for the frontend.
     * @return array An associative array of the object's properties.
     */
    public function getRenderData(): array;

    /**
     * Gets the next available object in the free list.
     * @return GameObject|null The next object, or null if it's the end of the list.
     */
    public function getNext(): ?GameObject;
    
    /**
     * Sets the next available object in the free list.
     * @param GameObject|null $next The object to point to.
     */
    public function setNext(?GameObject $next): void;
}