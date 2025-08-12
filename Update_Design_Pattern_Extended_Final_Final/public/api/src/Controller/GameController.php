<?php
/**
 * Handles HTTP requests related to the game, using GameService for logic.
 * This class doesn't know or care how GameService was loaded.
 */
class GameController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function tick(): void
    {
        $world = $this->gameService->getWorld();
        $world->tick(FIXED_TIMESTEP);
        $this->gameService->saveState($world);

        $entitiesData = [];
        foreach ($world->getEntities() as $entity) {
            $entitiesData[] = $entity->getState();
        }

        $this->sendJsonResponse([
            'frame' => $this->gameService->getFrameCount(),
            'entities' => $entitiesData,
            'timestamp' => microtime(true)
        ]);
    }

    public function reset(): void
    {
        $this->gameService->reset();
        $this->sendJsonResponse(['status' => 'reset']);
    }

    private function sendJsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}