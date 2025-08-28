<?php

require_once 'Unit.php';
require_once 'ApiActions.php';

/**
 * This class manages all units, partitions the world into cells,
 * and handles the simulation logic for moving units and detecting proximity.
 */
class Grid
{

    /** @var int The total width and height of the world. */
    private int $worldSize;
    /** @var int The number of cells along one axis. */
    private int $numCells;
    /** @var float The size of each grid cell. */
    private float $cellSize;
    /** @var float The distance to be considered "near". */
    private float $proximityDistance;
    /** @var float The movement speed multiplier for units. */
    private float $unitSpeed;


    /**
     * The 2D array representing the grid. Each element stores the head
     * of a linked list of units within that cell.
     * @var array
     */
    private array $cells = [];

    /**
     * A master list of all unit objects for easy iteration.
     * @var Unit[]
     */
    private array $units = []; 

    /**
     * Grid constructor. Initializes the 2D cells array with null values.
     */
    public function __construct(object $config)
    {
        // Initialize properties from the configuration object.
        $this->worldSize = $config->WORLD_SIZE;
        $this->numCells = $config->NUM_CELLS;
        $this->proximityDistance = $config->PROXIMITY_DISTANCE;
        $this->unitSpeed = $config->UNIT_SPEED;
        $this->cellSize = $this->worldSize / $this->numCells;

        for ($x = 0; $x < $this->numCells; $x++) {
            for ($y = 0; $y < $this->numCells; $y++) {
                $this->cells[$x][$y] = null;
            }
        }
    }
    
    /**
     * Adds a unit to the grid.
     *
     * It adds the unit to the master list and then inserts it into the
     * beginning of the linked list for the appropriate grid cell.
     *
     * @param Unit $unit The unit to add.
     */
    public function add(Unit $unit): void
    {
        // Add to master list for easy global iteration (e.g., for moving all units).
        $this->units[] = $unit;
        
        // Determine which grid cell the unit is in based on its position.
        $cellX = (int)($unit->x / $this->cellSize);
        $cellY = (int)($unit->y / $this->cellSize);

        // Insert the unit at the front of the cell's linked list.
        $unit->prev = null; 
        $unit->next = $this->cells[$cellX][$cellY] ?? null;
        $this->cells[$cellX][$cellY] = $unit;

        // If there was already a unit in the list, update its 'prev' pointer.
        if ($unit->next !== null) {
            $unit->next->prev = $unit;
        }
    }

    /**
     * Moves a unit to a new position and updates the grid structure.
     *
     * If the unit crosses a cell boundary, it is efficiently unlinked
     * from its old cell and re-added to its new one.
     *
     * @param Unit $unit The unit to move.
     * @param float $x The new X-coordinate.
     * @param float $y The new Y-coordinate.
     */
    public function move(Unit $unit, float $x, float $y): void
    {
        // Calculate old and new cell coordinates.
        $oldCellX = (int)($unit->x / $this->cellSize);
        $oldCellY = (int)($unit->y / $this->cellSize);
        
        $unit->x = $x;
        $unit->y = $y;

        $newCellX = (int)($unit->x / $this->cellSize);
        $newCellY = (int)($unit->y / $this->cellSize);

        // If the unit is still in the same cell, do nothing else. [Optimization...]
        if ($oldCellX == $newCellX && $oldCellY == $newCellY) return;

        // Unlink the unit from its old cell's linked list.
        if ($unit->prev !== null) $unit->prev->next = $unit->next;
        if ($unit->next !== null) $unit->next->prev = $unit->prev;

        // If it was the head of the list, update the cell's head pointer.
        if ($this->cells[$oldCellX][$oldCellY] === $unit) {
            $this->cells[$oldCellX][$oldCellY] = $unit->next;
        }

        // Re-add the unit to the grid, which will place it in the correct new cell.
        $this->add($unit);
    }

    /**
     * Advances the entire simulation by one frame.
     *
     * This method first moves all units, then performs the optimized proximity check.
     */
    public function update(): void
    {   
        // Phase 1: Move all units and reset their 'isNear' state.
        foreach ($this->units as $unit) {
            $unit->isNear = false;
            $newX = $unit->x + (rand(-10, 10) / 100) * $this->unitSpeed;
            $newY = $unit->y + (rand(-10, 10) / 100) * $this->unitSpeed;
            // $newX = $unit->x + (rand(-100, 100) / 100) * $this->unitSpeed;
            // $newY = $unit->y + (rand(-100, 100) / 100) * $this->unitSpeed;

            // Enforce world boundaries.
            if ($newX < 0) $newX = 0;
            if ($newX > $this->worldSize) $newX = $this->worldSize;
            if ($newY < 0) $newY = 0;
            if ($newY > $this->worldSize) $newY = $this->worldSize;
            
            $this->move($unit, $newX, $newY);
        }

        // Phase 2: Iterate through each cell and check for proximity. [this is the core optimization of the pattern.]
        for ($x = 0; $x < $this->numCells; $x++) {
            for ($y = 0; $y < $this->numCells; $y++) {
                $this->checkProximityInCell($this->cells[$x][$y]);
            }
        }
    }

    /**
     * Checks for proximity between all units within a single cell's linked list.
     * This is an O(n^2) check, but 'n' is very small, making it fast.
     *
     * @param Unit|null $unit The head of the linked list for a cell.
     */
    private function checkProximityInCell(?Unit $unit): void
    {
        while ($unit !== null) {
            $other = $unit->next;
            while ($other !== null) {
                $distX = $unit->x - $other->x;
                $distY = $unit->y - $other->y;
                $distance = sqrt($distX * $distX + $distY * $distY);

                if ($distance < $this->proximityDistance) {
                    $unit->isNear = true;
                    $other->isNear = true;
                }
                $other = $other->next;
            }
            $unit = $unit->next;
        }
    }

    /**
     * Gathers the current state of all units into a simple array.
     * This is used to create the JSON response for the frontend.
     *
     * @return array An array of unit data.
     */
    public function getUnitsState(): array
    {
        $state = [];
        foreach ($this->units as $unit) {
            $state[] = [
                ReturnState::ID => $unit->id,
                ReturnState::X => $unit->x,
                ReturnState::Y => $unit->y,
                ReturnState::IS_NEAR => $unit->isNear,
            ];
        }
        return $state;
    }
}