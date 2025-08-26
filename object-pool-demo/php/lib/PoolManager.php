<?php

require_once 'ObjectPool.php';

/**
 * Manages all the object pools in the application using the Singleton pattern.
 *
 * This class is responsible for reading the configuration, initializing all necessary
 * ObjectPools, and providing a single, global point of access to them.
 */
class PoolManager {
    /**
     * The single, static instance of the PoolManager.
     * @var PoolManager|null
     */
    private static ?PoolManager $instance = null;

    /**
     * An associative array holding all the initialized ObjectPool instances.
     * The keys are the pool types (e.g., 'particle', 'smoke').
     * @var ObjectPool[]
     */
    private array $pools = [];

    /**
     * The constructor is private to enforce the Singleton pattern.
     * It reads the configuration file and creates all the specified object pools.
     */
    private function __construct() {
        // Load the main configuration file.
        $config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
        
        // Loop through each pool defined in the configuration.
        foreach ($config['pools'] as $key => $poolConfig) {
            // Conventionally derive the class name from the config key (e.g., 'particle' -> 'Particle').
            $className = ucfirst($key); 
            
            // Ensure the corresponding class file exists before creating a pool.
            if (class_exists($className)) {
                // Create a new ObjectPool and store it in our internal array.
                $this->pools[$key] = new ObjectPool(
                    $className, 
                    $poolConfig['size'],
                    $poolConfig['overflow']
                );
            }
        }
    }

    /**
     * The static method that provides the single global access point to the PoolManager instance.
     *
     * @return PoolManager The single instance of the PoolManager.
     */
    public static function getInstance(): PoolManager {
        // If an instance doesn't exist yet, create one.
        if (self::$instance === null) {
            self::$instance = new PoolManager();
        }
        // Return the single, existing instance.
        return self::$instance;
    }

    /**
     * Retrieves a specific object pool by its type.
     *
     * @param string $type The key of the pool to retrieve (e.g., 'particle').
     * @return ObjectPool|null The requested ObjectPool, or null if it doesn't exist.
     */
    public function getPool(string $type): ?ObjectPool {
        return $this->pools[$type] ?? null;
    }
    
    /**
     * Retrieves all initialized object pools.
     *
     * @return ObjectPool[] An associative array of all ObjectPool instances.
     */
    public function getAllPools(): array {
        return $this->pools;
    }
}