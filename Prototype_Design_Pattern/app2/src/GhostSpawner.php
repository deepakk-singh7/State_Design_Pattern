<?php 

require_once 'Ghost.php';
require_once 'SpawnerInterface.php';
class GhostSpawner implements SpawnerInterface{
    public function spawnMonster():Ghost{
        return new Ghost(15,3); // fixed values - not flexible
    }
}