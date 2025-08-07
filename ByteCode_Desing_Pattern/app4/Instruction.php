<?php 

class Instruction
{
    // Stat Manipulation
    const SET_HEALTH = 0x01;
    const SET_WISDOM = 0x02;
    const SET_AGILITY = 0x03;
    const GET_HEALTH = 0x04;
    const GET_WISDOM = 0x05;
    const GET_AGILITY = 0x06;

    // Arithmetic
    const ADD = 0x10;
    const SUBTRACT = 0x11;
    const MULTIPLY = 0x12;
    const DIVIDE = 0x13;

    // Data
    const LITERAL = 0x20;
}