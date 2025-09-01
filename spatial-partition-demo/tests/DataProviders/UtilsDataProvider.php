<?php 

namespace Tests\DataProviders;
use App\Unit;
class UtilsDataProvider {
    
        /**
     * Data provider for valid coordinate scenarios
     */
    public static function validCoordinatesProvider(): array
    {
        return [
            'origin point' => [
                /* unitX, unitY */ 0.0, 0.0,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 0, 'y' => 0]
            ],
            'center of first cell' => [
                /* unitX, unitY */ 30.0, 30.0,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 0, 'y' => 0]
            ],
            'boundary of first cell' => [
                /* unitX, unitY */ 59.9, 59.9,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 0, 'y' => 0]
            ],
            'start of second cell' => [
                /* unitX, unitY */ 60.0, 60.0,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 1, 'y' => 1]
            ],
            'middle cell' => [
                /* unitX, unitY */ 300.0, 300.0,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 5, 'y' => 5]
            ],
            'exact world boundary' => [
                /* unitX, unitY */ 600.0, 600.0,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 9, 'y' => 9] // Should clamp to last cell
            ],
            'beyond world boundary' => [
                /* unitX, unitY */ 700.0, 800.0,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 9, 'y' => 9] // Should clamp to last cell
            ],
            'fractional coordinates' => [
                /* unitX, unitY */ 125.7, 189.3,
                /* cellSize */ 60.0,
                /* numCells */ 10,
                /* expected */ ['x' => 2, 'y' => 3]
            ],
            'small world large cells' => [
                /* unitX, unitY */ 50.0, 75.0,
                /* cellSize */ 100.0,
                /* numCells */ 2,
                /* expected */ ['x' => 0, 'y' => 0]
            ],
            'large world small cells' => [
                /* unitX, unitY */ 123.0, 456.0,
                /* cellSize */ 10.0,
                /* numCells */ 100,
                /* expected */ ['x' => 12, 'y' => 45]
            ]
        ];
    }


      /**
     * Data provider for invalid parameter scenarios.
     */
    public static function invalidParametersProvider(): array
    {
        return [
            'zero cell size' => [100.0, 100.0, 0.0, 10, 'Cell size must be greater than 0, got: 0'],
            'negative cell size' => [100.0, 100.0, -50.0, 10, 'Cell size must be greater than 0, got: -50'],
            'zero num cells' => [100.0, 100.0, 60.0, 0, 'Number of cells must be greater than 0, got: 0'],
            'negative num cells' => [100.0, 100.0, 60.0, -5, 'Number of cells must be greater than 0, got: -5'],
            'negative unit x' => [-10.0, 100.0, 60.0, 10, 'Unit coordinates cannot be negative. Got x: -10, y: 100'],
            'negative unit y' => [100.0, -20.0, 60.0, 10, 'Unit coordinates cannot be negative. Got x: 100, y: -20'],
            'both negative coordinates' => [-50.0, -75.0, 60.0, 10, 'Unit coordinates cannot be negative. Got x: -50, y: -75']
        ];
    }

    /**
     * Data provider for testCalculateNewPosition.
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

    /**
     * Data provider for testCheckProximityInCell.
     */
    public static function proximityProvider(): array
    {
        // Scenario 1: Two units near, one far
        $unitA_near = new Unit(1, 10, 10);
        $unitB_near = new Unit(2, 12, 12);
        $unitC_near = new Unit(3, 0, 0);
        $unitA_near->next = $unitB_near;
        $unitB_near->prev = $unitA_near;
        $unitB_near->next = $unitC_near;
        $unitC_near->prev = $unitB_near;
        
        // Scenario 2: Two units, far from each other
        $unitA_far = new Unit(3, 10, 10);
        $unitB_far = new Unit(4, 20, 20);
        $unitA_far->next = $unitB_far;
        $unitB_far->prev = $unitA_far;

        // Scenario 3: Three units, A and B are near, C is far
        $unitA_mix = new Unit(5, 10, 10);
        $unitB_mix = new Unit(6, 11, 11);
        $unitC_mix = new Unit(7, 100, 100);
        $unitA_mix->next = $unitB_mix;
        $unitB_mix->prev = $unitA_mix;
        $unitB_mix->next = $unitC_mix;
        $unitC_mix->prev = $unitB_mix;

        // Scenario 4: Single unit in cell
        $unit_single = new Unit(8, 50, 50);

        return [
            'two units near' => [
                'head' => $unitA_near,
                'proximityDistanceSquare' => 49.0, // (7*7)
                'expectedStates' => [1 => true, 2 => true, 3 => false]
            ],
            'two units far' => [
                'head' => $unitA_far,
                'proximityDistanceSquare' => 49.0, 
                'expectedStates' => [3 => false, 4 => false]
            ],
            'three units mixed proximity' => [
                'head' => $unitA_mix,
                'proximityDistanceSquare' => 49.0, 
                'expectedStates' => [5 => true, 6 => true, 7 => false]
            ],
            'single unit' => [
                'head' => $unit_single,
                'proximityDistanceSquare' => 49.0,
                'expectedStates' => [8 => false]
            ],
            'empty (null) list' => [
                'head' => null,
                'proximityDistanceSquare' => 49.0,
                'expectedStates' => []
            ],
        ];
    }

     /**
     * Data provider for the addUnit function test.
     */
    public static function addUnitProvider(): array
    {
        // Scenario 1: Adding a unit to an empty list
        $scenario1 = [
            'unitToAdd' => new Unit(1, 10, 10),
            'head' => null
        ];

        // Scenario 2: Adding a unit to a list with a single item
        $scenario2 = [
            'unitToAdd' => new Unit(2, 20, 20),
            'head' => new Unit(1, 10, 10)
        ];

        // Scenario 3: Adding a unit to a list with multiple items
        $head3 = new Unit(1, 10, 10);
        $nextUnit3 = new Unit(2, 20, 20);
        $head3->next = $nextUnit3;
        $nextUnit3->prev = $head3;
        $scenario3 = [
            'unitToAdd' => new Unit(3, 30, 30),
            'head' => $head3
        ];
        
        return [
            'adding to empty list' => $scenario1,
            'adding to single-item list' => $scenario2,
            'adding to multi-item list' => $scenario3,
        ];
    }

    /**
     * Data provider for the unlinkUnit function test.
     */
    public static function unlinkUnitProvider(): array
    {
        // Scenario 1: Unlinking the head of a 3-node list
        $head1 = new Unit(1, 10, 10);
        $middle1 = new Unit(2, 20, 20);
        $tail1 = new Unit(3, 30, 30);
        $head1->next = $middle1;
        $middle1->prev = $head1;
        $middle1->next = $tail1;
        $tail1->prev = $middle1;
        
        // Scenario 2: Unlinking the middle of a 3-node list (reuse list structure)
        $head2 = new Unit(1, 10, 10);
        $middle2 = new Unit(2, 20, 20);
        $tail2 = new Unit(3, 30, 30);
        $head2->next = $middle2;
        $middle2->prev = $head2;
        $middle2->next = $tail2;
        $tail2->prev = $middle2;
        
        // Scenario 3: Unlinking the tail of a 3-node list (reuse list structure)
        $head3 = new Unit(1, 10, 10);
        $middle3 = new Unit(2, 20, 20);
        $tail3 = new Unit(3, 30, 30);
        $head3->next = $middle3;
        $middle3->prev = $head3;
        $middle3->next = $tail3;
        $tail3->prev = $middle3;

        // Scenario 4: Unlinking the only node in the list
        $head4 = new Unit(1, 10, 10);

        return [
            'unlinking the head' => [
                'unitToUnlink' => $head1,
                'head' => $head1
            ],
            'unlinking a middle node' => [
                'unitToUnlink' => $middle2,
                'head' => $head2
            ],
            'unlinking the tail' => [
                'unitToUnlink' => $tail3,
                'head' => $head3
            ],
            'unlinking the only node' => [
                'unitToUnlink' => $head4,
                'head' => $head4
            ]
        ];
    }


}
 