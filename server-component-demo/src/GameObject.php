<?php
/**
 * The container class for components.
 * It holds shared state and delegates behavior to its components.
 */
class GameObject
{
    /** @var string A unique identifier for the object. */
    public string $id;
    /** @var int The object's position on the X and Y axes, and its health. This is shared state. */
    public int $x, $y, $health = 0;
    /** @var Component[] An associative array holding all components attached to this object. */
    public array $components = [];

    /**
     * Constructs a new GameObject with an ID and initial position.
     * @param string $id The ID for the object (e.g., 'player').
     * @param int $x The starting X coordinate.
     * @param int $y The starting Y coordinate.
     */
    public function __construct(string $id, int $x, int $y)
    {
        $this->id = $id;
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Plugs a new component into the GameObject.
     * @param Component $component The component instance to add.
     */
    public function addComponent(Component $component)
    {
        $this->components[get_class($component)] = $component;
    }

    /**
     * Retrieves a specific component from the GameObject by its class name.
     * @param string $className The fully qualified name of the component class to find.
     * @return Component|null The component instance if found, otherwise null.
     */
    public function getComponent(string $className): ?Component
    {
        return $this->components[$className] ?? null;
    }
}