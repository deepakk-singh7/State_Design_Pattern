<?php
require_once __DIR__ . "/Superpower.php";

class IceBlast extends Superpower {
    protected function activate(): array {
        $log = [];
        $log[] = "IceBlast activated!";
        $log[] = $this->playSound("ice_crack.wav");
        $log[] = $this->spawnParticles("ice_shards");
        $log[] = $this->move(0, 5, 0); // move sideways
        return $log;
    }
}
