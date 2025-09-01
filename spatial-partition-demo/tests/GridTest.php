<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Grid;
use App\Unit;
use ReflectionClass;
use stdClass;

/**
 * Test suite for the Grid class.
 */
class GridTest extends TestCase
{
    private Grid $grid;
    private stdClass $config;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        // Create a mock configuration object for the Grid
        $this->config = new stdClass();
        $this->config->WORLD_SIZE = 600;
        $this->config->NUM_CELLS = 10; // This makes each cell 60x60
        $this->config->PROXIMITY_DISTANCE_SQUARE = 49;
        $this->config->UNIT_SPEED = 5.0;

        $this->grid = new Grid($this->config);
    }

    public function testAddSingleUnit(): void
    {
        // ARRANGE: Create a unit that should fall into cell [0][0]
        $unit = new Unit(1, 30, 45);
        $expectedCellX = 0;
        $expectedCellY = 0;

        // ACT: Add the unit to the grid
        $this->grid->add($unit);

        // ASSERT:
        // Use Reflection to access the private 'units' and 'cells' properties for verification
        $reflection = new ReflectionClass($this->grid);
        
        $unitsProperty = $reflection->getProperty('units');
        $unitsProperty->setAccessible(true);
        $masterList = $unitsProperty->getValue($this->grid);

        $cellsProperty = $reflection->getProperty('cells');
        $cellsProperty->setAccessible(true);
        $cells = $cellsProperty->getValue($this->grid);
        
        // 1. Check that the unit was added to the master list
        $this->assertCount(1, $masterList, 'Master unit list should contain one unit.');
        $this->assertSame($unit, $masterList[0], 'The correct unit should be in the master list.');

        // 2. Check that the unit is the new head of the correct cell's linked list
        $this->assertSame($unit, $cells[$expectedCellX][$expectedCellY], 'Unit should be the head of its cell.');
        $this->assertNull($unit->next, 'A single unit should have no next pointer.');
        $this->assertNull($unit->prev, 'A single unit should have no prev pointer.');
    }

    public function testAddMultipleUnitsToSameCell(): void
    {
        // ARRANGE: Create two units that will be in the same cell [1][2]
        $unitA = new Unit(1, 80, 150); // Cell [1][2]
        $unitB = new Unit(2, 85, 155); // Cell [1][2]

        // ACT: Add both units to the grid
        $this->grid->add($unitA);
        $this->grid->add($unitB);

        // ASSERT:
        $reflection = new ReflectionClass($this->grid);
        $cellsProperty = $reflection->getProperty('cells');
        $cellsProperty->setAccessible(true);
        $cells = $cellsProperty->getValue($this->grid);

        $cellHead = $cells[1][2];

        // 1. The last unit added (unitB) should be the new head
        $this->assertSame($unitB, $cellHead, 'The last unit added should be the new head.');

        // 2. The new head's 'next' should point to the previous head (unitA)
        $this->assertSame($unitA, $unitB->next, 'The new head should point to the old head.');

        // 3. The old head's 'prev' should point back to the new head (unitB)
        $this->assertSame($unitB, $unitA->prev, 'The old head should point back to the new head.');
        $this->assertNull($unitA->next, 'The original unit should now be second in the list.');
    }

    public function testAddExistingUnitIsNotDuplicatedInMasterList(): void
    {
        // ARRANGE: Add a unit to the grid.
        $unit = new Unit(1, 30, 30);
        $this->grid->add($unit);

        // ACT: Add the exact same unit instance again.
        // The `add` method's `in_array` check should prevent it from being added to the master list twice.
        $this->grid->add($unit);

        // ASSERT:
        $reflection = new ReflectionClass($this->grid);
        $unitsProperty = $reflection->getProperty('units');
        $unitsProperty->setAccessible(true);
        $masterList = $unitsProperty->getValue($this->grid);

        // Check that the master list still only contains one unit
        $this->assertCount(1, $masterList, 'Master list should not contain duplicate units.');
    }


    // ############## Move Methods...#########


    public function testMoveUnitWithinSameCell(): void
    {
        // ARRANGE: Add a unit to cell [1][1]
        $unit = new Unit(1, 70, 70);
        $this->grid->add($unit);
        
        // ACT: Move the unit to a new position within the same cell
        $this->grid->move($unit, 75, 75);

        // ASSERT:
        $reflection = new ReflectionClass($this->grid);
        $cellsProperty = $reflection->getProperty('cells');
        $cellsProperty->setAccessible(true);
        $cells = $cellsProperty->getValue($this->grid);
        
        // 1. The unit's coordinates should be updated
        $this->assertEquals(75, $unit->x);
        $this->assertEquals(75, $unit->y);

        // 2. The unit should still be the head of the original cell
        $this->assertSame($unit, $cells[1][1], 'Unit should remain in the same cell.');
        
        // 3. Since no cell change occurred, the linked list pointers should be null
        $this->assertNull($unit->next, 'Unit next pointer should still be null.');
    }

    public function testMoveUnitToDifferentEmptyCell(): void
    {
        // ARRANGE: Add a unit to cell [1][1]
        $unit = new Unit(1, 70, 70);
        $this->grid->add($unit);

        // ACT: Move the unit to a new, empty cell [2][2] 
        $this->grid->move($unit, 130, 130);

        // ASSERT:
        $reflection = new ReflectionClass($this->grid);
        $cellsProperty = $reflection->getProperty('cells');
        $cellsProperty->setAccessible(true);
        $cells = $cellsProperty->getValue($this->grid);

        // 1. The old cell [1][1] should now be empty
        $this->assertNull($cells[1][1], 'The original cell should now be empty.');

        // 2. The new cell [2][2] should now contain the unit
        $this->assertSame($unit, $cells[2][2], 'Unit should be the head of the new cell.');
    }

    public function testMoveUnitToDifferentOccupiedCell(): void
    {
        // ARRANGE: Add unitA to cell [1][1] and unitB to cell [2][2]
        $unitA = new Unit(1, 70, 70);
        $unitB = new Unit(2, 130, 130);
        $this->grid->add($unitA);
        $this->grid->add($unitB);

        // ACT: Move unitA into unitB's cell [2][2]
        $this->grid->move($unitA, 140, 140);

        // ASSERT:
        $reflection = new ReflectionClass($this->grid);
        $cellsProperty = $reflection->getProperty('cells');
        $cellsProperty->setAccessible(true);
        $cells = $cellsProperty->getValue($this->grid);

        // 1. The old cell [1][1] should now be empty
        $this->assertNull($cells[1][1], 'The original cell for unitA should be empty.');

        // 2. The moved unit (unitA) should be the new head of cell [2][2]
        $this->assertSame($unitA, $cells[2][2], 'Moved unit should be the new head of the destination cell.');
        
        // 3. The new head (unitA) should point to the original occupant (unitB)
        $this->assertSame($unitB, $unitA->next, 'New head (unitA) should point to the old head (unitB).');
        $this->assertSame($unitA, $unitB->prev, 'Old head (unitB) should point back to the new head (unitA).');
    }
    
    public function testMoveNonHeadUnitToNewCell(): void
    {
        // ARRANGE: Add unitA then unitB to cell [1][1]. The list is B -> A.
        $unitA = new Unit(1, 70, 70);
        $unitB = new Unit(2, 80, 80);
        $this->grid->add($unitA);
        $this->grid->add($unitB);
        
        // ACT: Move unitA (the second item in the list) to cell [2][2]
        $this->grid->move($unitA, 130, 130);
        
        // ASSERT:
        $reflection = new ReflectionClass($this->grid);
        $cellsProperty = $reflection->getProperty('cells');
        $cellsProperty->setAccessible(true);
        $cells = $cellsProperty->getValue($this->grid);

        // 1. The old cell [1][1] should now only contain unitB
        $this->assertSame($unitB, $cells[1][1], 'The old cell should still be headed by unitB.');
        $this->assertNull($unitB->next, 'The next pointer of unitB should be null after unitA was moved.');

        // 2. The new cell [2][2] should be headed by unitA
        $this->assertSame($unitA, $cells[2][2], 'The new cell should be headed by the moved unitA.');
        $this->assertNull($unitA->prev, 'The prev pointer of the moved unit should be null.');
    }

}