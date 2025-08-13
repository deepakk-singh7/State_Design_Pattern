<?php
/**
 * Mocked particle system.
 */
class ParticleSystem {
    /**
     * Simulate spawning a particle effect.
     * @param string $particleType Logical particle effect identifier.
     * @return string Human-readable log line for frontend display.
     */
    public static function spawn(string $particleType) {
        return "Spawning particles: {$particleType}";
    }
}
