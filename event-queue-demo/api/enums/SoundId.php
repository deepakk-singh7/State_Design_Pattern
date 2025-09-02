<?php
/**
 * Defines the set of valid sound identifiers used in the game.
 */
enum SoundId: string
{
    case JUMP = 'JUMP';
    case ENEMY_HIT = 'ENEMY_HIT';
    case COIN = 'COIN';
    case UNKNOWN = 'UNKNOWN';
}
