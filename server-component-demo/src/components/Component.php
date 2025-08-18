<?php

/**
 * Defines the base blueprint for all components in the game.
 */
abstract class Component
{
    // /** @var GameObject A reference back to the container object. */
    // public ?GameObject $gameObject = null;

    /**
     * Provides a default, empty update method.
     *  
     * @param GameObject $gameObject A reference to the container object.
     */
    public function update(GameObject $gameObject) {}
}