<?php
require_once 'Entity.php';

class Skeleton extends Entity
{
    private bool $patrollingLeft = false;

    public function update(World $world): void
    {
        if ($this->patrollingLeft) {
            $this->x--;
            if ($this->x <= 0) {
                $this->patrollingLeft = false;
            }
        } else {
            $this->x++;
            if ($this->x >= 100) {
                $this->patrollingLeft = true;
            }
        }
    }
}