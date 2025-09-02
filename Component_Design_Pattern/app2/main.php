<?php 
require_once 'GameObject.php';
require_once 'GraphicsComponent.php';
require_once 'PhysicsComponent.php';


// --- Assemble our objects---

// 1. A Decoration has only graphics
echo "Creating a Decoration (Bush):\n";
$bush = new GameObject();
$bush->addComponent(new GraphicsComponent());
$bush->update(); // Will only call the graphics component's update

echo "\n---\n\n";

// 2. A Zone has only physics
echo "Creating a Zone (Trigger Area):\n";
$trigger = new GameObject();
$trigger->addComponent(new PhysicsComponent());
$trigger->update(); // Will only call the physics component's update

echo "\n---\n\n";

// 3. A Prop has both graphics and physics!
echo "Creating a Prop (Crate):\n";
$crate = new GameObject();
$crate->addComponent(new GraphicsComponent());
$crate->addComponent(new PhysicsComponent());
$crate->update(); // Will call the update method on BOTH components