<?php

/**
 * A helper class to handle data loading operations.
 *
 * This centralizes the logic for reading and decoding the monsters.json file,
 */
class DataManager {

     /**
     * Defines the path to the monster data file.
     */
    private const MONSTERS_FILENAME = 'monsters.json';
    /**
     * Loads and decodes the monsters.json file into an associative array.
     *
     * @return array The decoded monster data.
     */
    public static function loadAllBreedsData(): array {
        $json_data = file_get_contents(self::MONSTERS_FILENAME);
        return json_decode($json_data, true);
    }
}
