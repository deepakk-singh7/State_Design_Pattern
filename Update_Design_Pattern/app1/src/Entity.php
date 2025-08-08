<?php

abstract class Entity
{
    protected float $x;
    protected float $y;

    public function __construct(float $x = 0, float $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    // This is the core of the pattern, Every entity MUST implement this method to define its per-frame behavior.
    abstract public function update(): void;

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setX(float $x): void
    {
        $this->x = $x;
    }

    public function setY(float $y): void
    {
        $this->y = $y;
    }
}