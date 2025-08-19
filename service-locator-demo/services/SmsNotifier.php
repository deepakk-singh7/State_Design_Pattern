<?php
// Service Provider 2: Concrete implementation for sending SMS.
require_once 'Notifier.php';

/**
 * An implementation of the Notifier interface that "sends" an SMS.
  */
class SmsNotifier implements Notifier {
    /**
     * simulates sending a message via SMS.
     *
     * @param string $message The message content.
     * @return string A confirmation message.
     */
    public function send(string $message): string {
        return "📱 SMS: Message '{$message}' sent successfully.";
    }
}