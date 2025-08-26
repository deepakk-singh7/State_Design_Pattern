<?php

// Contains all our string literals and constants for type safety.

class ObjectType {
    const PARTICLE = 'particle';
    const SMOKE = 'smoke';
}

class OverflowStrategy {
    const IGNORE = 'ignore';
    const RECLAIM = 'reclaim';
}

class ApiAction {
    const CREATE = 'create';
    const RETURN_OBJECTS = 'return';
    const STATUS = 'status';
}