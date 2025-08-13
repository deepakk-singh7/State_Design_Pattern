<?php
header("Content-Type: application/json");

require_once __DIR__ . "/Fireball.php";
require_once __DIR__ . "/IceBlast.php";
require_once __DIR__ . "/SkyLaunch.php";

session_start();
if (!isset($_SESSION['hero'])) {
    $_SESSION['hero'] = serialize(new Hero()); // Initial  state
}
$hero = unserialize($_SESSION['hero']);

$power = $_GET['power'] ?? '';

switch ($power) {
    case "fireball":
        $ability = new Fireball($hero);
        break;
    case "iceblast":
        $ability = new IceBlast($hero);
        break;
    case "skylaunch":
        $ability = new SkyLaunch($hero);
        break;
    default:
        echo json_encode(["error" => "Unknown power"]);
        exit;
}
// Execute sandboxed behavior and capture logs for UI.
$result = $ability->use();

// Persist updated hero state for subsequent requests 
$_SESSION['hero'] = serialize($hero);

// Output logs as JSON for frontend rendering.
echo json_encode($result);
