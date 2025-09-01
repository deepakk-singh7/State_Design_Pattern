<?php

namespace App;

/**
 * Defines constants for the API actions.
 * Using constants prevents typos and makes the code easier to maintain.
 */
class ApiActions
{
    const INIT = 'init';
    const UPDATE = 'update';
    const ACTION = 'action';
    const GRID = 'grid';
}

class ReturnState {
    const X = 'x';
    const Y = 'y';
    const IS_NEAR = 'isNear';
    const ID = 'id';
}