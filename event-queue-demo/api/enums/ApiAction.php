<?php
/**
 * Defines the valid actions the API can perform.
 */
enum ApiAction: string
{
    case PlaySound = 'playSound';
    case Update = 'update';
    case Reset = 'reset';
    case Init = 'init';
    case None = 'none';
}