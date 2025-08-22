<?php

/**
 * Defines the simulation modes.
 */
enum Mode: string {
    case Inefficient = 'inefficient';
    case Efficient = 'efficient';
}

/**
 * Defines constant keys.
 */
class Keys {
    // Session Keys
    const DOTS_INITIALIZED = 'dots_initialized';
    const INEFFICIENT_DOTS = 'inefficient_dots';
    const EFFICIENT_DOTS = 'efficient_dots';

    // Data Structure Keys (for efficient mode arrays)
    const X = 'x';
    const Y = 'y';
    const VX = 'vx';
    const VY = 'vy';

    // API Request/Response Keys
    const MODE = 'mode';
    const POSITIONS = 'positions';
    const SERVER_TIME = 'serverTime';
}

/**
 * Defines core simulation parameters.
 */
class Simulation {
    const NUM_DOTS = 20000;
    const WIDTH = 800;
    const HEIGHT = 600;
}