<?php
/**
 * Represents the Type Object.
 *
 * Each instance of this class holds the shared data for a specific "type" or "breed" of monster.
 * relying on a factory to handle the creation from raw data.
 */

class Breed {
    // Public properties for easy serialization into JSON.
    // public string $name;
    // public int $health;
    // public string $attack;
    // public string $image;

     /**
     * Constructs a new Breed object.
     *
     * @param string $name The name of the breed.
     * @param int $health The starting health for this breed.
     * @param string $attack The attack message for this breed.
     * @param string $image The URL for the breed's image.
     */
    //  public function __construct(string $name, int $health, string $attack, string $image) {
    //     $this->name = $name;
    //     $this->health = $health;
    //     $this->attack = $attack;
    //     $this->image = $image;
    // }

    // ---- Modification ----
    
    // PHP 8.0+ Constructor Property Promotion.
    // This declares the public properties and assigns them in one step.
    public function __construct(
        public string $name,
        public int $health,
        public string $attack,
        public string $image
    ) {}
}