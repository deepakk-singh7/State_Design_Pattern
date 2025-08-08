<?php 

require_once 'Entity.php';

class Statue extends Entity
{
    private int $framesUntilShoot = 5;

    public function update(): void
    {
        $this->framesUntilShoot--;
        if ($this->framesUntilShoot == 0) {
            echo "Statue at ({$this->x}, {$this->y}) shoots lightning! ⚡\n";
            $this->framesUntilShoot = 5; // Reset timer
        } else {
            echo "Statue is charging...\n";
        }
    }
}