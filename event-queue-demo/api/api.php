<?php
// --- SETUP ---
// require_once 'Enums.php';
require_once 'enums/ApiAction.php';
require_once 'enums/SoundId.php';
require_once 'Audio.php';

// Start the session to persist the queue state across requests.
session_start();

// Set the content type to JSON for all responses.
header('Content-Type: application/json');

// --- ROUTING ---
// Determine the requested action from the URL, defaulting to 'none'.
// ApiAction::tryFrom will return null if the action is invalid.
$action = ApiAction::tryFrom($_GET['action'] ?? 'none') ?? ApiAction::None;
$log = Audio::init(); // Ensure the queue is initialized on every request.

// --- ACTION HANDLING ---
// Process the request based on the validated action.
switch ($action) {
    case ApiAction::PlaySound:
        // Safely get the SoundId enum from the request string.
        $soundId = SoundId::tryFrom($_GET['soundId'] ?? '') ?? SoundId::UNKNOWN;
        $volume = (int)($_GET['volume'] ?? 100);
        // Merge the log from this action with any init logs.
        $log = array_merge($log, Audio::playSound($soundId, $volume));
        break;

    case ApiAction::Update:
        $log = array_merge($log, Audio::update());
        break;
        
    case ApiAction::Reset:
        $log = array_merge($log, Audio::reset());
        break;
}

// --- RESPONSE ---
// Send the final log back to the frontend as a JSON object.
echo json_encode(['log' => $log]);