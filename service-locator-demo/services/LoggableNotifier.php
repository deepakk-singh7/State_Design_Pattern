<?php
// The Decorator: Adds logging functionality to any Notifier service.
require_once 'Notifier.php';

/**
 * A Decorator that adds logging functionality to any Notifier object.
*/
class LoggableNotifier implements Notifier {

    /**
     * @var Notifier The wrapped Notifier instance (the service being decorated).
     */
    private Notifier $wrapped; // Service Reference..

     /**
     * The constructor accepts any object that implements the Notifier interface.
     *
     * @param Notifier $wrappedService The notifier service to wrap.
     */
    public function __construct(Notifier $wrappedService) {
        $this->wrapped = $wrappedService;
    }

    /**
     * Adds logging before delegating the send operation to the wrapped notifier.
     *
     * @param string $message The message content.
     * @return string The combined log message and the result from the wrapped service.
     */
    
    public function send(string $message): string {
        // 1. Add the new behavior (logging).
        $logMessage = "[LOG] " . date('Y-m-d H:i:s') . ": Preparing to send message...\n";

        // 2. Delegate the actual work to the wrapped object.
        $result = $this->wrapped->send($message);

        // 3. Return the combined output.
        return $logMessage . $result;
    }
}