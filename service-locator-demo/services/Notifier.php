<?php

// The SERVICE 
/**
 * The Notifier interface defines the contract for all notification services.
 */
interface Notifier {

     /**
     * Sends a message.
     *
     * @param string $message The message content to be sent.
     * @return string The result or status of the sending operation.
     */
    public function send(string $message): string;
}