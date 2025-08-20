<?php
class CollisionSystem
{
    /**
     * Checks if two GameObjects are colliding using AABB. [Axis Align Boundary Box]
     */
    private function checkCollision(GameObject $a, GameObject $b): bool
    {
        $aRender = $a->getComponent(RenderDataComponent::class); // To get the size or widht of a game object
        $bRender = $b->getComponent(RenderDataComponent::class);

        if ($aRender instanceof RenderDataComponent && $bRender instanceof RenderDataComponent) {
            return ($a->x < $b->x + $bRender->size && $a->x + $aRender->size > $b->x &&
                    $a->y < $b->y + $bRender->size && $a->y + $aRender->size > $b->y);
        }
        return false;
    }

    /**
     * Processes all collisions for a given list of game objects.
     * This is the "Handler" that contains all the game's collision rules.
     * @param array $objects The list of all game objects.
     */
    public function process(array &$objects): void
    { // objects = [1,2,3,4] Feedback :: Make sure that all objects are being checks
        $objectCount = count($objects);
        for ($i = 0; $i < $objectCount; $i++) {
            for ($j = $i + 1; $j < $objectCount; $j++) {
                $object1 = $objects[$i];
                $object2 = $objects[$j];

                if ($this->checkCollision($object1, $object2)) {
                    // Use the Enum's value for the comparison.
                    $isPlayerPackCollision = ($object1->id === EntityType::Player->value && $object2->id === EntityType::HealthPack->value) || 
                                             ($object2->id === EntityType::Player->value && $object1->id === EntityType::HealthPack->value);

                    if ($isPlayerPackCollision) {
                        // get the player and healthPack game object
                        $player = ($object1->id === EntityType::Player->value) ? $object1 : $object2;
                        $pack = ($object1->id === EntityType::HealthPack->value) ? $object1 : $object2;
                        // Heal the Player
                        $player->getComponent(HealthComponent::class)->heal($player, 25);
                        // Move the healthPack to random position.. 
                        $pack->x = rand(20, 360);
                        $pack->y = rand(20, 360);
                    }
                }
            }
        }
    }
}