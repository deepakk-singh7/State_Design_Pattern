<?php

/**
 * The main application driver for the API.
 * It handles session state, routing requests, and sending JSON responses.
 */
class ApiDriver {
    /**
     * @var PoolManager The single instance of the PoolManager, retrieved from the session.
     */
    private PoolManager $poolManager;

    /**
     * ApiDriver constructor.
     * Initializes the session and loads the PoolManager state.
     */
    public function __construct() {
        session_start();
        if (!isset($_SESSION[ApiParams::SESSION_KEY])) {
            $_SESSION[ApiParams::SESSION_KEY] = PoolManager::getInstance();
        }
        $this->poolManager = $_SESSION[ApiParams::SESSION_KEY];
    }

    /**
     * The main entry point for handling an incoming web request.
     * It routes the request to the correct handler and sends the response.
     * @return void
     */
    public function handleRequest(): void {
        $action = $_GET[ApiParams::ACTION] ?? null;
        $response = ['success' => false, 'message' => 'Invalid action.'];

        switch ($action) {
            case ApiAction::CREATE:
                $response = $this->handleCreate();
                break;
            case ApiAction::RETURN_OBJECTS:
                $response = $this->handleReturnObjects();
                break;
            case ApiAction::STATUS:
                $response = $this->handleStatus();
                break;
        }

        $this->saveState();
        $this->sendResponse($response);
    }

    /**
     * Handles the logic for creating new objects from a pool.
     * @return array The response data to be sent as JSON.
     */
    private function handleCreate(): array {
        $type = $_GET[ApiParams::TYPE] ?? null;
        $count = (int)($_GET[ApiParams::COUNT] ?? 1);
        $x = (float)($_GET[ApiParams::X] ?? 400);
        $y = (float)($_GET[ApiParams::Y] ?? 200);

        $pool = $this->poolManager->getPool($type);
        if (!$pool) {
            return ['success' => false, 'message' => 'Invalid object type specified.'];
        }

        $createdObjects = [];
        for ($i = 0; $i < $count; $i++) {
            $obj = $pool->create($x, $y);
            if ($obj) {
                $createdObjects[] = $obj->getRenderData();
            }
        }
        return ['success' => true, 'objects' => $createdObjects];
    }

    /**
     * Handles the logic for returning expired objects to a pool.
     * @return array The response data to be sent as JSON.
     */
    private function handleReturnObjects(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        $type = $data[ApiParams::TYPE] ?? null;
        $ids = $data[ApiParams::IDS] ?? [];

        $pool = $this->poolManager->getPool($type);
        if (!$pool || empty($ids)) {
            return ['success' => false, 'message' => 'Invalid type or no IDs provided.'];
        }

        $returnedCount = 0;
        foreach ($ids as $id) {
            $objectToReturn = $pool->getObjectById((int)$id);
            if ($objectToReturn) {
                $pool->returnObject($objectToReturn);
                $returnedCount++;
            }
        }
        return ['success' => true, 'message' => "Returned {$returnedCount} objects."];
    }

    /**
     * Handles the logic for retrieving the status of all pools.
     * @return array The response data to be sent as JSON.
     */
    private function handleStatus(): array {
        $statuses = [];
        foreach ($this->poolManager->getAllPools() as $type => $pool) {
            $statuses[$type] = $pool->getStatus();
        }
        return ['success' => true, 'statuses' => $statuses];
    }

    /**
     * Saves the current state of the PoolManager back into the session.
     * @return void
     */
    private function saveState(): void {
        $_SESSION[ApiParams::SESSION_KEY] = $this->poolManager;
    }

    /**
     * Encodes the response data into JSON and sends it to the browser.
     * @param array $data The final response data.
     * @return void
     */
    private function sendResponse(array $data): void {
        echo json_encode($data);
    }
}