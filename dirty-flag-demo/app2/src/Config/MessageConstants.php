<?php
/**
 * Centralizes all user-facing strings and log messages.
 */

namespace App\Config;

class MessageConstants
{
    public const SIMULATION_HEADER = "--- Simulating a single frame with multiple moves (Dirty Flag Approach) ---\n\n";
    public const RENDER_HEADER = "\n--- RENDER FRAME ---\n";
    public const ACTION_MOVING = "*ACTION*: Moving '%s'. Flagging as dirty.\n";
    public const RECALCULATING_DIRTY = "  -> Recalculating world transform for '%s' because it's dirty.\n";
    public const FINAL_POSITION = "Parrot's final rendered position: %s\n";
    public const TOTAL_CALCULATIONS = "Total 'expensive' calculations performed: %d";
    public const SEPARATOR = "\n----------------------------------------\n";
}