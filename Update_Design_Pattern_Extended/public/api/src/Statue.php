<?php
require_once 'Entity.php';
require_once 'LightningBolt.php';

class Statue extends Entity
{
    private int $framesUntilShoot = 10;

    public function update(World $world): void
    {
        $this->framesUntilShoot--;
        if ($this->framesUntilShoot == 0) {
            // PRODUCE a new entity
            $bolt = new LightningBolt($this->x, $this->y);
            $world->spawnEntity($bolt);
            
            $this->framesUntilShoot = 10; // Reset timer
        }
    }
}