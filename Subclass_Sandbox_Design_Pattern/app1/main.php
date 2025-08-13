<?php 
require_once 'src/Hero.php';
require_once 'src/SkyLaunch.php';

$hero = new Hero();
$skyLaunch = new SkyLaunch($hero);

// 1. Use the power when on the ground
echo "Scenario 1: Hero is on the ground (Z=0).\n";
$skyLaunch->use();
echo "\n========================================\n\n";

// 2. Use the power again when already in the air (but low)
echo "Scenario 2: Hero is low in the air (Z=20).\n";
$skyLaunch->use(); // This will trigger the "double jump" logic
echo "\n========================================\n\n";

// 3. Use the power when high in the air
echo "Scenario 3: Hero is high in the air (Z=40).\n";
$skyLaunch->use(); // This will trigger the "dive attack" logic
echo "\n========================================\n\n";

// 4. Verify hero is back on the ground and use the power again
echo "Scenario 4: Hero is back on the ground after the dive.\n";
$skyLaunch->use(); // This will trigger the initial super jump again
echo "\n";