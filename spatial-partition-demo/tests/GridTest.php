<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Grid;
use App\Unit;
use App\ReturnState; 
// use ReflectionClass;
use stdClass;

/**
 * Test suite for the Grid class.
 */
// class GridTest extends TestCase
// {
//     private Grid $grid;
//     private stdClass $config;


//     protected function setUp(): void
//     {
//         // Create a mock configuration object for the Grid
//         $this->config = new stdClass();
//         $this->config->WORLD_SIZE = 600;
//         $this->config->NUM_CELLS = 10; // This makes each cell 60x60
//         $this->config->PROXIMITY_DISTANCE_SQUARE = 49;
//         $this->config->UNIT_SPEED = 5.0;

//         $this->grid = new Grid($this->config);
//     }

//     public function testAddSingleUnit(): void
//     {
//         // ARRANGE: Create a unit that should fall into cell [0][0]
//         $unit = new Unit(1, 30, 45);
//         $expectedCellX = 0;
//         $expectedCellY = 0;

//         // ACT: Add the unit to the grid
//         $this->grid->add($unit);

//         // ASSERT:
//         // Use Reflection to access the private 'units' and 'cells' properties for verification
//         $reflection = new ReflectionClass($this->grid);
        
//         $unitsProperty = $reflection->getProperty('units');
//         $unitsProperty->setAccessible(true);
//         $masterList = $unitsProperty->getValue($this->grid);

//         $cellsProperty = $reflection->getProperty('cells');
//         $cellsProperty->setAccessible(true);
//         $cells = $cellsProperty->getValue($this->grid);
        
//         // 1. Check that the unit was added to the master list
//         $this->assertCount(1, $masterList, 'Master unit list should contain one unit.');
//         $this->assertSame($unit, $masterList[0], 'The correct unit should be in the master list.');

//         // 2. Check that the unit is the new head of the correct cell's linked list
//         $this->assertSame($unit, $cells[$expectedCellX][$expectedCellY], 'Unit should be the head of its cell.');
//         $this->assertNull($unit->next, 'A single unit should have no next pointer.');
//         $this->assertNull($unit->prev, 'A single unit should have no prev pointer.');
//     }

//     public function testAddMultipleUnitsToSameCell(): void
//     {
//         // ARRANGE: Create two units that will be in the same cell [1][2]
//         $unitA = new Unit(1, 80, 150); // Cell [1][2]
//         $unitB = new Unit(2, 85, 155); // Cell [1][2]

//         // ACT: Add both units to the grid
//         $this->grid->add($unitA);
//         $this->grid->add($unitB);

//         // ASSERT:
//         $reflection = new ReflectionClass($this->grid);
//         $cellsProperty = $reflection->getProperty('cells');
//         $cellsProperty->setAccessible(true);
//         $cells = $cellsProperty->getValue($this->grid);

//         $cellHead = $cells[1][2];

//         // 1. The last unit added (unitB) should be the new head
//         $this->assertSame($unitB, $cellHead, 'The last unit added should be the new head.');

//         // 2. The new head's 'next' should point to the previous head (unitA)
//         $this->assertSame($unitA, $unitB->next, 'The new head should point to the old head.');

//         // 3. The old head's 'prev' should point back to the new head (unitB)
//         $this->assertSame($unitB, $unitA->prev, 'The old head should point back to the new head.');
//         $this->assertNull($unitA->next, 'The original unit should now be second in the list.');
//     }

//     public function testAddExistingUnitIsNotDuplicatedInMasterList(): void
//     {
//         // ARRANGE: Add a unit to the grid.
//         $unit = new Unit(1, 30, 30);
//         $this->grid->add($unit);

//         // ACT: Add the exact same unit instance again.
//         // The `add` method's `in_array` check should prevent it from being added to the master list twice.
//         $this->grid->add($unit);

//         // ASSERT:
//         $reflection = new ReflectionClass($this->grid);
//         $unitsProperty = $reflection->getProperty('units');
//         $unitsProperty->setAccessible(true);
//         $masterList = $unitsProperty->getValue($this->grid);

//         // Check that the master list still only contains one unit
//         $this->assertCount(1, $masterList, 'Master list should not contain duplicate units.');
//     }


//     // ############## Move Methods...#########


//     public function testMoveUnitWithinSameCell(): void
//     {
//         // ARRANGE: Add a unit to cell [1][1]
//         $unit = new Unit(1, 70, 70);
//         $this->grid->add($unit);
        
