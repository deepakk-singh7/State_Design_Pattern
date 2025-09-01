<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Unit;
use App\utils\UnitUtilsFunctions; 
use InvalidArgumentException;

/**
 * Test suite for the UnitUtilsFunctions class.
 */
class UnitUtilsFunctionsTest extends TestCase
{
    /**
     * @dataProvider \Tests\DataProviders\UtilsDataProvider::validCoordinatesProvider
     */
    public function testGetCellCoordinatesValid(
        float $unitX,
        float $unitY,
        float $cellSize,
        int $numCells,
        array $expectedResult
    ): void {
        $unit = new Unit(1, $unitX, $unitY);
        $result = UnitUtilsFunctions::getCellCoordinates($unit, $cellSize, $numCells);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider \Tests\DataProviders\UtilsDataProvider::invalidParametersProvider
     */
    public function testGetCellCoordinatesInvalidParameters(
        float $unitX,
        float $unitY,
        float $cellSize,
        int $numCells,
        string $expectedExceptionMessage
    ): void {
        $unit = new Unit(1, $unitX, $unitY);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        UnitUtilsFunctions::getCellCoordinates($unit, $cellSize, $numCells);
    }

    /**
     * @dataProvider \Tests\DataProviders\UtilsDataProvider::positionProvider
     */
    public function testCalculateNewPosition(float $initialX, float $initialY, float $speed, int $worldSize): void
    {
        $unit = new Unit(1, $initialX, $initialY);
        $newPosition = UnitUtilsFunctions::calculateNewPosition($unit, $speed, $worldSize);

        $this->assertGreaterThanOrEqual(0, $newPosition['x']);
        $this->assertLessThanOrEqual($worldSize, $newPosition['x']);
        $this->assertGreaterThanOrEqual(0, $newPosition['y']);
        $this->assertLessThanOrEqual($worldSize, $newPosition['y']);

        if ($speed === 0.0) {
            $this->assertEquals($initialX, $newPosition['x']);
            $this->assertEquals($initialY, $newPosition['y']);
        }
    }

    /**
     * @dataProvider \Tests\DataProviders\UtilsDataProvider::proximityProvider
     */
    public function testCheckProximityInCell(?Unit $head, float $proximityDistanceSquare, array $expectedStates): void
    {
        UnitUtilsFunctions::checkProximityInCell($head, $proximityDistanceSquare);

        if (empty($expectedStates)) {
            $this->assertTrue(true, 'Test confirms the function handles a null head without errors.');
            return;
        }

        foreach ($expectedStates as $unitId => $expectedIsNear) {
            $currentUnit = $head;
            $found = false;
            while ($currentUnit !== null) {
                if ($currentUnit->id === $unitId) {
                    $this->assertEquals(
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
     * @dataProvider \Tests\DataProviders\UtilsDataProvider::addUnitProvider
     */
    public function testAddUnit(Unit $unitToAdd, ?Unit $head): void
    {
        // ARRANGE: Keep a reference to the original head to check its pointers later
        $originalHead = $head;

        // ACT: Call the function to add the unit to the list.
        // The $head variable is passed by reference and will be modified.
        UnitUtilsFunctions::addUnit($unitToAdd, $head);

        // ASSERT:
        // 1. The head variable should now be the new unit.
        $this->assertSame($unitToAdd, $head, 'Head was not updated to the new unit.');
        
        // 2. The new head's pointers should be set correctly.
        $this->assertNull($head->prev, 'New head\'s prev pointer should be null.');
        $this->assertSame($originalHead, $head->next, 'New head\'s next pointer should point to the original head.');
        
        // 3. If there was an original head, its 'prev' pointer should be updated to the new unit.
        if ($originalHead !== null) {
            $this->assertSame($unitToAdd, $originalHead->prev, 'Original head\'s prev pointer was not updated.');
        }
    }

    /**
     * @dataProvider \Tests\DataProviders\UtilsDataProvider::unlinkUnitProvider
     */
    public function testUnlinkUnit(Unit $unitToUnlink, ?Unit $head): void
    {
        // ARRANGE: Get references to the original surrounding nodes and head
        $originalHead = $head;
        $prevNode = $unitToUnlink->prev;
        $nextNode = $unitToUnlink->next;

        // ACT: Call the unlink function. $head is passed by reference and may be modified.
        UnitUtilsFunctions::unlinkUnit($unitToUnlink, $head);

        // ASSERT:
        // 1. The unlinked unit's pointers must be null.
        $this->assertNull($unitToUnlink->prev, 'Unlinked unit\'s prev pointer should be null.');
        $this->assertNull($unitToUnlink->next, 'Unlinked unit\'s next pointer should be null.');

        // 2. Check if the head of the list was updated correctly.
        if ($originalHead === $unitToUnlink) {
            $this->assertSame($nextNode, $head, 'Head was not updated to the next node.');
        } else {
            $this->assertSame($originalHead, $head, 'Head should not have changed.');
        }

        // 3. Check if the surrounding nodes are correctly rewired.
        if ($prevNode !== null) {
            $this->assertSame($nextNode, $prevNode->next, 'The previous node\'s next pointer is incorrect.');
        }
        if ($nextNode !== null) {
            $this->assertSame($prevNode, $nextNode->prev, 'The next node\'s prev pointer is incorrect.');
        }
    }
}