<?php

session_start();
header('Content-Type: application/json');

require_once 'src/Grid.php';

// Router based on the 'action' GET parameter.
$action = $_GET['action'] ?? 'update';

switch ($action) {
    // The 'init' action creates a new simulation.
    case 'init':
        $grid = new Grid();
        /// Create 150 units at random positions for the larger world.
        for ($i = 0; $i < 150; $i++) {
            new Unit($grid, rand(0, Grid::WORLD_SIZE), rand(0, Grid::WORLD_SIZE));
        }
        // Serialize and save the entire Grid object in the session.
        $_SESSION['grid'] = serialize($grid);
        // Send the initial state of the units back to the frontend.
        echo json_encode($grid->getUnitsState());
        break;

    // The 'update' action advances the simulation by one frame.
    case 'update':
        // Check if a simulation exists in the session.
        if (isset($_SESSION['grid'])) {
            // Unserialize the Grid object from the session to restore its state.
            $grid = unserialize($_SESSION['grid']);
            // Run one frame of the simulation.
            $grid->update();
            // Re-serialize and save the updated state back to the session.
            $_SESSION['grid'] = serialize($grid);
            // Send the new state back to the frontend.
            echo json_encode($grid->getUnitsState());
        }
        break;
}