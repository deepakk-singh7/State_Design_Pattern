<?php
/**
 * Mocked audio system.
 */
class AudioSystem {
    /**
     * Simulate playing a sound in the engine.
     * @param string $soundId Logical sound identifier (not a filepath).
     * @return string Human-readable log line for frontend display.
     */
    public static function playSound(string $soundId) {
        return "Playing sound: {$soundId}";
    }
}