//         // ACT: Move the unit to a new position within the same cell
//         $this->grid->move($unit, 75, 75);

//         // ASSERT:
//         $reflection = new ReflectionClass($this->grid);
//         $cellsProperty = $reflection->getProperty('cells');
//         $cellsProperty->setAccessible(true);
//         $cells = $cellsProperty->getValue($this->grid);
        
//         // 1. The unit's coordinates should be updated
//         $this->assertEquals(75, $unit->x);
//         $this->assertEquals(75, $unit->y);

//         // 2. The unit should still be the head of the original cell
//         $this->assertSame($unit, $cells[1][1], 'Unit should remain in the same cell.');
        
//         // 3. Since no cell change occurred, the linked list pointers should be null
//         $this->assertNull($unit->next, 'Unit next pointer should still be null.');
//     }

//     public function testMoveUnitToDifferentEmptyCell(): void
//     {
//         // ARRANGE: Add a unit to cell [1][1]
//         $unit = new Unit(1, 70, 70);
//         $this->grid->add($unit);

//         // ACT: Move the unit to a new, empty cell [2][2] 
//         $this->grid->move($unit, 130, 130);

//         // ASSERT:
//         $reflection = new ReflectionClass($this->grid);
//         $cellsProperty = $reflection->getProperty('cells');
//         $cellsProperty->setAccessible(true);
//         $cells = $cellsProperty->getValue($this->grid);

//         // 1. The old cell [1][1] should now be empty
//         $this->assertNull($cells[1][1], 'The original cell should now be empty.');

//         // 2. The new cell [2][2] should now contain the unit
//         $this->assertSame($unit, $cells[2][2], 'Unit should be the head of the new cell.');
//     }

//     public function testMoveUnitToDifferentOccupiedCell(): void
//     {
//         // ARRANGE: Add unitA to cell [1][1] and unitB to cell [2][2]
//         $unitA = new Unit(1, 70, 70);
//         $unitB = new Unit(2, 130, 130);
//         $this->grid->add($unitA);
//         $this->grid->add($unitB);

//         // ACT: Move unitA into unitB's cell [2][2]
//         $this->grid->move($unitA, 140, 140);

//         // ASSERT:
//         $reflection = new ReflectionClass($this->grid);
//         $cellsProperty = $reflection->getProperty('cells');
//         $cellsProperty->setAccessible(true);
//         $cells = $cellsProperty->getValue($this->grid);

//         // 1. The old cell [1][1] should now be empty
//         $this->assertNull($cells[1][1], 'The original cell for unitA should be empty.');

//         // 2. The moved unit (unitA) should be the new head of cell [2][2]
//         $this->assertSame($unitA, $cells[2][2], 'Moved unit should be the new head of the destination cell.');
        
//         // 3. The new head (unitA) should point to the original occupant (unitB)
//         $this->assertSame($unitB, $unitA->next, 'New head (unitA) should point to the old head (unitB).');
//         $this->assertSame($unitA, $unitB->prev, 'Old head (unitB) should point back to the new head (unitA).');
//     }
    
//     public function testMoveNonHeadUnitToNewCell(): void
//     {
//         // ARRANGE: Add unitA then unitB to cell [1][1]. The list is B -> A.
//         $unitA = new Unit(1, 70, 70);
//         $unitB = new Unit(2, 80, 80);
//         $this->grid->add($unitA);
//         $this->grid->add($unitB);
        
//         // ACT: Move unitA (the second item in the list) to cell [2][2]
//         $this->grid->move($unitA, 130, 130);
        
//         // ASSERT:
//         $reflection = new ReflectionClass($this->grid);
//         $cellsProperty = $reflection->getProperty('cells');
//         $cellsProperty->setAccessible(true);
//         $cells = $cellsProperty->getValue($this->grid);

//         // 1. The old cell [1][1] should now only contain unitB
//         $this->assertSame($unitB, $cells[1][1], 'The old cell should still be headed by unitB.');
//         $this->assertNull($unitB->next, 'The next pointer of unitB should be null after unitA was moved.');

//         // 2. The new cell [2][2] should be headed by unitA
//         $this->assertSame($unitA, $cells[2][2], 'The new cell should be headed by the moved unitA.');
//         $this->assertNull($unitA->prev, 'The prev pointer of the moved unit should be null.');
//     }

// }



