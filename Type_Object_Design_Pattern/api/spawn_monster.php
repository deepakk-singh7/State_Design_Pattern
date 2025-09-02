<?php

header('Content-Type: application/json');
require_once 'Monster.php';
require_once 'BreedFactory.php';
require_once 'DataManager.php';

// Get the requested breed name from the URL query parameter (e.g., ...?breed=Troll).
const QUERY_PARAM = 'breed';
$breedName = $_GET[QUERY_PARAM] ?? '';

// --- Input Validation ---
if (empty($breedName)) {
    http_response_code(400);
    echo json_encode(['error' => 'Breed name not specified.']);
    exit;
}

// Load all breed data from the JSON file.
// $allBreedsData = json_decode(file_get_contents('monsters.json'), true);

// Use the DataManager to load the data. No more repeated code!
$allBreedsData = DataManager::loadAllBreedsData();

// Check if the requested breed actually exists in our data.
if (!isset($allBreedsData[$breedName])) {
    http_response_code(404);
    echo json_encode(['error' => "Breed '{$breedName}' not found."]);
    exit;
}

// Isolate the data for the specific breed we want to create.
$breedData = $allBreedsData[$breedName];

// --- Object Creation ---
// 1. Use the BreedFactory to create the Type Object (Breed).
$breed = BreedFactory::create($breedName, $breedData);

// 2. Create the Typed Object (Monster), composing it with its Breed.
$monster = new Monster($breed);

// Send the complete, composed Monster object back to the frontend as a JSON string.
echo json_encode($monster);
