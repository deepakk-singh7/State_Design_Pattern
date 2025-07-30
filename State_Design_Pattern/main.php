<?php 

require_once 'src/HeroineV1.php';
require_once 'src/input.php';

echo "Staring ...." . PHP_EOL;

// Creating the context object
$heroine = new HeroineV1();

// var_dump($heroine);

$heroine->handleInput(Input::PRESS_DOWN); // Should Ducking
$heroine->update(); // Increase chargeTime
$heroine->update(); // Increase chargeTime
$heroine->update(); // ready for attack
$heroine->handleInput(Input::PRESS_B); // Should do nothing, is ducking
$heroine->handleInput(Input::RELEASE_DOWN); // Should stand up
$heroine->handleInput(Input::PRESS_B); // Should jump
$heroine->handleInput(Input::PRESS_DOWN); // Should dive
