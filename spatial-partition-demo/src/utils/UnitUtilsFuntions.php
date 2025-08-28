<?php

require_once __DIR__ .'/../Unit.php';
require_once __DIR__ . '/../Grid.php';

// This file contains utility functions related to units.
class UnitUtilsFunctions
{
    /**
     * Updates a unit's position and calculates its old and new grid cell coordinates.
     *
     * @param Unit $unit The unit object (passed by reference).
     * @param float $x The new x-coordinate.
     * @param float $y The new y-coordinate.
     * @param float $cellSize The size of a grid cell.
     * @return array An associative array with old/new cell coordinates.
     */
    public static function updateAndCalculateCoordinates(Unit $unit, float $x, float $y, float $cellSize): array
    {
        // 1. Calculate old cell coordinates from the unit's current position.
        $oldCellX = (int)($unit->x / $cellSize);
        $oldCellY = (int)($unit->y / $cellSize);

        // 2. Update the unit's position (this modifies the original object).
        $unit->x = $x;
        $unit->y = $y;

        // 3. Calculate new cell coordinates from the updated position.
        $newCellX = (int)($unit->x / $cellSize);
        $newCellY = (int)($unit->y / $cellSize);

        // 4. Return all values in a readable associative array.
        return [
            'oldCellX' => $oldCellX,
            'oldCellY' => $oldCellY,
            'newCellX' => $newCellX,
            'newCellY' => $newCellY,
        ];
    }

    /**
     * Calculates a new random position for a unit, ensuring it stays within world boundaries.
     *
     * @param Unit $unit The unit to calculate the new position for.
     * @param float $unitSpeed The movement speed multiplier.
     * @param int $worldSize The boundary of the world.
     * @return array An associative array containing the new 'x' and 'y' coordinates.
     */
    public static function calculateNewPosition(Unit $unit, float $unitSpeed, int $worldSize): array
    {
        // Calculate a random movement vector.
        $newX = $unit->x + (rand(-10, 10) / 100) * $unitSpeed;
        $newY = $unit->y + (rand(-10, 10) / 100) * $unitSpeed;

        // Enforce world boundaries.
        if ($newX < 0) $newX = 0;
        if ($newX > $worldSize) $newX = $worldSize;
        if ($newY < 0) $newY = 0;
        if ($newY > $worldSize) $newY = $worldSize;

        return ['x' => $newX, 'y' => $newY];
    }

    public static function checkProximityInCell(?Unit $unit, float $proximityDistance):void{
        while ($unit !== null) {
            $other = $unit->next;
            while ($other !== null) {
                $distX = $unit->x - $other->x;
                $distY = $unit->y - $other->y;
                // removing sqrt function, [ d2 = x2 + y2];
                $distance = $distX * $distX + $distY * $distY;

                if ($distance < $proximityDistance) {
                    $unit->isNear = true;
                    $other->isNear = true;
                }
                $other = $other->next;
            }
            $unit = $unit->next;
        }
    }
}