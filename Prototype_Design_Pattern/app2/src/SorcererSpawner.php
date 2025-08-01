<?php 

require_once 'Sorcerer.php';
require_once 'SpawnerInterface.php';

class SorcererSpawner implements SpawnerInterface{
    public function spawnMonster(){
        return new Sorcerer(12, 2, 10); // fixed values - not flexible
    }
}