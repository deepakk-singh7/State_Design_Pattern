<?php

session_start();
header('Content-Type: application/json');

require_once 'src/Grid.php';
require_once 'src/ApiActions.php';

// Read the configuration file once.
$configFile = 'config.json';
if (!file_exists($configFile)) {
    // Set a 500 Internal Server Error status code.
    http_response_code(500);
    // Send a clear error message as JSON and stop execution.
    echo json_encode(['error' => 'Configuration file not found.']);
    exit;
}

// 2. Read the file and decode the JSON.
$config = json_decode(file_get_contents($configFile));

// 3. Check if JSON decoding was successful.
if ($config === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON in configuration file.']);
    exit;
}

// Router based on the 'action' GET parameter.
$action = $_GET[ApiActions::ACTION] ?? ApiActions::UPDATE;

switch ($action) {
    // The 'init' action creates a new simulation.
    case ApiActions::INIT:
        $grid = new Grid($config);
        /// Create 150 units at random positions for the larger world.
        for ($i = 0; $i < $config->UNIT_COUNT; $i++) {
            $unit = new Unit($i, rand(0, $config->WORLD_SIZE), rand(0, $config->WORLD_SIZE));
            $grid->add($unit);
        }
        // Serialize and save the entire Grid object in the session.
        $_SESSION[ApiActions::GRID] = serialize($grid);
        // Send the initial state of the units back to the frontend.
        echo json_encode([
        'config' => $config,
        'units' => $grid->getUnitsState()
    ]);
        break;

    // The 'update' action advances the simulation by one frame.
    case ApiActions::UPDATE:
        // Check if a simulation exists in the session.
        if (isset($_SESSION[ApiActions::GRID])) {
            // Unserialize the Grid object from the session to restore its state.
            $grid = unserialize($_SESSION[ApiActions::GRID]);
            // Run one frame of the simulation.
            $grid->update();
            // Re-serialize and save the updated state back to the session.
            $_SESSION[ApiActions::GRID] = serialize($grid);
            // Send the new state back to the frontend.
            echo json_encode(['units' => $grid->getUnitsState()]);
        }
        break;
}