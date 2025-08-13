<?php
/**
 * Master Configuration File (Single Source of Truth)
 */

// --- Server Setting ---
// This defines the game's update rate in Hz (updates per second).
define('SERVER_TICK_RATE_HZ', 10.0); // 10 Hz is a good default

// --- Client Setting ---
// This defines the target rendering framerate for the browser.
define('CLIENT_TARGET_FPS', 60.0);

// This value is derived automatically for the server's internal physics.
define('FIXED_TIMESTEP', 1.0 / SERVER_TICK_RATE_HZ);