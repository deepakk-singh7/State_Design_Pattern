<?php

/**
 * All user-facing strings and log messages.
 */

namespace App\Config;

class MessageConstants
{
    public const SIMULATION_HEADER = "--- Simulating a single frame with multiple moves (Eager Approach) ---\n\n";
    public const RENDER_HEADER = "\n--- RENDER FRAME ---\n";
    public const ACTION_MOVING = "*ACTION*: Moving '%s'.\n";
    public const RECALCULATING = "  -> Recalculating world transform for '%s'.\n";
    public const FINAL_POSITION = "Parrot's final rendered position: %s\n";
    public const TOTAL_CALCULATIONS = "Total 'expensive' calculations performed: %d";
    public const SEPARATOR = "\n----------------------------------------\n";
}