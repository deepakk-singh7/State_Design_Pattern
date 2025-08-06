<?php
require_once 'Entity.php';
require_once 'Minion.php';

class Spawner extends Entity
{
    private int $timer;
    private int $spawnRate;

    public function __construct(float $x, float $y, int $spawnRate = 5)
    {
        parent::__construct($x, $y);
        $this->spawnRate = $spawnRate;
        $this->timer = $this->spawnRate;
    }

    public function update(World $world): void
    {
        $this->timer--;
        if ($this->timer <= 0) {
            // PRODUCE a new Minion at a random position near the spawner
            $minionX = $this->x + rand(-5, 5);
            $minionY = $this->y + rand(-5, 5);
            $world->spawnEntity(new Minion($minionX, $minionY));

            $this->timer = $this->spawnRate; // Reset timer
        }
    }
}