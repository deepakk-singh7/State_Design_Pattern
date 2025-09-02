<?php
/**
 * Defines the possible transformation states for scene objects.
 */
namespace App\Enums;

enum Transform: string
{
    // Initial positions
    case SHIP_AT_SEA = '[Ship@Sea]';
    case NEST_ON_MAST = '[Nest@Mast]';
    case PIRATE_IN_NEST = '[Pirate@Nest]';
    case PARROT_ON_SHOULDER = '[Parrot@Shoulder]';

    // Moved positions
    case SHIP_MOVED = '[Ship moved]';
    case NEST_MOVED = '[Nest moved]';
    case PIRATE_MOVED = '[Pirate moved]';
    case PARROT_MOVED = '[Parrot moved]';
}