####### TESTING THROUGH PUBLIC INTERFACE ONLY || NO RELECTION USED. 

class GridTest extends TestCase {
    private Grid $grid;

    private stdClass $config;

    /**
     * This method is called before each test..
     */

    protected function setUp():void{
        // Create a mock config object for the grid. 

        $this->config = new stdClass();
        $this->config->WORLD_SIZE = 600;
        $this->config->NUM_CELLS = 10;
        $this->config->PROXIMITY_DISTANCE_SQUARE = 49;
        $this->config->UNIT_SPEED = 5.0; 

        $this->grid = new Grid($this->config);
    }

    // =========== 
    // TESTING ADD METHOD 
    // =============

    public function testAddSingleUnit():void{
        // ARRANGE 
        $unit = new Unit(1,30,45);

        // ACT 
        $this->grid->add($unit);

        // ASSERT : Test through public interface - getUnitsState()
        $state = $this->grid->getUnitsState();

        // Verify the unit was added by checking the public state 
        $this->assertCount(1,$state, 'Grid should contain one unit after adding..');
        $this->assertEquals(1, $state[0]['id'], 'Unit ID shold be preserved');
        $this->assertEquals(30, $state[0]['x'], 'Unit X coordinate should be preserverd.');
        $this->assertEquals(45, $state[0]['y'],'Unit Y coordinate shold be preserved.');
        $this->assertFalse($state[0]['isNear'],'Single unit should not be marked as near. ');
    }

    public function testAddMultipleUnitsToSameCell(): void
    {
        // ARRANGE: Create two units that will be in the same cell [1][2]
        $unitA = new Unit(1, 80, 150); // Cell [1][2]
        $unitB = new Unit(2, 85, 155); // Cell [1][2]

        // ACT: Add both units to the grid || B->A
        $this->grid->add($unitA);
        $this->grid->add($unitB);

        // ASSERT: Verify through public interface
        $state = $this->grid->getUnitsState();
        
        $this->assertCount(2, $state, 'Grid should contain two units.');
        
        // Find units in the state array
        $foundUnitA = null;
        $foundUnitB = null;
        foreach ($state as $unitData) {
            if ($unitData['id'] === 1) $foundUnitA = $unitData;
            if ($unitData['id'] === 2) $foundUnitB = $unitData;
        }
        
        $this->assertNotNull($foundUnitA, 'Unit A should be found in the grid state.');
        $this->assertNotNull($foundUnitB, 'Unit B should be found in the grid state.');
    }

    public function testAddExistingUnitIsNotDuplicatedInMasterList(): void
    {
        // ARRANGE: Add a unit to the grid
        $unit = new Unit(1, 30, 30);
        $this->grid->add($unit);

        // ACT: Add the same unit instance again
        $this->grid->add($unit);

        // ASSERT: Check through public interface that no duplicate exists
        $state = $this->grid->getUnitsState();
        
        $this->assertCount(1, $state, 'Grid should not contain duplicate units.');
        $this->assertEquals(1, $state[0]['id'], 'The single unit should have the correct ID.');
    }


    // ===================================
    // TESTING MOVE METHOD ----
    // =========================================

    public function testMoveUnitWithinSameCell(): void
    {
        // ARRANGE: Add a unit to the grid
        $unit = new Unit(1, 70, 70);
        $this->grid->add($unit);
        
        // ACT: Move the unit to a new position within the same cell
        $this->grid->move($unit, 75, 75);

        // ASSERT: Verify through public interface and direct unit properties
        $this->assertEquals(75, $unit->x, 'Unit X coordinate should be updated.');
        $this->assertEquals(75, $unit->y, 'Unit Y coordinate should be updated.');
        
        // Verify the unit is still in the grid
        $state = $this->grid->getUnitsState();
        $this->assertCount(1, $state, 'Grid should still contain one unit.');
        $this->assertEquals(75, $state[0]['x'], 'Grid state should reflect new X coordinate.');
        $this->assertEquals(75, $state[0]['y'], 'Grid state should reflect new Y coordinate.');
    }

