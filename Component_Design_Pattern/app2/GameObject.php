<?php 
require_once 'Component.php';
/**
 * The GameObject Class (The "Container")
 * This class doesn't have game logic itself. It's a container that holds
 * components and shared data (like position).
 */
class GameObject
{
    // Public properties can be accessed by any component
    public $position;
    public $velocity;

    /**
     * @var Component[]
     */
    private $components = [];

    public function __construct()
    {
        // Initialize with default state
        $this->position = ['x' => 0, 'y' => 0];
        $this->velocity = ['x' => 0, 'y' => 0];
    }

        /**
     * Attaches a new component to this GameObject.
     * @param Component $component The component to add.
     */
    public function addComponent(Component $component)
    {
        $this->components[] = $component;
    }

    /**
     * Updates the GameObject by updating all of its components.
     */
    public function update()
    {
        // Loop through and update every component it has
        foreach ($this->components as $component) {
            $component->update($this);
        }
    }
}