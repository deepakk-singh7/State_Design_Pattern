<?php
class CollisionSystem
{
    /**
     * Checks if two GameObjects are colliding using AABB. [Axis Align Boundary Box]
     */
    private function checkCollision(GameObject $a, GameObject $b): bool
    {
        $aRender = $a->getComponent(RenderDataComponent::class);
        $bRender = $b->getComponent(RenderDataComponent::class);

        if ($aRender instanceof RenderDataComponent && $bRender instanceof RenderDataComponent) {
            return ($a->x < $b->x + $bRender->size && $a->x + $aRender->size > $b->x &&
                    $a->y < $b->y + $bRender->size && $a->y + $aRender->size > $b->y);
        }
        return false;
    }

      /**
     * Checks if two entities should collide based on their categories
     */
    private function shouldCheckCollision(GameObject $a, GameObject $b): bool
    {
        $aType = EntityType::from($a->id);
        $bType = EntityType::from($b->id);
        
        $aCategory = $aType->getCategory();
        $bCategory = $bType->getCategory();
        
        // Don't check collision between entities of the same category
        return $aCategory !== $bCategory;
    }

     /**
     * Handles collision between two objects using the Collectable interface
     */
    private function handleCollision(GameObject $object1, GameObject $object2): void
    {
        // Determine which object is the collector and which is collectable
        $collector = null;
        $collected = null;
        $collectableComponent = null;
        
        // Check if object1 can collect object2
        $object2Components = $object2->components;
        foreach ($object2Components as $component) {
            if ($component instanceof Collectable && $component->canBeCollectedBy($object1)) {
                $collector = $object1;
                $collected = $object2;
                $collectableComponent = $component;
                break;
            }
        }

        // If not found, check if object2 can collect object1
        if (!$collectableComponent) {
            $object1Components = $object1->components;
            foreach ($object1Components as $component) {
                if ($component instanceof Collectable && $component->canBeCollectedBy($object2)) {
                    $collector = $object2;
                    $collected = $object1;
                    $collectableComponent = $component;
                    break;
                }
            }
        }
        // Execute the collision if we found a valid collectable
        if ($collectableComponent) {
            $collectableComponent->onCollision($collector, $collected);
        }
    }


    /**
     * Processes all collisions for a given list of game objects.
     * This is the "Handler" that contains all the game's collision rules.
     * @param array $objects The list of all game objects.
     */
    public function process(array &$objects): void
    {
        $objectCount = count($objects);
        
        for ($i = 0; $i < $objectCount; $i++) {
            for ($j = $i + 1; $j < $objectCount; $j++) {
                $object1 = $objects[$i];
                $object2 = $objects[$j];

                // Skip if these entities shouldn't collide (same category)
                if (!$this->shouldCheckCollision($object1, $object2)) {
                    continue;
                }

                // Check for actual collision
                if ($this->checkCollision($object1, $object2)) {
                    $this->handleCollision($object1, $object2);
                }
            }
        }
    }
    


    /**
     * Processes all collisions for a given list of game objects.
     * This is the "Handler" that contains all the game's collision rules.
     * @param array $objects The list of all game objects.
     */
    // public function process(array &$objects): void
    // { // objects = [1,2,3,4] Feedback :: Make sure that all objects are being checks
    //     $objectCount = count($objects);
    //     for ($i = 0; $i < $objectCount; $i++) {
    //         for ($j = $i + 1; $j < $objectCount; $j++) {
    //             $object1 = $objects[$i];
    //             $object2 = $objects[$j];

    //             if ($this->checkCollision($object1, $object2)) {
    //                 // Use the Enum's value for the comparison.
    //                 $isPlayerPackCollision = ($object1->id === EntityType::Player->value && $object2->id === EntityType::HealthPack->value) || 
    //                                          ($object2->id === EntityType::Player->value && $object1->id === EntityType::HealthPack->value);

    //                 if ($isPlayerPackCollision) {
    //                     // get the player and healthPack game object
    //                     $player = ($object1->id === EntityType::Player->value) ? $object1 : $object2;
    //                     $pack = ($object1->id === EntityType::HealthPack->value) ? $object1 : $object2;
    //                     // Heal the Player
    //                     $player->getComponent(HealthComponent::class)->heal($player, 25);
    //                     // Move the healthPack to random position.. 
    //                     $pack->x = rand(20, 360);
    //                     $pack->y = rand(20, 360);
    //                 }
    //             }
    //         }
    //     }
    // }
}