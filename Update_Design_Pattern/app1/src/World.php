<?php 

require_once 'Entity.php';
class World
{
    private array $entities = [];

    public function addEntity(Entity $entity): void
    {
        $this->entities[] = $entity;
    }

    public function gameLoop(): void
    {
        while (true) {
            // 1. In a real game, you would handle user input here.

            // 2. Update each entity (This is the Update Method pattern in action!)
            foreach ($this->entities as $entity) {
                $entity->update();
            }

            // 3. In a real game, you would handle physics and rendering here.
            
            // To prevent this from running too fast and locking up the console,
            // we'll simulate frames passing.
            echo "--- End of Frame ---\n";
            sleep(1);
        }
    }
}