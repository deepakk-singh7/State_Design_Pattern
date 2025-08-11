<?php
/**
 * @file Contains the abstract base class for all game objects.
 */

/**
 * Represents the fundamental blueprint for any object that can exist in the game world.
 *
 * This abstract class provides core properties like position and type, and defines the essential methods that all concrete entities must implement.
 */
abstract class Entity
{
    /**
     * The unique identifier for this entity instance.
     * Crucial for the client to track objects between state updates for interpolation.
     * @var string
     */
    public string $id; 
    /**
     * The horizontal position of the entity in the world (e.g., 0-100).
     * @var float
     */
    protected float $x;
    /**
     * The vertical position of the entity in the world (e.g., 0-100).
     * @var float
     */
    protected float $y;
    /**
     * The type of the entity (e.g., "Skeleton", "Minion").
     * @var string
     */
    protected string $type;

     /**
     * Entity constructor.
     *
     * @param float $x The initial horizontal position.
     * @param float $y The initial vertical position.
     */
    public function __construct(float $x = 0, float $y = 0)
    {
        // Generate a unique ID that persists across serialization
        $this->id = uniqid('', true);
        $this->x = $x;
        $this->y = $y;
        // Automatically determine the entity's type from its class name.
        $this->type = (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Updates the entity's state for a single game tick.
     *
     * @param World $world The world instance, allowing the entity to interact with it (e.g., spawn or kill other entities).
     * @param float $deltaTime The time elapsed since the last update, in seconds. Used for frame-rate independent calculations.
     * @return void
     */
    abstract public function update(World $world, float $deltaTime): void;
    /**
     * Gets the current horizontal position.
     * @return float
     */
    public function getX(): float { return $this->x; }
    /**
     * Gets the current vertical position.
     * @return float
     */
    public function getY(): float { return $this->y; }
    
    /**
     * Serializes the entity's state into a simple array.
     * This is used to create the JSON response sent to the client.
     *
     * @return array An associative array of the entity's public state.
     */
    public function getState(): array
    {
        return [
            'id' => $this->id, // Include the ID in the state
            'type' => $this->type,
            'x' => $this->x,
            'y' => $this->y
        ];
    }
}