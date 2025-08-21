<?php

/**
 * Interface for entities that can be collected by other entities
 */
interface Collectable
{
    /**
     * Handles the collision/collection logic
     * @param GameObject $collector The entity that collected this item
     * @param GameObject $collected The item being collected (this object)
     * @return void
     */
    public function onCollision(GameObject $collector, GameObject $collected): void;
    
    /**
     * Determines if this entity should be collected by the given collector
     * @param GameObject $collector The potential collector
     * @return bool
     */
    public function canBeCollectedBy(GameObject $collector): bool;
}