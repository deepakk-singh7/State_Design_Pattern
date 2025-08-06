<?php
require_once 'Entity.php';

class LightningBolt extends Entity
{
    private int $life = 20; // Self-destructs after 20 frames

    public function update(World $world): void
    {
        // Move downwards
        $this->y += 5;

        $this->life--;
        if ($this->life <= 0 || $this->y > 100) {
            // KILL itself when it goes off-screen or times out
            $world->killEntity($this);
        }
    }
}