<?php
class Game {
    private array $wizards = [];
    private array $log = [];

    public function __construct() {
        $this->wizards[0] = ['Name' => 'Gandalf', 'Health' => 100, 'Wisdom' => 10, 'Agility' => 5];
        $this->wizards[1] = ['Name' => 'Dumbledore', 'Health' => 100, 'Wisdom' => 8, 'Agility' => 7];
        $this->log[] = "The duel begins! Gandalf vs. Dumbledore.";
    }

    public function getHealth(int $wizardId): int { return $this->wizards[$wizardId]['Health']; }
    public function getWisdom(int $wizardId): int { return $this->wizards[$wizardId]['Wisdom']; }
    public function getAgility(int $wizardId): int { return $this->wizards[$wizardId]['Agility']; }
    public function getName(int $wizardId): string { return $this->wizards[$wizardId]['Name']; }

    public function setHealth(int $wizardId, int $amount): void { $this->wizards[$wizardId]['Health'] = max(0, $amount); }
    public function setWisdom(int $wizardId, int $amount): void { $this->wizards[$wizardId]['Wisdom'] = max(0, $amount); }
    public function setAgility(int $wizardId, int $amount): void { $this->wizards[$wizardId]['Agility'] = max(0, $amount); }

    public function addToLog(string $message): void { $this->log[] = $message; }

    public function isOver(): bool {
        return $this->wizards[0]['Health'] <= 0 || $this->wizards[1]['Health'] <= 0;
    }

    public function getWinner(): ?string {
        if ($this->wizards[0]['Health'] <= 0 && $this->wizards[1]['Health'] <= 0) return "It's a draw!";
        if ($this->wizards[0]['Health'] <= 0) return $this->wizards[1]['Name'];
        if ($this->wizards[1]['Health'] <= 0) return $this->wizards[0]['Name'];
        return null;
    }

    public function getState(): array {
        $state = [
            'wizards' => $this->wizards,
            'log' => $this->log,
            'isOver' => $this->isOver(),
            'winner' => $this->getWinner()
        ];
        $this->log = []; // Clear log after getting state
        return $state;
    }
}