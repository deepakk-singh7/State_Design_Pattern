<?php
require_once 'Entity.php';

class Minion extends Entity
{
    public function update(World $world): void
    {
        // Wander around randomly
        $this->x += rand(-1, 1);
        $this->y += rand(-1, 1);

        // Clamp position to world bounds (0-100)
        $this->x = max(0, min(100, $this->x));
        $this->y = max(0, min(100, $this->y));
    }
}