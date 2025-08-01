<?php 

require_once 'TimerGame_ObserverPattern/GameTimer.php';
require_once 'TimerGame_ObserverPattern/UIDisplay.php';
require_once 'TimerGame_ObserverPattern/AudioSystem.php';

// creating subject / observant
$gameTimer = new GameTimer(16);

// var_dump($gameTimer);

// creating observers...

$UIDisplay = new UIDisplay('Deepak');
$audioSystem = new AudioSystem(0.9);

// register the observers...

$gameTimer->addObserver($UIDisplay);
$gameTimer->addObserver($audioSystem);
// var_dump($gameTimer) . '</br>';
echo 'Starting the Timer Game...' . '</br>';

$gameTimer->start();

echo 'Finished....' . '</br>';
