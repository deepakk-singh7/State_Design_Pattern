<?php

/**
 * Represents a single entity in the simulation world.
 *
 * This class holds the state for a unit, including its unique ID,
 * position, and linked-list pointers.
 */

class Unit
{
    /**
     * A static counter to ensure every unit gets a unique ID.
     * @var int
     */
    private static int $nextId = 0;
    /**
     * The unique identifier for this unit.
     * @var int
     */
    public int $id;

    /**
     * The unit's X-coordinate in the world.
     * @var float
     */
    public float $x;

    /**
     * The unit's Y-coordinate in the world.
     * @var float
     */
    public float $y;

    /**
     * A flag indicating if the unit is close to another unit.
     * This is updated every frame and used for rendering.
     * @var bool
     */
    public bool $isNear = false;

    /**
     * A reference to the Grid that this unit belongs to.
     * @var Grid|null
     */
    public ?Grid $grid;
    
    /**
     * A pointer to the previous unit in the same grid cell's linked list.
     * @var Unit|null
     */
    public ?Unit $prev = null;

    /**
     * A pointer to the next unit in the same grid cell's linked list.
     * @var Unit|null
     */
    public ?Unit $next = null;

    /**
     * Unit constructor.
     *
     * @param Grid|null $grid The grid this unit will be added to.
     * @param float $x The initial X-coordinate.
     * @param float $y The initial Y-coordinate.
     */
    public function __construct(?Grid $grid, float $x, float $y)
    {
        $this->id = self::$nextId++;
        $this->grid = $grid;
        $this->x = $x;
        $this->y = $y;

        // Automatically add the unit to the grid upon creation.
        $this->grid->add($this);
    }
}