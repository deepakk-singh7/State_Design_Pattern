<?php
require_once 'GameObject.php';

/**
 * Represents a single "Sparkle" effect.
 */
class Particle implements GameObject {
    // A counter shared by all Particle instances to ensure unique IDs.
    private static int $idCounter = 0;
    // The unique ID for this specific particle instance.
    private int $id;
    // The internal countdown timer. When this hits 0, the particle is "dead".
    private int $framesLeft = 0;
    
    // Public properties that define the particle's state when it's alive.
    public float $x, $y, $xVel, $yVel, $size;
    public string $color;

    // A link to the next available particle in the pool's free list.
    private ?GameObject $next = null;

    /**
     * Constructor is called only once when the pool is first created.
     * It assigns a permanent, unique ID to this object instance.
     */
    public function __construct() {
        $this->id = self::$idCounter++;
    }
    
    /**
     * Resets the particle to its inactive state.
     * @return void
     */
    public function reset(): void {
        $this->framesLeft = 0;
    }

    /**
     * Brings a recycled particle to life with a new position, random velocity, and lifetime.
     * @param  ...$args 
     * @return void
     */
    public function init(...$args): void {
        $this->x = $args[0];
        $this->y = $args[1];
        $this->xVel = (rand(-100, 100) / 100.0);
        $this->yVel = (rand(-100, 100) / 100.0) - 1.5; // Give it an upward thrust
        $this->framesLeft = rand(60, 120);
        $this->size = rand(2, 4);
        $this->color = "rgba(255, 223, 186, 0.8)";
    }

    /**
     * The particle's per-frame logic. Returns true on the frame it dies to signal the pool to recycle it.
     * @return bool
     */
    public function animate(): bool {
        if (!$this->inUse()) return false;
        $this->framesLeft--;
        return $this->framesLeft === 0;
    }

    /**
     * A particle is "in use" if its internal countdown timer is greater than 0.
     */
    public function inUse(): bool {
        return $this->framesLeft > 0;
    }

    public function getId(): int {
        return $this->id;
    }
    
    /**
     * Bundles its properties into a data packet for frontend.
     */
    public function getRenderData(): array {
        return [
            'id' => $this->id,
            'type' => ObjectType::PARTICLE,
            'x' => $this->x,
            'y' => $this->y,
            'size' => $this->size,
            'color' => $this->color,
            'xVel' => $this->xVel,
            'yVel' => $this->yVel,
            // This 'lifetime' is an instruction for the frontend's animation loop.
            'lifetime' => 120 
        ];
    }
    
    public function getNext(): ?GameObject {
        return $this->next;
    }

    public function setNext(?GameObject $next): void {
        $this->next = $next;
    }
}