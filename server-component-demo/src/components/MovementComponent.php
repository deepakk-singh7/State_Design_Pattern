<?php
/**
 * A behavior component that encapsulates all movement logic.
 */
class MovementComponent extends Component
{
    /**
     * Updates the GameObject's position based on a direction.
     * @param GameObject $obj The game object to move.
     * @param Direction $direction The direction enum to move in ('up', 'down', 'left', 'right').
     */
    public function move(GameObject $obj, Direction $direction)
    {
        $speed = 15;
        // Update the object's coordinates based on the input direction.
        switch ($direction) {
            case Direction::Up: $obj->y -= $speed; break;
            case Direction::Down: $obj->y += $speed; break;
            case Direction::Left: $obj->x -= $speed; break;
            case Direction::Right: $obj->x += $speed; break;
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