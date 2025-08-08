<?php 
require_once 'Car.php';

// The main Game class.
class Game {
    // Game Constants
    private const UPDATES_PER_SECOND = 50;
    private const MS_PER_UPDATE = 1000 / self::UPDATES_PER_SECOND; // This is our fixed 20ms time step.

    private Car $car;

    public function __construct() {
        // When a Game is created, it creates its objects, like the car.
        $this->car = new Car();
    }

    /**
     * Runs the main game loop demonstration.
     * @param int $simulationTimeMs How long to run the simulation for (in milliseconds).
     * @param int $frameTimeMs The simulated time it takes to process and render one frame.
     */
    public function run(int $simulationTimeMs, int $frameTimeMs): void {
        $lag = 0.0;
        $previousTime = getCurrentTimeMillis();
        $totalTimeSimulated = 0;

        while ($totalTimeSimulated < $simulationTimeMs) {
            $currentTime = getCurrentTimeMillis();
            $elapsed = $currentTime - $previousTime;
            $previousTime = $currentTime;
            $lag += $elapsed;
            $totalTimeSimulated += $elapsed;

            echo "\n--- New Frame (Elapsed Real Time: " . number_format($elapsed, 2) . "ms, Lag: " . number_format($lag, 2) . "ms) ---\n";

            // In a real game, we would process input here.
            // processInput();

            // The inner "catch-up" loop. It calls the update method on game objects.
            while ($lag >= self::MS_PER_UPDATE) {
                $this->car->update(self::MS_PER_UPDATE / 1000.0); // Pass time step in seconds
                $lag -= self::MS_PER_UPDATE;
            }

            // After catching up, calculate the interpolation factor from the leftover lag.
            $interpolation = $lag / self::MS_PER_UPDATE;
            
            // Pass the interpolation factor to the render method.
            $this->car->render($interpolation, self::MS_PER_UPDATE / 1000.0);

            // Simulate the time this frame took to render by sleeping.
            usleep($frameTimeMs * 1000);
        }
    }
}