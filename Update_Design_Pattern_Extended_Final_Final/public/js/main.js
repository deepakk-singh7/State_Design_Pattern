import { Game } from './Game.js';

document.addEventListener('DOMContentLoaded', () => {
    // Collect all the DOM elements the game needs
    const domElements = {
        worldElement: document.getElementById('world'),
        startStopBtn: document.getElementById('startStopBtn'),
        resetBtn: document.getElementById('resetBtn'),
        frameCounter: document.getElementById('frame-counter'),
        entityCounter: document.getElementById('entity-counter'),
        clientFpsCounter: document.getElementById('client-fps-counter')
    };
    
    // Create a new game instance, passing in the config from the HTML
    // The global 'AppConfig' is available here because the script in index.php runs first.
    const game = new Game(domElements, AppConfig); 
    game.init();
});