<?php
/**
 * The single entry point for all backend API requests from the frontend.
 * This script handles object creation, status updates, and object recycling.
 */

// Set the HTTP header to ensure all output is treated as JSON by the browser.
header('Content-Type: application/json');

/**
 * A simple autoloader to dynamically include class files from the /lib directory
 */
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/lib/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Start or resume the PHP session to maintain the state of our pools between requests.
session_start();

// Use the session to persist the PoolManager instance.
if (!isset($_SESSION['pool_manager'])) {
    $_SESSION['pool_manager'] = PoolManager::getInstance();
}
$poolManager = $_SESSION['pool_manager'];

// Determine the requested action from the URL query string (e.g., ?action=create).
$action = $_GET['action'] ?? null;
// Prepare a default error response in case the action is invalid.
$response = ['success' => false, 'message' => 'Invalid action.'];

// Route the request to the appropriate logic based on the action.
switch ($action) {
    /**
     * Handles requests to create one or more new objects from a specified pool.
     */
    case ApiAction::CREATE:
        // Sanitize and retrieve parameters from the URL.
        $type = $_GET['type'] ?? null;
        $count = (int)($_GET['count'] ?? 1);
        $x = (float)($_GET['x'] ?? 400);
        $y = (float)($_GET['y'] ?? 200);

        $pool = $poolManager->getPool($type);
        if ($pool) {
            $createdObjects = [];
            // Loop to create the requested number of objects.
            for ($i = 0; $i < $count; $i++) {
                $obj = $pool->create($x, $y);
                if ($obj) {
                    // If an object was successfully created, get its render data.
                    $createdObjects[] = $obj->getRenderData();
                }
            }
            $response = ['success' => true, 'objects' => $createdObjects];
        } else {
            $response['message'] = 'Invalid object type specified.';
        }
        break;

    /**
     * Handles notifications from the frontend that objects have expired.
     */
    case ApiAction::RETURN_OBJECTS:
        $data = json_decode(file_get_contents('php://input'), true);
        $type = $data['type'] ?? null;
        $ids = $data['ids'] ?? [];

        $pool = $poolManager->getPool($type);
        if ($pool && !empty($ids)) {
            $returnedCount = 0;
            // Loop through each expired ID sent by the frontend.
            foreach ($ids as $id) {
                // Find the actual object instance using its ID.
                $objectToReturn = $pool->getObjectById((int)$id);
                if ($objectToReturn) {
                    // If found, return it to the pool.
                    $pool->returnObject($objectToReturn);
                    $returnedCount++;
                }
            }
            $response = ['success' => true, 'message' => "Returned {$returnedCount} objects."];
        } else {
            $response['message'] = 'Invalid type or no IDs provided.';
        }
        break;
        
    /**
     * Handles requests for the current status of all pools.
     */
    case ApiAction::STATUS:
        $statuses = [];
        // Loop through all pools managed by the manager.
        foreach($poolManager->getAllPools() as $type => $pool) {
            // Get the status of each pool and add it to our response.
            $statuses[$type] = $pool->getStatus();
        }
        $response = ['success' => true, 'statuses' => $statuses];
        break;
}

//  Save the (potentially modified) PoolManager state back into the session.
$_SESSION['pool_manager'] = $poolManager;

// Send the final JSON response back to the browser.
echo json_encode($response);