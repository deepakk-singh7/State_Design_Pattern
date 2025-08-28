<?php

require_once 'Unit.php';

/**
 * This class manages all units, partitions the world into cells,
 * and handles the simulation logic for moving units and detecting proximity.
 */
class Grid
{
    /** The total width and height of the simulation world in pixels. */
    public const WORLD_SIZE = 600;

    /** The number of cells along one axis of the grid (e.g., 10x10). */
    public const NUM_CELLS = 10;
    
    /** The size of each square cell, derived from world size and cell count. */
    public const CELL_SIZE = self::WORLD_SIZE / self::NUM_CELLS;

    /** The distance within which units are considered "near" each other. */
    public const PROXIMITY_DISTANCE = 7.0; 

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
    public function __construct()
    {
        for ($x = 0; $x < self::NUM_CELLS; $x++) {
            for ($y = 0; $y < self::NUM_CELLS; $y++) {
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
        $cellX = (int)($unit->x / self::CELL_SIZE);
        $cellY = (int)($unit->y / self::CELL_SIZE);

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
        $oldCellX = (int)($unit->x / self::CELL_SIZE);
        $oldCellY = (int)($unit->y / self::CELL_SIZE);
        
        $unit->x = $x;
        $unit->y = $y;

        $newCellX = (int)($unit->x / self::CELL_SIZE);
        $newCellY = (int)($unit->y / self::CELL_SIZE);

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
            $newX = $unit->x + (rand(-10, 10) / 100) * 1.5;
            $newY = $unit->y + (rand(-10, 10) / 100) * 1.5;
            // $newX = $unit->x + (rand(-100, 100) / 100) * 1.5;
            // $newY = $unit->y + (rand(-100, 100) / 100) * 1.5;

            // Enforce world boundaries.
            if ($newX < 0) $newX = 0;
            if ($newX > self::WORLD_SIZE) $newX = self::WORLD_SIZE;
            if ($newY < 0) $newY = 0;
            if ($newY > self::WORLD_SIZE) $newY = self::WORLD_SIZE;
            
            $this->move($unit, $newX, $newY);
        }

        // Phase 2: Iterate through each cell and check for proximity. [this is the core optimization of the pattern.]
        for ($x = 0; $x < self::NUM_CELLS; $x++) {
            for ($y = 0; $y < self::NUM_CELLS; $y++) {
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

                if ($distance < self::PROXIMITY_DISTANCE) {
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
                'id' => $unit->id,
                'x' => $unit->x,
                'y' => $unit->y,
                'isNear' => $unit->isNear,
            ];
        }
        return $state;
    }
}