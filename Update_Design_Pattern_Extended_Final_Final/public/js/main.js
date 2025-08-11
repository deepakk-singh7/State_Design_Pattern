import { Game } from './Game.js';

document.addEventListener('DOMContentLoaded', () => {
    // Collect all the DOM elements the game needs
    const config = {
        worldElement: document.getElementById('world'),
        startStopBtn: document.getElementById('startStopBtn'),
        resetBtn: document.getElementById('resetBtn'),
        frameCounter: document.getElementById('frame-counter'),
        entityCounter: document.getElementById('entity-counter')
    };
    
    // Create a new game instance and initialize it
    const game = new Game(config);
    game.init();
});