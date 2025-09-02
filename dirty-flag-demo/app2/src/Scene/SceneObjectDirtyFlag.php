<?php
/**
 * Represents an object in a scene graph using a "Dirty Flag" pattern.
 */

namespace App\Scene;

use App\Config\MessageConstants;
use App\Enums\ObjectName;
use App\Enums\Transform;

class SceneObjectDirtyFlag
{
    /** The name of the object. */
    public ObjectName $name;
    /** The object's position and orientation relative to its parent. */
    private string $localTransform;
    /** The cached final position in world space. */
    private ?string $worldTransform = null;
    /** The flag that indicates if the worldTransform is stale and needs recalculation. */
    private bool $isDirty = true;
    /** A reference to the parent object in the scene graph. */
    private ?SceneObjectDirtyFlag $parent = null;
    /** An array of child objects. */
    private array $children = [];
    /** A static counter to track the expensive calculation runs. */
    public static int $calculationCount = 0;

    /**
     * @param ObjectName $name The name of the object.
     * @param Transform $localTransform The initial local transform.
     */
    public function __construct(ObjectName $name, Transform $localTransform)
    {
        $this->name = $name;
        $this->localTransform = $localTransform->value;
    }

    /**
     * @param SceneObjectDirtyFlag $child The child object to add.
     */
    public function addChild(SceneObjectDirtyFlag $child): void
    {
        $this->children[] = $child;
        $child->parent = $this;
        $child->markDirty(); // The new child is marked dirty. [initially..]
    }

    /**
     * Sets a new local transform. Cheap Operation because it will only set the localTransform value and mark as dirty.
     *
     * @param Transform $newTransform The new local transform to apply.
     */
    public function setLocalTransform(Transform $newTransform): void
    {
        echo sprintf(MessageConstants::ACTION_MOVING, $this->name->value);
        $this->localTransform = $newTransform->value;
        $this->markDirty();
    }

    /**
     * Recursively marks this object and all its descendants as dirty.
     */
    private function markDirty(): void
    {
        $this->isDirty = true;
        foreach ($this->children as $child) {
            // Prevent unnecessary recursion
            if (!$child->isDirty) {
                $child->markDirty();
            }
        }
    }

    /**
     * Gets the final world transform.
     * This is the "lazy" part. The calculation only runs if the dirty flag is true.
     *
     * @return string The up-to-date world transform.
     */
    public function getWorldTransform(): string
    {
        // Only enter the expensive block if the transform is stale / dirty.
        if ($this->isDirty) {
            self::$calculationCount++;
            echo sprintf(MessageConstants::RECALCULATING_DIRTY, $this->name->value);

            if ($this->parent === null) {
                // The root object's world transform is its local transform.
                $this->worldTransform = $this->localTransform;
            } else {
                // To calculate our transform, we must first ensure our parent is up-to-date.
                $parentWorldTransform = $this->parent->getWorldTransform();
                $this->worldTransform = $parentWorldTransform . $this->localTransform;
            }

            // clear the flag. The transform is now clean.
            $this->isDirty = false;
        }

        return $this->worldTransform;
    }
}