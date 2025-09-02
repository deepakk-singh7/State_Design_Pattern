<?php

require_once 'Breed.php';

/**
 * Represents the Typed Object.
 *
 * Each instance of this class is a unique monster in the game world.
 */

class Monster {
    // Instance-specific data: each monster gets its own unique ID.
    public int $id;
    public Breed $breed; // A Monster "has-a" Breed.

    /**
     * Constructs a new Monster instance.
     *
     * @param Breed $breed The Breed object that defines this monster's type.
     */
    public function __construct(Breed $breed) {
        $this->id = rand(1000, 9999); // Give each monster a unique ID
        $this->breed = $breed;
    }
}