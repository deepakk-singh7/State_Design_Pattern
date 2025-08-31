<?php

namespace App;

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
    private float $proximityDistanceSquare;
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
        $this->proximityDistanceSquare = $config->PROXIMITY_DISTANCE_SQUARE;
        $this->unitSpeed = $config->UNIT_SPEED;
        $this->cellSize = $this->worldSize / $this->numCells; // widht x height of each cell

        for ($x = 0; $x < $this->numCells; $x++) { // 0 -> 9
            for ($y = 0; $y < $this->numCells; $y++) { // 0 ->9
                $this->cells[$x][$y] = null; // [0][0] -> [9][9] => total 100
            }
        }
    }
    
    /**
     * Adds a unit to the grid.
     *
     * It adds the unit to the master list and then inserts it into the
     * beginning of the linked list for the appropriate grid cell.
     *
     * @param Unit $unit The unit to be added in the cell and units[] [Pass by reference]
     * @return void
     */
    public function add(Unit &$unit): void{
        // Add to master list for easy global iteration (e.g., for moving all units). Note : Only add to master list, if it's not already there, moving Units will be already there. 
        if(!in_array($unit, $this->units,true)){
            $this->units[] = $unit;
        }
        
        // Determine which cell the unit is in, based on its coordinates.
        ['x' => $cellX, 'y' => $cellY] = UnitUtilsFunctions::getCellCoordinates($unit, $this->cellSize, $this->numCells);

        // Insert the unit at the front of the cell's linked list.
        // Get the head of this cell 
        // $headOfCell = $this->cells[$cellX][$cellY]; // In this way I'm just copying the referance, won't work

        // Insert the unit at the front of the cell's linked list.
        UnitUtilsFunctions::addUnit($unit,$this->cells[$cellX][$cellY]); // We are passing reference of the head...
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
    public function move(Unit &$unit, float $x, float $y): void
    {
        // Verify the move() parameters // Not checking the data type and accepted values yet. 
        if(!$unit || !$x || !$y) return;

        // Calculate old and new cell coordinates.
        ['x' => $oldCellX, 'y' => $oldCellY] = UnitUtilsFunctions::getCellCoordinates($unit,$this->cellSize, $this->numCells);
        
        //  Update the unit coordinate
        $unit->x = $x;
        $unit->y = $y;

        // get the new Cell Coordinate
        ['x' => $newCellX, 'y' => $newCellY] = UnitUtilsFunctions::getCellCoordinates($unit, $this->cellSize, $this->numCells);


        // If the unit is still in the same cell, do nothing else. [Optimization...]
        if ($oldCellX === $newCellX && $oldCellY  === $newCellY) return;

        // If cell change, chage the data structure of old cell and add it to new one.

        // 1: Get a reference to the head node of the old cell 

        // $headOfOldCell = $this->cells[$oldCellX][$oldCellY];

        // 2: Unlink unit from the old Cell

        UnitUtilsFunctions::unlinkUnit($unit,$this->cells[$oldCellX][$oldCellY]);  

        // Re-add the unit to the grid. The add() method will automatically place it
        // in the correct new cell based on its updated coordinates.
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

             // Get the new position.
            $newPosition = UnitUtilsFunctions::calculateNewPosition(
                $unit,
                $this->unitSpeed,
                $this->worldSize
            );
            
            $this->move($unit, $newPosition['x'], $newPosition['y']);
        }

        // Phase 2: Iterate through each cell and check for proximity. [this is the core optimization of the pattern.]
        for ($x = 0; $x < $this->numCells; $x++) {
            for ($y = 0; $y < $this->numCells; $y++) {
                UnitUtilsFunctions::checkProximityInCell($this->cells[$x][$y],$this->proximityDistanceSquare);
            }
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