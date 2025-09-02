<?php

// Include all the necessary class and enum files.
require_once __DIR__ . '/src/Config/MessageConstants.php';
require_once __DIR__ . '/src/Enums/ObjectName.php';
require_once __DIR__ . '/src/Enums/Transform.php';
require_once __DIR__ . '/src/Scene/SceneObjectEager.php';

// The 'use' statements work because the files have been included above.
use App\Config\MessageConstants;
use App\Enums\ObjectName;
use App\Enums\Transform;
use App\Scene\SceneObjectEager;

// --- Scene SETUP ---
$ship = new SceneObjectEager(ObjectName::SHIP, Transform::SHIP_AT_SEA);
$nest = new SceneObjectEager(ObjectName::NEST, Transform::NEST_ON_MAST);
$pirate = new SceneObjectEager(ObjectName::PIRATE, Transform::PIRATE_IN_NEST);
$parrot = new SceneObjectEager(ObjectName::PARROT, Transform::PARROT_ON_SHOULDER);

// Build the hierarchy by linking them in parent-child relationships.
// Ship -> Nest -> Pirate -> Parrot
$ship->addChild($nest);
$nest->addChild($pirate);
$pirate->addChild($parrot);

echo MessageConstants::SIMULATION_HEADER;

// Simulation of a Single Frame

// --- ACTIONS ---
$ship->setLocalTransform(Transform::SHIP_MOVED);
$nest->setLocalTransform(Transform::NEST_MOVED);
$pirate->setLocalTransform(Transform::PIRATE_MOVED);
$parrot->setLocalTransform(Transform::PARROT_MOVED);

echo MessageConstants::RENDER_HEADER;

// --- RENDER --- [ We only need the final position of the parrot]
$finalPosition = $parrot->getWorldTransform();
echo sprintf(MessageConstants::FINAL_POSITION, $finalPosition);

echo MessageConstants::SEPARATOR;
echo sprintf(MessageConstants::TOTAL_CALCULATIONS, SceneObjectEager::$calculationCount);
echo MessageConstants::SEPARATOR;