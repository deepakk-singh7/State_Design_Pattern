<?php

/**
 * Game State Manager for the Wizard Duel.
 *
 * This file defines the Game class, which is responsible for holding all
 * state related to the duel, such as the stats for each wizard.
 */

class Game {
    /**
     * Holds the data for all wizards in the game.
     *
     * @var array
     */
    private array $wizards = [];

    /**
     * Game constructor.
     *
     * Initializes the game by creating the two default wizards,
     * Gandalf and Dumbledore, with their starting stats.
     */
    public function __construct() {
        $this->wizards[0] = ['Name' => 'Gandalf', 'Health' => 100, 'Wisdom' => 10, 'Agility' => 5];
        $this->wizards[1] = ['Name' => 'Dumbledore', 'Health' => 80, 'Wisdom' => 8, 'Agility' => 7];
    }

    // --- Getters ---

    /**
     * Gets the health of a specific wizard.
     *
     * @param int $wizardId The ID of the wizard (0 or 1).
     * @return int The current health of the wizard.
     * @throws \Exception If the wizard ID is invalid.
     */
    public function getHealth(int $wizardId): int {
        if (!isset($this->wizards[$wizardId])) {
            throw new \Exception("Invalid wizard ID: {$wizardId}");
        }
        return $this->wizards[$wizardId]['Health'];
    }

    /**
     * Gets the wisdom of a specific wizard.
     *
     * @param int $wizardId The ID of the wizard.
     * @return int The current wisdom of the wizard.
     */

    public function getWisdom(int $wizardId): int {
        return $this->wizards[$wizardId]['Wisdom'];
    }

    /**
     * Gets the agility of a specific wizard.
     *
     * @param int $wizardId The ID of the wizard.
     * @return int The current agility of the wizard.
     */
    public function getAgility(int $wizardId): int {
        return $this->wizards[$wizardId]['Agility'];
    }

    // --- Setters ---

    /**
     * Sets the health of a specific wizard.
     *
     * @param int $wizardId The ID of the wizard.
     * @param int $amount   The new health value.
     * @return void
     * @throws \Exception If the wizard ID is invalid.
     */
    public function setHealth(int $wizardId, int $amount): void {
        if (!isset($this->wizards[$wizardId])) {
            throw new \Exception("Invalid wizard ID: {$wizardId}");
        }
        // Ensure health doesn't go below 0
        $this->wizards[$wizardId]['Health'] = max(0, $amount);
    }

    public function setWisdom(int $wizardId, int $amount): void {
        $this->wizards[$wizardId]['Wisdom'] = $amount;
    }

    public function setAgility(int $wizardId, int $amount): void {
        // Corrected key from 'Agiligy' to 'Agility'
        $this->wizards[$wizardId]['Agility'] = $amount;
    }

    // --- Game State & Utility Methods ---

    /**
     * Prints the current status of all wizards to the console.
     *
     * @return void
     */
    public function printStatus(): void {
        // Used double quotes for newline character \n to work
        echo "----------------------------------------\n";
        foreach ($this->wizards as $id => $data) {
            echo "Wizard: {$data['Name']} | ID: {$id} | Health: {$data['Health']} | Wisdom: {$data['Wisdom']} | Agility: {$data['Agility']}\n";
        }
        echo "----------------------------------------\n";
    }

    /**
     * Checks if the game has ended.
     * @return bool True if the game is over, false otherwise.
     */
    public function isOver(): bool {
        // Correctly checks all wizards and for health <= 0
        foreach ($this->wizards as $data) {
            if ($data['Health'] <= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines and returns the name of the winning wizard.
     *
     * @return string|null The winner's name, or null if the game is not over.
     */
    public function getWinner(): ?string {
        if ($this->wizards[0]['Health'] <= 0) {
            return $this->wizards[1]['Name'];
        }
        if ($this->wizards[1]['Health'] <= 0) {
            return $this->wizards[0]['Name'];
        }
        // Return null if there is no winner yet.
        return null;
    }
}