<?php 

namespace Tests\DataProviders;

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
}
 