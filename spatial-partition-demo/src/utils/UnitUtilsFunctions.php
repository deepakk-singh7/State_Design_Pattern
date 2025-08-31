<?php


// Declare the namespace corresponding to the directory structure
namespace App\utils;

// Import the Unit class from the parent namespace
use App\Unit; 
use InvalidArgumentException;

require_once __DIR__ .'/../Unit.php';
require_once __DIR__ . '/../Grid.php';


// This file contains utility functions related to units.
class UnitUtilsFunctions
{
    /**
     * Get the cell coordinate based on the unit coordinate and cellsize.
     * @param Unit $unit The unit object (read-only)
     * @param float $cellSize The cell size (width x height) - must be > 0
     * @param int $numCells The number of cells per axis - must be > 0
     * @return array{x: int, y: int} Return an associative array containing cell coordinates
     * @throws InvalidArgumentException If parameters are invalid
     */
    public static function getCellCoordinates(Unit $unit, float $cellSize, int $numCells):array{

        if ($cellSize <= 0) {
            throw new InvalidArgumentException("Cell size must be greater than 0, got: $cellSize");
        }
        
        if ($numCells <= 0) {
            throw new InvalidArgumentException("Number of cells must be greater than 0, got: $numCells");
        }
        
        if ($unit->x < 0 || $unit->y < 0) {
            throw new InvalidArgumentException("Unit coordinates cannot be negative. Got x: {$unit->x}, y: {$unit->y}");
        }
        // Calculate cell coordinates with proper bounds checking
        $cellX = (int)min(($unit->x / $cellSize),$numCells-1); // x == 600 and cellSize is 60 cellX = 10 which is out of bound.. 
        $cellY = (int)min(($unit->y / $cellSize), $numCells-1);
        return ['x' => $cellX, 'y' => $cellY];
    }


    /**
     * Unlink a unit [node] from the cell [ doubly linked list] 
     * @param Unit $unit The unit(Node) object [pass by reference]
     * @param mixed $head Head of the cell [pass by reference]
     * @return void
     */
    public static function unlinkUnit(Unit &$unit, ?Unit &$head):void{
        // unit can be 1: null, only one node, head, last element or the middle element. 
        // If unit prev node exits then update its pointer else it will be the head
        if($unit->prev !== null){
            $unit->prev->next = $unit->next;
        }else {
            $head = $unit->next;
        }

        // If unit next node exits, update its pointer else it will be last node[do nothing]
        if($unit->next !==null){
            $unit->next->prev = $unit->prev;
        }

        // make the next and prev pointers of unit null
        $unit->next = null; 
        $unit->prev = null;
    }

   
    /**
     * Add an Unit(Node) at front of the Cell(List);
     * @param Unit $unit The Unit(node) to be added [Pass by reference]
     * @param mixed $head The head of the cell [Pass by reference]
     * @return void
     */
    public static function addUnit(Unit &$unit, ?Unit &$head):void{

        $unit->prev = null;
        $unit->next = $head;
        
        // If there was already a unit in the list, update its 'prev' pointer.
        if($head !==null){
            $head->prev = $unit;
        }
        $head = $unit;
    }


    // /**
    //  * Updates a unit's position and calculates its old and new grid cell coordinates.
    //  *
    //  * @param Unit $unit The unit object (passed by reference).
    //  * @param float $x The new x-coordinate.
    //  * @param float $y The new y-coordinate.
    //  * @param float $cellSize The size of a grid cell.
    //  * @return array An associative array with old/new cell coordinates.
    //  */
    // public static function updateAndCalculateCoordinates(Unit $unit, float $x, float $y, float $cellSize): array
    // {
    //     // 1. Calculate old cell coordinates from the unit's current position.
    //     $oldCellX = (int)($unit->x / $cellSize);
    //     $oldCellY = (int)($unit->y / $cellSize);

    //     // 2. Update the unit's position (this modifies the original object).
    //     $unit->x = $x;
    //     $unit->y = $y;

    //     // 3. Calculate new cell coordinates from the updated position.
    //     $newCellX = (int)($unit->x / $cellSize);
    //     $newCellY = (int)($unit->y / $cellSize);

    //     // 4. Return all values in a readable associative array.
    //     return [
    //         'oldCellX' => $oldCellX,
    //         'oldCellY' => $oldCellY,
    //         'newCellX' => $newCellX,
    //         'newCellY' => $newCellY,
    //     ];
    // }

    /**
     * Calculates a new random position for a unit, ensuring it stays within world boundaries.
     *
     * @param Unit $unit The unit to calculate the new position for. [Pass by reference]
     * @param float $unitSpeed The movement speed multiplier.
     * @param int $worldSize The boundary of the world.
     * @return array An associative array containing the new 'x' and 'y' coordinates.
     */
    public static function calculateNewPosition(Unit &$unit, float $unitSpeed, int $worldSize): array
    {
        // Calculate a random movement vector.
        $newX = $unit->x + (rand(-10, 10) / 100) * $unitSpeed; // convert rand() into utility functions.. so that it can use across all the applications. m
        $newY = $unit->y + (rand(-10, 10) / 100) * $unitSpeed;

        // Enforce world boundaries.
        if ($newX < 0) $newX = 0;
        if ($newX > $worldSize) $newX = $worldSize;
        if ($newY < 0) $newY = 0;
        if ($newY > $worldSize) $newY = $worldSize;

        return ['x' => $newX, 'y' => $newY];
    }

    /**
     * Check if any two units of a cell are colliding if yes, update there isNear property. 
     * @param mixed $head The head of the cell [Pass by value]
     * @param float $proximityDistanceSquare
     * @return void
     */
    public static function checkProximityInCell(?Unit $head, float $proximityDistanceSquare):void{
    $unit = $head; // Use local variable instead of modifying parameter
    while ($unit !== null) {
        $other = $unit->next;
        while ($other !== null) {
            $distX = $unit->x - $other->x;
            $distY = $unit->y - $other->y;
            $distance = $distX * $distX + $distY * $distY;

            if ($distance < $proximityDistanceSquare) {
                $unit->isNear = true;
                $other->isNear = true;
            }
            $other = $other->next;
        }
        $unit = $unit->next;
    }
}

}