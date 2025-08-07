<?php

class Game {
    private array $wizards = [];

    public function __construct() {
        $this->wizards[0] = ['Name' => 'Gandalf', 'Health' => 100, 'Wisdom' => 10, 'Agility' => 5];
        $this->wizards[1] = ['Name' => 'Dumbledore', 'Health' => 80, 'Wisdom' => 8, 'Agility' => 7];
    }

    // All getters methods
    public function getHealth(int $wizardId): int {
        if (!isset($this->wizards[$wizardId])) {
            throw new \Exception("Invalid wizard ID: {$wizardId}");
        }
        return $this->wizards[$wizardId]['Health'];
    }

    public function getWisdom(int $wizardId): int {
        return $this->wizards[$wizardId]['Wisdom'];
    }

    // Corrected method name from getAgiligy to getAgility
    public function getAgility(int $wizardId): int {
        return $this->wizards[$wizardId]['Agility'];
    }

    // All setters methods
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

    // Print the status of a wizard
    public function printStatus(): void {
        // Used double quotes for newline character \n to work
        echo "----------------------------------------\n";
        foreach ($this->wizards as $id => $data) {
            echo "Wizard: {$data['Name']} | ID: {$id} | Health: {$data['Health']} | Wisdom: {$data['Wisdom']} | Agility: {$data['Agility']}\n";
        }
        echo "----------------------------------------\n";
    }

    // Function to check if the game is over
    public function isOver(): bool {
        // Correctly checks all wizards and for health <= 0
        foreach ($this->wizards as $data) {
            if ($data['Health'] <= 0) {
                return true;
            }
        }
        return false;
    }

    // Function to get the winner
    public function getWinner(): ?string {
        // Corrected case-sensitive keys 'Health' and 'Name'
        if ($this->wizards[0]['Health'] <= 0) {
            return $this->wizards[1]['Name'];
        }
        if ($this->wizards[1]['Health'] <= 0) {
            return $this->wizards[0]['Name'];
        }
        return null;
    }
}