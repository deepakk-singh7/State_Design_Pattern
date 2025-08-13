<?php 
require_once 'Superpower.php';

class SkyLaunch extends Superpower {
    protected function activate(): void {
        echo "--- Activating SkyLaunch ---\n";
        $currentHeight = $this->getHeroZ();

        if ($currentHeight == 0) {
            // On the ground, so do a super jump.
            echo "Hero is on the ground. Performing a super jump!\n";
            $this->playSound("SOUND_SPROING", 1.0);
            $this->spawnParticles("PARTICLE_DUST", 10);
            $this->move(0, 0, 20);

        } else if ($currentHeight < 10.0) {
            // Near the ground, so do a double jump.
            echo "Hero is near the ground. Performing a double jump!\n";
            $this->playSound("SOUND_SWOOP", 1.0);
            $this->move(0, 0, 20); // Add 20 more to current height

        } else {
            // Way up in the air, so do a dive attack.
            echo "Hero is high in the air. Performing a dive attack!\n";
            $this->playSound("SOUND_DIVE", 0.7);
            $this->spawnParticles("PARTICLE_SPARKLES", 1);
            // Move back down to the ground (move by the negative of the current height)
            $this->move(0, 0, -$currentHeight);
        }
    }
}