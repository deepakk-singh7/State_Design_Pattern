<?php

// Set the HTTP header to indicate that the response is JSON.
header('Content-Type: application/json');
require_once 'DataManager.php';

// Read the entire contents of the monsters.json file.
// $json_data = file_get_contents('monsters.json');
// // Decode the JSON string into a PHP associative array.
// $breeds_data = json_decode($json_data, true);

// 1. Use the DataManager to get all the breed data.
$breeds_data = DataManager::loadAllBreedsData();

// Get all the top-level keys from the array (e.g., "Slime", "Troll", "Dragon").
$breed_names = array_keys($breeds_data);

// Encode the simple array of names into a JSON string and send it as the response.
echo json_encode($breed_names);

