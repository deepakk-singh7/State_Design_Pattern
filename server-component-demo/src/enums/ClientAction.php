<?php

enum ClientAction: string
{
    case Move = 'move';
    case Reset = 'reset';
    case GetState = 'getState';
}