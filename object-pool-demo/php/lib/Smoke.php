<?php
require_once 'GameObject.php';

/**
 * Represents a single "Puff of Smoke" effect.
 */
class Smoke implements GameObject {
    private static int $idCounter = 0;
    private int $id;
    private int $framesLeft = 0;
    
    public float $x, $y, $size;
    public string $color;

    private ?GameObject $next = null;
    
    public function __construct() {
        $this->id = self::$idCounter++;
    }
    
    /**
     * Resets the smoke puff to its inactive state.
     */
    public function reset(): void {
        $this->framesLeft = 0;
    }

    /**
     * Brings a recycled smoke puff to life.
     * It includes a check to prevent errors if called with no arguments.
     */
    public function init(...$args): void {
        if (empty($args)) {
            $this->reset();
            return;
        }
        $this->x = $args[0];
        $this->y = $args[1];
        $this->framesLeft = rand(100, 150);
        $this->size = rand(5, 10);
        $this->color = "rgba(128, 128, 128, 0.5)";
    }

    /**
     * The smoke's per-frame logic. Returns true when its life is over.
     */
    public function animate(): bool {
        // ... (animate logic is the same) ...
        if (!$this->inUse()) return false;
        $this->framesLeft--;
        return $this->framesLeft === 0;
    }

    public function inUse(): bool {
        return $this->framesLeft > 0;
    }
    
    public function getId(): int {
        return $this->id;
    }

     /**
     * Bundles its properties for the frontend.
     * Note: It sends a hardcoded velocity to JS, defining its upward drift.
     */
    public function getRenderData(): array {
        return [
            'id' => $this->id,
            'type' => ObjectType::SMOKE,
            'x' => $this->x,
            'y' => $this->y,
            'size' => $this->size,
            'color' => $this->color,
            'xVel' => 0,
            'yVel' => -0.5, // Move smoke up slowly
            'lifetime' => 150
        ];
    }

    public function getNext(): ?GameObject {
        return $this->next;
    }

    public function setNext(?GameObject $next): void {
        $this->next = $next;
    }
}