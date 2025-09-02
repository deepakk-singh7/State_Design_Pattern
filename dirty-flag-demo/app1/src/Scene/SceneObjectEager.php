<?php
/**
 * Represents an object in a graph.
 */
namespace App\Scene;

use App\Config\MessageConstants;
use App\Enums\ObjectName;
use App\Enums\Transform;

class SceneObjectEager
{
    /** The name of the object. */
    public ObjectName $name;
    /** The object's position and orientation relative to its parent. */
    private string $localTransform;

    /** The object's final calculated position in world space. Cached after calculation. */
    private ?string $worldTransform = null;

    /** A reference to the parent object in the scene graph. Null for the root object. */
    private ?SceneObjectEager $parent = null;

    /** An array of child objects. */
    private array $children = [];
    
    /** A static counter to track how many times the expensive calculation runs across all instances. */
    public static int $calculationCount = 0;

    /**
     * Constructs a new SceneObject.
     * @param ObjectName $name The name of the object.
     * @param Transform $localTransform The initial local transform.
     */
    public function __construct(ObjectName $name, Transform $localTransform)
    {
        $this->name = $name;
        $this->localTransform = $localTransform->value;
    }

    /**
     * Adds a child object to this object, establishing a parent-child relationship.
     * @param SceneObjectEager $child The child object to add.
     */
    public function addChild(SceneObjectEager $child): void
    {
        $this->children[] = $child;
        $child->parent = $this;
    }

    /**
     * Sets a new local transform and immediately triggers a recursive recalculation.
     * @param Transform $newTransform The new local transform to apply.
     */
    public function setLocalTransform(Transform $newTransform): void
    {
        echo sprintf(MessageConstants::ACTION_MOVING, $this->name->value);
        $this->localTransform = $newTransform->value;
        // Directly Calling to calculate the WorldTransform.
        $this->recalculateWorldTransform();
    }
    /**
     * Performs the "expensive" process of calculating the world transform.
     * It then forces all of its children to do the same in a cascading update.
     */
    private function recalculateWorldTransform(): void
    {
        self::$calculationCount++;
        echo sprintf(MessageConstants::RECALCULATING, $this->name->value);
        // If there's no parent, world transform is just the local transform
        if ($this->parent === null) {
            $this->worldTransform = $this->localTransform;
        } else {
            // Otherwise, combine the parent's world transform with this object's local transform. [ just for simplicity, in real it will be more complex like matrix multiplication]
            $this->worldTransform = ($this->parent->worldTransform ?? '') . $this->localTransform;
        }
        // Force every child to update its own transform now.
        foreach ($this->children as $child) {
            // recursion.. 
            $child->recalculateWorldTransform();
        }
    }

    /**
     * Gets the final world transform.
     * If it has never been calculated, it triggers the calculation once.
     * @return string|null The final calculated world transform.
     */
    public function getWorldTransform(): ?string
    {
        if ($this->worldTransform === null) {
            $this->recalculateWorldTransform();
        }
        return $this->worldTransform;
    }
}