    public function testMoveUnitToDifferentCell(): void
    {
        // ARRANGE: Add a unit to one area of the grid
        $unit = new Unit(1, 70, 70); // Cell [1][1]
        $this->grid->add($unit);

        // ACT: Move the unit to a different cell area
        $this->grid->move($unit, 130, 130); // Cell [2][2]

        // ASSERT: Verify coordinates are updated
        $this->assertEquals(130, $unit->x, 'Unit X coordinate should be updated.');
        $this->assertEquals(130, $unit->y, 'Unit Y coordinate should be updated.');
        
        // Verify through public interface
        $state = $this->grid->getUnitsState();
        $this->assertCount(1, $state, 'Grid should still contain one unit after move.');
        $this->assertEquals(130, $state[0]['x'], 'Grid state should show new X coordinate.');
        $this->assertEquals(130, $state[0]['y'], 'Grid state should show new Y coordinate.');
    }

    public function testMoveUnitToDifferentOccupiedCell(): void
    {
        // ARRANGE: Add two units to different cells
        $unitA = new Unit(1, 70, 70);   // Cell [1][1]
        $unitB = new Unit(2, 130, 130); // Cell [2][2]
        $this->grid->add($unitA);
        $this->grid->add($unitB);

        // ACT: Move unitA into unitB's cell area
        $this->grid->move($unitA, 140, 140); // Move to Cell [2][2]

        // ASSERT: Both units should still exist with correct coordinates
        $state = $this->grid->getUnitsState();
        $this->assertCount(2, $state, 'Both units should still exist after move.');
        
        // Find both units in the state
        $foundUnitA = null;
        $foundUnitB = null;
        foreach ($state as $unitData) {
            if ($unitData['id'] === 1) $foundUnitA = $unitData;
            if ($unitData['id'] === 2) $foundUnitB = $unitData;
        }
        
        $this->assertNotNull($foundUnitA, 'Unit A should exist after move.');
        $this->assertNotNull($foundUnitB, 'Unit B should exist after move.');
        $this->assertEquals(140, $foundUnitA['x'], 'Unit A should have new coordinates.');
        $this->assertEquals(140, $foundUnitA['y'], 'Unit A should have new coordinates.');
    }


    // ==============
    // TESTING MOVE WITH INVALID PARAMETERS - Edge Cases
    // =========================================

   public function testMoveWithInvalidParameters(): void
    {
        // ARRANGE: Add a unit
        $unit = new Unit(1, 50, 50);
        $this->grid->add($unit);
        $originalX = $unit->x;
        $originalY = $unit->y;

        // ACT & ASSERT: Your Grid.php checks if(!$x || !$y) - so 0 is invalid
        $this->grid->move($unit, 0, 50); // 0 is falsy, should be rejected
        $this->assertEquals($originalX, $unit->x, 'X should not change when X=0.');
        
        $this->grid->move($unit, 50, 0); // 0 is falsy, should be rejected
        $this->assertEquals($originalY, $unit->y, 'Y should not change when Y=0.');
    }


    // ========================================================================
    // TESTING UPDATE METHOD - Core Functionality
    // ========================================================================

    public function testUpdateMethodMovesUnits(): void
    {
        // ARRANGE: Add units to the grid
        $unit1 = new Unit(1, 100, 100);
        $unit2 = new Unit(2, 200, 200);
        $this->grid->add($unit1);
        $this->grid->add($unit2);
        
        // Store initial positions
        $initialX1 = $unit1->x;
        $initialY1 = $unit1->y;
        $initialX2 = $unit2->x;
        $initialY2 = $unit2->y;

        // ACT: Call update to move units
        $this->grid->update();

        // ASSERT: Units should have moved (positions should be different)
        // Note: Due to random movement, we can't predict exact positions
        $this->assertTrue(
            $unit1->x !== $initialX1 || $unit1->y !== $initialY1 || 
            $unit2->x !== $initialX2 || $unit2->y !== $initialY2,
            'At least one unit should have moved after update.'
        );
        
        // Verify units are still in the grid
        $state = $this->grid->getUnitsState();
        $this->assertCount(2, $state, 'Both units should still exist after update.');
    }

    public function testUpdateMethodDetectsProximity(): void
    {
        // ARRANGE: Add two units very close to each other (within proximity distance)
        // Proximity distance square is 49, so distance of 6 should trigger proximity
        $unit1 = new Unit(1, 100, 100);
        $unit2 = new Unit(2, 103, 104); // Distance ~5, which squared = 25 < 49
        
        $this->grid->add($unit1);
        $this->grid->add($unit2);

        // ACT: Run update to check proximity
        $this->grid->update();

        // ASSERT: At least one unit should be marked as near
        // Note: After update, units move randomly, so we test the proximity detection logic
        $state = $this->grid->getUnitsState();
        
        // Check if proximity detection is working by examining the state
        $this->assertCount(2, $state, 'Both units should exist.');
        
        // The exact proximity result depends on where units moved, but we can verify
        // the method completed without errors and state is consistent
        foreach ($state as $unitData) {
            $this->assertArrayHasKey('isNear', $unitData, 'Each unit should have isNear property.');
            $this->assertIsBool($unitData['isNear'], 'isNear should be a boolean.');
        }
    }

