<?php
// Null Service Provider: A safe default that does nothing.
require_once 'Notifier.php';

/**
 * A "null" implementation of the Notifier interface (Null Object Pattern).
 */

class NullNotifier implements Notifier {
    /**
     * Does nothing but returns a status message indicating that notifications are disabled.
     *
     * @param string $message The message content (which is ignored).
     * @return string A message indicating that no action was taken.
     */
    public function send(string $message): string {
        return "🚫 NOTIFICATIONS DISABLED: Message was not sent.";
    }
}