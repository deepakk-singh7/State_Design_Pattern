<?php 

// Simulates the main game engine's API that spells can call.
class Game
{
    public function setHealth(int $wizardId, int $amount): void
    {
        echo "-> Setting health for wizard {$wizardId} to {$amount}.\n";
    }

    public function setWisdom(int $wizardId, int $amount): void
    {
        echo "-> Setting wisdom for wizard {$wizardId} to {$amount}.\n";
    }

    public function setAgility(int $wizardId, int $amount): void
    {
        echo "-> Setting agility for wizard {$wizardId} to {$amount}.\n";
    }

    public function playSound(int $soundId): void
    {
        echo "-> Playing sound {$soundId}.\n";
    }

    public function spawnParticles(int $particleType): void
    {
        echo "-> Spawning particle type {$particleType}.\n";
    }
}