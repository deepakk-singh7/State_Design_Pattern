<?php
/**
 * A behavior component that encapsulates all movement logic.
 */
class MovementComponent extends Component
{
    /**
     * Updates the GameObject's position based on a direction.
     * @param GameObject $obj The game object to move.
     * @param string $direction The direction to move in ('up', 'down', 'left', 'right').
     */
    public function move(GameObject $obj, string $direction)
    {
        $speed = 15;
        // Update the object's coordinates based on the input direction.
        switch ($direction) {
            case 'up': $obj->y -= $speed; break;
            case 'down': $obj->y += $speed; break;
            case 'left': $obj->x -= $speed; break;
            case 'right': $obj->x += $speed; break;
        }
        
        // --- Inter-Component Communication ---
        // Get the RenderDataComponent to know the object's size for collision checks.
        $renderData = $obj->getComponent(RenderDataComponent::class);
        if ($renderData instanceof RenderDataComponent) {
            // Clamp the position to keep the object within the 400x400 canvas bounds.
            $obj->x = max(0, min(400 - $renderData->size, $obj->x));
            $obj->y = max(0, min(400 - $renderData->size, $obj->y));
        }
    }
}