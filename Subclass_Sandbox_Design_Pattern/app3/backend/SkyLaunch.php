<?php
require_once __DIR__ . "/Superpower.php";

class SkyLaunch extends Superpower {
    protected function activate(): array {
        $log = [];
        $currentHeight = $this->getHeroZ();

        if ($currentHeight == 0) {
            $log[] = "--- SkyLaunch from ground ---";
            $log[] = $this->playSound("spring_jump.wav");
            $log[] = $this->spawnParticles("dust_cloud");
            $log[] = $this->move(0, 0, 20); // shoot upwards
        }
        elseif ($currentHeight < 10) {
            $log[] = "--- SkyLaunch: Double Jump ---";
            $log[] = $this->playSound("whoosh.wav");
            $log[] = $this->move(0, 0, 15); // Add height
        }
        else {
            $log[] = "--- SkyLaunch: Dive Attack ---";
            $log[] = $this->playSound("dive_boom.wav");
            $log[] = $this->spawnParticles("spark_trail");
            $log[] = $this->move(0, 0, -$currentHeight); // land safely
        }

        return $log;
    }
}
