<?php 
// it hold a prototype and create new monsters by cloning it.. 
require_once 'CloneableMonster.php';
class MonsterSpawner {
    private CloneableMonster $prototype; // for holding a template monster
    private int $spawnCount = 0;

    public function __construct(CloneableMonster $prototype) {
        $this->prototype = $prototype;
        echo "✓ Spawner created with prototype: " . $prototype->getInfo() . "\n";
    }
    
    // creates new monsters by cloning the prototype
    public function spawnMonster():CloneableMonster {
        $this->spawnCount++;
        $newMonster = $this->prototype->cloneMonster();
        echo "  → Spawned monster #{$this->spawnCount}: " . $newMonster->getInfo() . "\n";
        return $newMonster;
    }
        public function getPrototypeInfo() {
        return "Spawns: " . $this->prototype->getInfo();
    }
    
    public function getSpawnCount() {
        return $this->spawnCount;
    }
}