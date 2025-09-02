<?php
/**
 * Main executable script to run the dirty flag (lazy evaluation) simulation.
 */

// Class Loading 
require_once __DIR__ . '/src/Config/MessageConstants.php';
require_once __DIR__ . '/src/Enums/ObjectName.php';
require_once __DIR__ . '/src/Enums/Transform.php';
require_once __DIR__ . '/src/Scene/SceneObjectDirtyFlag.php';

// Namespace Imports
use App\Config\MessageConstants;
use App\Enums\ObjectName;
use App\Enums\Transform;
use App\Scene\SceneObjectDirtyFlag;

// Scene Setup
// Ship -> Nest -> Pirate -> Parrot
$ship = new SceneObjectDirtyFlag(ObjectName::SHIP, Transform::SHIP_AT_SEA);
$nest = new SceneObjectDirtyFlag(ObjectName::NEST, Transform::NEST_ON_MAST);
$pirate = new SceneObjectDirtyFlag(ObjectName::PIRATE, Transform::PIRATE_IN_NEST);
$parrot = new SceneObjectDirtyFlag(ObjectName::PARROT, Transform::PARROT_ON_SHOULDER);

$ship->addChild($nest);
$nest->addChild($pirate);
$pirate->addChild($parrot);

echo MessageConstants::SIMULATION_HEADER;

//  Simulation of a Single Frame
$ship->setLocalTransform(Transform::SHIP_MOVED);
$nest->setLocalTransform(Transform::NEST_MOVED);
$pirate->setLocalTransform(Transform::PIRATE_MOVED);
$parrot->setLocalTransform(Transform::PARROT_MOVED);

echo MessageConstants::RENDER_HEADER;

// Rendering

$finalPosition = $parrot->getWorldTransform();
echo sprintf(MessageConstants::FINAL_POSITION, $finalPosition);

echo MessageConstants::SEPARATOR;
echo sprintf(MessageConstants::TOTAL_CALCULATIONS, SceneObjectDirtyFlag::$calculationCount);
echo MessageConstants::SEPARATOR;