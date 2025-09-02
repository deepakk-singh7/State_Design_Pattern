<?php
/**
 * Defines the CSS classes for different types of log messages.
 * This centralizes styling hooks for the frontend.
 */
enum LogType: string
{
    case Info = 'info';
    case Process = 'process';
    case Error = 'error';
}
