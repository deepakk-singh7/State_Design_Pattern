<?php
abstract class Entity
{
    protected float $x;
    protected float $y;
    protected string $type;

    public function __construct(float $x = 0, float $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
        // Get the class name for the type property
        $this->type = (new \ReflectionClass($this))->getShortName();
    }

    // The update method now accepts the World so it can interact with it
    abstract public function update(World $world, float $deltaTime): void;

    public function getX(): float { return $this->x; }
    public function getY(): float { return $this->y; }
    
    // Returns a simple array representation for JSON encoding
    public function getState(): array
    {
        return [
            'type' => $this->type,
            'x' => $this->x,
            'y' => $this->y
        ];
    }
}