<?php
/**
 * Manages the core game state, abstracting session and world logic.
 */
class GameService
{
    private array $session;

    public function __construct(array &$session)
    {
        $this->session = &$session;
    }

    public function getWorld(): World
    {
        if (!isset($this->session['world'])) {
            $this->initializeWorld();
        }
        return unserialize($this->session['world']);
    }

    public function saveState(World $world): void
    {
        $this->session['world'] = serialize($world);
        $this->session['frame']++;
    }

    public function reset(): void
    {
        session_unset();
        session_destroy();
    }
    
    public function getFrameCount(): int
    {
        return $this->session['frame'] ?? 0;
    }

    private function initializeWorld(): void
    {
        $world = new World();
        $world->addEntity(new Skeleton(10, 50));
        $world->addEntity(new Statue(90, 20));
        $world->addEntity(new Spawner(5, 5, 1));

        $this->session['world'] = serialize($world);
        $this->session['frame'] = 0;
    }
}