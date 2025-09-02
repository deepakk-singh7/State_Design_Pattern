<?php
/**
 * Represents a single pending request to play a sound.
 */
class PlayMessage {
    public function __construct(
        public SoundId $soundId, 
        public int $volume
    ) {}
}