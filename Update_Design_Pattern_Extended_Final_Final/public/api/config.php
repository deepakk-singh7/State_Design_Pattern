<?php
/**
 * Global configuration for the server-side application.
 */

// Define the server's update rate (tick rate).
// 10.0 = 10 Hz (100ms interval)
// 5.0  = 5 Hz  (200ms interval)
// 1.0  = 1 Hz  (1000ms interval)
// define('SERVER_TICK_RATE_HZ', 10.0);
// define('SERVER_TICK_RATE_HZ', 20.0);
// 2 Hz (500ms interval)
// define('SERVER_TICK_RATE_HZ', 2.0);
define('SERVER_TICK_RATE_HZ', 10.0);
define('FIXED_TIMESTEP', 1.0 / SERVER_TICK_RATE_HZ);