<?php
require_once 'enums/EntityType.php';    
require_once 'enums/GameConstants.php';  
class Game implements JsonSerializable
{
    /** @var GameObject[] The list of all objects currently in the game. */
    public array $objects = [];
    private CollisionSystem $collisionSystem;

    public function __construct()
    {
        $this->collisionSystem = new CollisionSystem();
        $this->reset(); // Initialize the game on creation.
    }

    /**
     * This method contains the logic from initializeGameState().
     * It resets the game to its starting state.
     */
    public function reset(): void
    {
        // $player = new GameObject('player', 180, 180);
        $player = new GameObject(EntityType::Player->value, 180, 180);
        $player->health = 10; 
        $player->addComponent(new RenderDataComponent(GameConstants::PLAYER_COLOR, 30));
        $player->addComponent(new MovementComponent());
        $player->addComponent(new HealthComponent());

        // $healthPack = new GameObject('health_pack', rand(20, 360), rand(20, 360));
        // Create health pack
        $healthPack = new GameObject(EntityType::HealthPack->value, rand(20, 360), rand(20, 360));
        $healthPack->addComponent(new RenderDataComponent(GameConstants::HEALTH_PACK_COLOR, 20));
        $healthPack->addComponent(new HealthPackComponent(25)); // Heals 25 HP

        // Create poison pack
        $poisonPack = new GameObject(EntityType::PoisonPack->value, rand(20, 360), rand(20, 360));
        $poisonPack->addComponent(new RenderDataComponent(GameConstants::POISON_PACK_COLOR, 20));
        $poisonPack->addComponent(new PoisonPackComponent(20)); // Damages 20 HP

        // Create speed boost pack
        $speedBoost = new GameObject(EntityType::SpeedBoost->value, rand(20, 360), rand(20, 360));
        $speedBoost->addComponent(new RenderDataComponent(GameConstants::SPEED_BOOST_COLOR, 20));
        $speedBoost->addComponent(new SpeedBoostComponent());

        $this->objects = [$player, $healthPack, $poisonPack, $speedBoost]; // $speedBoost
    }

    /**
     * Finds the player object in the game world.
     * @return GameObject|null The player object if found, otherwise null.
     */
    private function getPlayer(): ?GameObject
    {
        foreach ($this->objects as $object) {
            if ($object->id === EntityType::Player->value) {
                return $object;
            }
        }
        return null;
    }

    /**
     * Processes a player movement action by delegating to the MovementComponent.
     * @param Direction $direction The direction enum to move ('up', 'down', 'left', 'right').
     */
    public function movePlayer(Direction $direction): void
    {
        $player = $this->getPlayer();
        if ($player) {
            $movementComponent = $player->getComponent(MovementComponent::class);
            if ($movementComponent instanceof MovementComponent) {
                $movementComponent->move($player, $direction);
            }
        }
    }

    /**
     * Runs all the game's systems for this "tick", like collision detection.
     */
    public function update(): void
    {
        $this->collisionSystem->process($this->objects);
    }

    /**
     * Defines how the Game object should be converted to JSON for the client.
     * The client only needs the list of objects to render.
     */
    public function jsonSerialize(): mixed
    {
        return $this->objects;
    }
}