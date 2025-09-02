<?php

/**
 * The Component Interface
 * Ensures every component has a standard way to be updated.
 */
interface Component
{
    /**
     * The update method is called on every frame of the game loop.
     * @param GameObject $gameObject The object this component is attached to.
     */
    public function update(GameObject $gameObject);
}
