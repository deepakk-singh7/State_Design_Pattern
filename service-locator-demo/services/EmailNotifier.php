<?php
// Service Provider 1: Concrete implementation for sending emails.
require_once 'Notifier.php';

/**
 * An implementation of the Notifier interface that "sends" an email.
*/
class EmailNotifier implements Notifier {
     /**
     * simulates sending a message via email.
     *
     * @param string $message The message content.
     * @return string A confirmation message.
     */
    public function send(string $message): string {
        return "✅ EMAIL: Message '{$message}' sent successfully.";
    }
}