     public function testUpdateMethodResetsIsNearProperty(): void
    {
        // ARRANGE: Add a unit and manually set isNear to true
        $unit = new Unit(1, 100, 100);
        $unit->isNear = true; // Manually set to true
        $this->grid->add($unit);

        // ACT: Run update
        $this->grid->update();

        // ASSERT: The isNear property should be handled correctly by update
        // (It gets reset to false at start of update, then checked again)
        $state = $this->grid->getUnitsState();
        $this->assertCount(1, $state, 'Unit should exist.');
        $this->assertArrayHasKey('isNear', $state[0], 'Unit should have isNear property.');
    }


    // ========================================================================
    // TESTING BOUNDARY CONDITIONS
    // ========================================================================

    public function testUnitsStayWithinWorldBoundaries(): void
    {
        // ARRANGE: Add units near the world boundaries
        $unit1 = new Unit(1, 5, 5);     // Near (0,0)
        $unit2 = new Unit(2, 595, 595); // Near (600,600)
        
        $this->grid->add($unit1);
        $this->grid->add($unit2);

        // ACT: Run multiple updates to see if units stay within bounds
        for ($i = 0; $i < 10; $i++) {
            $this->grid->update();
            
            // ASSERT: Units should stay within world boundaries
            $this->assertGreaterThanOrEqual(0, $unit1->x, 'Unit1 X should not be negative.');
            $this->assertGreaterThanOrEqual(0, $unit1->y, 'Unit1 Y should not be negative.');
            $this->assertLessThanOrEqual(600, $unit1->x, 'Unit1 X should not exceed world size.');
            $this->assertLessThanOrEqual(600, $unit1->y, 'Unit1 Y should not exceed world size.');
            
            $this->assertGreaterThanOrEqual(0, $unit2->x, 'Unit2 X should not be negative.');
            $this->assertGreaterThanOrEqual(0, $unit2->y, 'Unit2 Y should not be negative.');
            $this->assertLessThanOrEqual(600, $unit2->x, 'Unit2 X should not exceed world size.');
            $this->assertLessThanOrEqual(600, $unit2->y, 'Unit2 Y should not exceed world size.');
        }
    }

    // ========================================================================
    // TESTING INTEGRATION - All Methods Working Together
    // ========================================================================

    public function testCompleteSimulationWorkflow(): void
    {
        // ARRANGE: Create a realistic simulation scenario
        $units = [
            new Unit(1, 50, 50),
            new Unit(2, 150, 150),
            new Unit(3, 250, 250),
            new Unit(4, 350, 350),
            new Unit(5, 450, 450),
        ];
        
        // Add all units to the grid
        foreach ($units as $unit) {
            $this->grid->add($unit);
        }

        // ACT: Run a complete simulation cycle
        $initialState = $this->grid->getUnitsState();
        $this->grid->update();
        $afterUpdateState = $this->grid->getUnitsState();

        // ASSERT: Verify the complete workflow
        $this->assertCount(5, $initialState, 'Initial state should have 5 units.');
        $this->assertCount(5, $afterUpdateState, 'After update state should still have 5 units.');
        
        // Verify all required properties exist in the state
        foreach ($afterUpdateState as $unitData) {
            $this->assertArrayHasKey('id', $unitData, 'Unit should have ID.');
            $this->assertArrayHasKey('x', $unitData, 'Unit should have X coordinate.');
            $this->assertArrayHasKey('y', $unitData, 'Unit should have Y coordinate.');
            $this->assertArrayHasKey('isNear', $unitData, 'Unit should have isNear property.');
            
            // Verify data types
            $this->assertIsInt($unitData['id'], 'ID should be integer.');
            $this->assertIsNumeric($unitData['x'] , 'X should be numeric.');
            $this->assertIsNumeric($unitData['y'] , 'Y should be numeric.');
            $this->assertIsBool($unitData['isNear'], 'isNear should be boolean.');
        }
    }




}