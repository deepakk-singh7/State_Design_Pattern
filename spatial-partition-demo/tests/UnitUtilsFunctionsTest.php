<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Unit;
use UnitUtilsFunctions;

/**
 * Test suite for the UnitUtilsFunctions class.
 */
class UnitUtilsFunctionsTest extends TestCase
{
    // 1 : Test for updateAndCalculateCoordinates()
    /**
     * @dataProvider coordinatesProvider
     */
    public function testUpdateAndCalculateCoordinates(
        float $initialX,
        float $initialY,
        float $newX,
        float $newY,
        float $cellSize,
        array $expectedResult
    ): void {
        // ARRANGE: Create a unit with initial coordinates.
        $unit = new Unit(1, $initialX, $initialY);

        // ACT: Call the function to update the unit's position and get cell coordinates.
        $result = UnitUtilsFunctions::updateAndCalculateCoordinates($unit, $newX, $newY, $cellSize);

        // ASSERT: Verify that the unit's coordinates were updated correctly.
        $this->assertEquals($newX, $unit->x, 'Unit X coordinate was not updated correctly.');
        $this->assertEquals($newY, $unit->y, 'Unit Y coordinate was not updated correctly.');

        // ASSERT: Verify that the returned old and new cell coordinates are correct.
        $this->assertEquals($expectedResult, $result, 'The calculated cell coordinates are incorrect.');
    }

    /**
     * Data provider for testUpdateAndCalculateCoordinates.
     *
     * Provides various scenarios including:
     * - No cell change
     * - Horizontal cell change
     * - Vertical cell change
     * - Diagonal cell change
     * - Movement from a boundary
     */
    public static function coordinatesProvider(): array
    {
        return [
            'no cell change' => [
                /* initialX, initialY */ 5.0, 5.0,
                /* newX, newY */       8.0, 8.0,
                /* cellSize */         10.0,
                /* expectedResult */   ['oldCellX' => 0, 'oldCellY' => 0, 'newCellX' => 0, 'newCellY' => 0]
            ],
            'horizontal cell change' => [
                /* initialX, initialY */ 8.0, 5.0,
                /* newX, newY */       12.0, 5.0,
                /* cellSize */         10.0,
                /* expectedResult */   ['oldCellX' => 0, 'oldCellY' => 0, 'newCellX' => 1, 'newCellY' => 0]
            ],
            'vertical cell change' => [
                /* initialX, initialY */ 5.0, 9.0,
                /* newX, newY */       5.0, 11.0,
                /* cellSize */         10.0,
                /* expectedResult */   ['oldCellX' => 0, 'oldCellY' => 0, 'newCellX' => 0, 'newCellY' => 1]
            ],
            'diagonal cell change' => [
                /* initialX, initialY */ 18.0, 19.0,
                /* newX, newY */       21.0, 22.0,
                /* cellSize */         20.0,
                /* expectedResult */   ['oldCellX' => 0, 'oldCellY' => 0, 'newCellX' => 1, 'newCellY' => 1]
            ],
            'movement from a boundary' => [
                /* initialX, initialY */ 10.0, 10.0,
                /* newX, newY */       9.9, 9.9,
                /* cellSize */         10.0,
                /* expectedResult */   ['oldCellX' => 1, 'oldCellY' => 1, 'newCellX' => 0, 'newCellY' => 0]
            ],
        ];
    }


    // 2: Tests for calculateNewPosition()
    
    /**
     * @dataProvider positionProvider
     */
    public function testCalculateNewPosition(float $initialX, float $initialY, float $speed, int $worldSize): void
    {
        // ARRANGE: Create a unit and set its initial position.
        $unit = new Unit(1, $initialX, $initialY);

        // ACT: Calculate the new position for the unit.
        $newPosition = UnitUtilsFunctions::calculateNewPosition($unit, $speed, $worldSize);

        // ASSERT: Verify the new position is within world boundaries.
        $this->assertGreaterThanOrEqual(0, $newPosition['x']);
        $this->assertLessThanOrEqual($worldSize, $newPosition['x']);
        $this->assertGreaterThanOrEqual(0, $newPosition['y']);
        $this->assertLessThanOrEqual($worldSize, $newPosition['y']);

        // ASSERT: If speed is 0, the position should not change.
        if ($speed === 0.0) {
            $this->assertEquals($initialX, $newPosition['x']);
            $this->assertEquals($initialY, $newPosition['y']);
        }
    }

