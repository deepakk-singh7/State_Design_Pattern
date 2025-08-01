<?php

require_once 'SpawnerInterface.php';
require_once('Demon.php');
class DemonSpawner implements SpawnerInterface{
    public function spawnMonster():Demon{
        return new Demon(20, 4, 8); //// fixed values - not flexible
    }
}