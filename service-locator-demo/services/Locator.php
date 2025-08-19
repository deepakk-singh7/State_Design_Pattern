<?php
// The Service Locator: A central registry for the Notifier service.
require_once 'Notifier.php';
require_once 'NullNotifier.php';

/**
 * The Locator class implements the Service Locator design pattern.
 *
 * It provides a static, global point of access to a service (in this case,
 * a Notifier).
 */

class Locator {
    /**
     * @var Notifier The static property holding the single service instance.
     */
    private static Notifier $service;

    /**
     * Initializes the locator with a safe default service (NullNotifier).
     * This ensures that calling getNotifier() will always return a valid object
     */
    public static function initialize() {
        self::$service = new NullNotifier();
    }

    /**
     * Returns the currently configured Notifier service.
     *
     * @return Notifier The active Notifier instance.
     */
    public static function getNotifier(): Notifier {
        return self::$service;
    }

     /**
     * Provides (registers) a new service to the locator.
     *
     * @param Notifier|null $service The service instance to be used. If null
     * is passed, it reverts to the safe NullNotifier.
     */
    public static function provide(?Notifier $service) {
        if ($service === null) {
            // Revert to the safe null service if null is provided.
            self::$service = new NullNotifier();
        } else {
            self::$service = $service;
        }
    }
}