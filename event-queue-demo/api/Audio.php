<?php

require_once 'PlayMessage.php';
require_once 'enums/SoundId.php';
require_once 'enums/LogType.php';
/**
 * Manages the audio event queue using a session-backed ring buffer.
 */
class Audio {
    /** @var int The maximum number of pending sound requests. */
    private const MAX_PENDING = 16;
    /** @var string The key used to store the queue in the PHP session. */
    private const SESSION_KEY = 'audio_queue';

    /**
     * Initializes the queue state in the session if it doesn't already exist.
     * @return array An array of log messages.
     */
    public static function init(): array {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [
                'pending' => [],
                'head' => 0,
                'tail' => 0
            ];
            return ["<p class='" . LogType::Info->value . "'>Audio engine initialized.</p>"];
        }
        return [];
    }

    /**
     * Clears the audio queue from the session.
     * @return array An array of log messages from the re-initialization.
     */
    public static function reset(): array {
        unset($_SESSION[self::SESSION_KEY]);
        return self::init();
    }

    /**
     * Enqueues a sound request. If a duplicate exists, it merges the request.
     * @param SoundId $soundId The enum for the sound to play.
     * @param int $volume The volume of the sound.
     * @return array An array of log messages.
     */

    // public static function playSound(SoundId $soundId, int $volume): array { // too many logics.... 
    //     $log = [];
    //     // Use a reference (&) to easily modify the session data.
    //     $q = &$_SESSION[self::SESSION_KEY];

    //     // --- Aggregation Logic ---
    //     // Walk the ring buffer to find if the same sound is already pending.
    //     for ($i = $q['head']; $i !== $q['tail']; $i = ($i + 1) % self::MAX_PENDING) {
    //         // Compare enums directly for type safety.
    //         if ($q['pending'][$i]->soundId === $soundId) {
    //             // If found, merge by taking the louder of the two volumes.
    //             $q['pending'][$i]->volume = max($volume, $q['pending'][$i]->volume);
    //             $log[] = "<p>Merged request for '{$soundId->value}'. Queue size unchanged.</p>";
    //             return $log;
    //         }
    //     }

    //     // --- Enqueue Logic ---
    //     // Check if the queue is full (tail is right behind the head).
    //     if (($q['tail'] + 1) % self::MAX_PENDING === $q['head']) {
    //         $log[] = "<p class='" . LogType::Error->value . "'>AUDIO QUEUE FULL! Dropping '{$soundId->value}'.</p>";
    //         return $log;
    //     }

    //     // Add the new request to the tail of the queue.
    //     $q['pending'][$q['tail']] = new PlayMessage($soundId, $volume);
    //     // Advance the tail pointer, wrapping around the array if necessary.
    //     $q['tail'] = ($q['tail'] + 1) % self::MAX_PENDING;
    //     $log[] = "<p>Enqueued '{$soundId->value}'. Head: {$q['head']}, Tail: {$q['tail']}</p>";
        
    //     return $log;
    // }

    // modification ------

    public static function playSound(SoundId $soundId, int $volume): array {
        $q = &$_SESSION[self::SESSION_KEY];

        // Try to find and merge an existing sound request. || --- Aggregation Logic ---
        $existingIndex = self::findPendingSoundIndex($soundId, $q);
        // If found, then merge the event
        if ($existingIndex !== null) {
            return self::mergeSoundRequest($existingIndex, $volume, $soundId, $q);
        }

        // If no existing request was found, enqueue a new one.
        return self::enqueueNewSound($soundId, $volume, $q);
    }

    /**
     * searches the queue for a pending sound.
     *
     * @param SoundId $soundId The sound to search for.
     * @param array $q A reference to the queue data.
     * @return int|null The array index if found, otherwise null.
     */
    private static function findPendingSoundIndex(SoundId $soundId, array &$q): ?int {
        for ($i = $q['head']; $i !== $q['tail']; $i = ($i + 1) % self::MAX_PENDING) {
            if (isset($q['pending'][$i]) && $q['pending'][$i]->soundId === $soundId) {
                return $i;
            }
        }
        return null;
    }

     /**
     * merges a new sound request with an existing one by taking the max volume.
     *
     * @param int $index The index of the existing request.
     * @param int $volume The volume of the new request.
     * @param SoundId $soundId The ID of the sound being merged.
     * @param array $q A reference to the queue data.
     * @return array An array of log messages.
     */
    private static function mergeSoundRequest(int $index, int $volume, SoundId $soundId, array &$q): array {
        $q['pending'][$index]->volume = max($volume, $q['pending'][$index]->volume);
        return ["<p>Merged request for '{$soundId->value}'. Queue size unchanged.</p>"];
    }

     /**
     * adds a new sound request to the tail of the queue.
     *
     * @param SoundId $soundId The sound to enqueue.
     * @param int $volume The volume of the sound.
     * @param array $q A reference to the queue data.
     * @return array An array of log messages.
     */
    private static function enqueueNewSound(SoundId $soundId, int $volume, array &$q): array {
        if (($q['tail'] + 1) % self::MAX_PENDING === $q['head']) {
            return ["<p class='" . LogType::Error->value . "'>AUDIO QUEUE FULL! Dropping '{$soundId->value}'.</p>"];
        }

        $q['pending'][$q['tail']] = new PlayMessage($soundId, $volume);
        $q['tail'] = ($q['tail'] + 1) % self::MAX_PENDING;
        
        return ["<p>Enqueued '{$soundId->value}'. Head: {$q['head']}, Tail: {$q['tail']}</p>"];
    }

    /**
     * processes a single sound request from the head of the queue.
     * @return array An array of log messages.
     */
    public static function update(): array {
        $log = [];
        $q = &$_SESSION[self::SESSION_KEY];

        // If head and tail are the same, the queue is empty.
        if ($q['head'] === $q['tail']) {
            $log[] = "<p class='" . LogType::Info->value . "'>Queue is empty. Nothing to process.</p>";
            return $log;
        }

        // Get the message from the head of the queue.
        $message = $q['pending'][$q['head']];
        // Use ->value to get the string representation of the enum for logging.
        $log[] = "<p class='" . LogType::Process->value . "'>Processing '{$message->soundId->value}' (Volume: {$message->volume})...</p>";

        // "Play" the sound (in this demo, we just log it and remove it).
        unset($q['pending'][$q['head']]); // Clean up the array slot.

        // Advance the head pointer, wrapping if necessary.
        $q['head'] = ($q['head'] + 1) % self::MAX_PENDING;
        $log[] = "<p class='" . LogType::Process->value . "'>  -> Processed. New Head: {$q['head']}, Tail: {$q['tail']}</p>";

        return $log;
    }
}