    /**
     * Data provider for testCalculateNewPosition.
     *
     * Provides scenarios including:
     * - Normal speed
     * - Zero speed (unit should not move)
     * - High speed (to test boundary enforcement)
     */
    public static function positionProvider(): array
    {
        return [
            'normal speed in middle' => [500.0, 500.0, 5.0, 1000],
            'zero speed' => [500.0, 500.0, 0.0, 1000],
            'high speed near top-left boundary' => [1.0, 1.0, 50.0, 1000],
            'high speed near bottom-right boundary' => [999.0, 999.0, 50.0, 1000],
        ];
    }


    // 3: Tests for checkProximityInCell()

    /**
     * @dataProvider proximityProvider
     */
    public function testCheckProximityInCell(?Unit $head, float $proximityDistanceSquare, array $expectedStates): void
    {
        // ARRANGE: The test setup is done via the data provider, which builds the linked list of units.

        // ACT: Run the proximity check on the provided list of units.
        UnitUtilsFunctions::checkProximityInCell($head, $proximityDistanceSquare);

        // Handle the case where no assertions are expected.
        if (empty($expectedStates)) {
            $this->assertTrue(true, 'Test confirms the function handles a null head without errors.');
            return;
        }

        // ASSERT: Check the `isNear` state of each unit against the expected outcome.
        foreach ($expectedStates as $unitId => $expectedIsNear) {
            $currentUnit = $head;
            $found = false;
            while ($currentUnit !== null) {
                if ($currentUnit->id === $unitId) {
                    $this->assertEquals( // if match, continue..
                        $expectedIsNear,
                        $currentUnit->isNear,
                        "Unit ID {$unitId} has an incorrect isNear state."
                    );
                    $found = true;
                    break;
                }
                $currentUnit = $currentUnit->next;
            }
            $this->assertTrue($found, "Test logic error: Unit ID {$unitId} not found in list.");
        }
    }

    /**
     * Data provider for testCheckProximityInCell.
     *
     * Provides scenarios including:
     * - Two units that are near
     * - Two units that are far
     * - Multiple units with mixed proximity
     * - A single unit (should not change state)
     * - An empty list (null head)
     */
    public static function proximityProvider(): array
    {
        // 1: 3wo units, near each other 
        $unitA_near = new Unit(1, 10, 10);
        $unitB_near = new Unit(2, 12, 12);
        $unitC_near = new Unit(3,0,0);
        $unitA_near->next = $unitB_near;
        $unitB_near->prev = $unitA_near;
        $unitB_near->next = $unitC_near;
        $unitC_near->prev = $unitB_near;
        $scenario1 = [
            'head' => $unitA_near,
            'proximityDistanceSquare' => 49.0, // (7*7)
            'expectedStates' => [1 => true, 2 => true, 3 => false]
        ];

        // 2: Two units, far from each other 
        $unitA_far = new Unit(3, 10, 10);
        $unitB_far = new Unit(4, 20, 20);
        $unitA_far->next = $unitB_far;
        $unitB_far->prev = $unitA_far;
        $scenario2 = [
            'head' => $unitA_far,
            'proximityDistanceSquare' => 49.0, 
            'expectedStates' => [3 => false, 4 => false]
        ];

        // 3: Three units, A and B are near, C is far 
        $unitA_mix = new Unit(5, 10, 10);
        $unitB_mix = new Unit(6, 11, 11);
        $unitC_mix = new Unit(7, 100, 100);
        $unitA_mix->next = $unitB_mix;
        $unitB_mix->prev = $unitA_mix;
        $unitB_mix->next = $unitC_mix;
        $unitC_mix->prev = $unitB_mix;
        $scenario3 = [
            'head' => $unitA_mix,
            'proximityDistanceSquare' => 49.0, 
            'expectedStates' => [5 => true, 6 => true, 7 => false]
        ];

        // 4: Single unit in cell 
        $unit_single = new Unit(8, 50, 50);
        $scenario4 = [
            'head' => $unit_single,
            'proximityDistanceSquare' => 49.0,
            'expectedStates' => [8 => false]
        ];

        // 5: Empty cell (null head) 
        $scenario5 = [
            'head' => null,
            'proximityDistanceSquare' => 49.0,
            'expectedStates' => []
        ];

        return [
            'two units near' => $scenario1,
            'two units far' => $scenario2,
            'three units mixed proximity' => $scenario3,
            'single unit' => $scenario4,
            'empty (null) list' => $scenario5,
        ];
    }
}