/**
 * @file Frontend game controller for the Simple Game Engine.
 * @description This script handles user interactions (start, stop, reset),
 * manages the main game loop by fetching state from a backend API,
 * and renders the game state onto the DOM.
 */

// Wait for the HTML document to be fully loaded and parsed before executing the script.
document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Element References ---
    const worldElement = document.getElementById('world');
    const startStopBtn = document.getElementById('startStopBtn');
    const resetBtn = document.getElementById('resetBtn');
    const frameCounter = document.getElementById('frame-counter');
    const entityCounter = document.getElementById('entity-counter');

    // --- Game State Variables ---
    /** @type {boolean} A flag to control whether the game loop is active. */
    let isRunning = false;

    /** @type {number} A reference to the timeout used for the game loop, to allow cancellation. */
    let gameLoopTimeout;

    /** @type {number} The time in milliseconds between each frame (game tick). */
    const FRAME_INTERVAL = 500; // ms per frame (2 FPS)

    // --- Control Logic ---

    /**
     * Toggles the game state between running and stopped when the start/stop button is clicked.
     */
    startStopBtn.addEventListener('click', () => {
        isRunning = !isRunning;
        startStopBtn.textContent = isRunning ? 'Stop' : 'Start';
        if (isRunning) {
            gameLoop();
        } else {
            clearTimeout(gameLoopTimeout);
        }
    });

    
    // Resets the game to its initial state when the reset button is clicked.
    resetBtn.addEventListener('click', async () => {
        // Stop the loop if it's running
        if (isRunning) {
            isRunning = false;
            startStopBtn.textContent = 'Start';
            clearTimeout(gameLoopTimeout);
        }
        
        // Call the backend to reset the state
        await fetch('api/game_tick.php?action=reset');
        
        // Clear the board and reset counters
        worldElement.innerHTML = '';
        frameCounter.textContent = '0';
        entityCounter.textContent = '0';
        console.log("Game Reset!");
    });

    // --- Game Loop and Rendering ---
    /**
     * The main game loop function.
     * It fetches the latest game state from the backend, triggers a render,
     * and then schedules the next iteration of itself.
     * @async
     */
    async function gameLoop() {
        if (!isRunning) return; // Exit the loop if the game has been stopped.
        try {
            // 1. Call the backend to update the game state for one tick
            const response = await fetch('api/game_tick.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const gameState = await response.json();

            // 2. Render the new state on the frontend
            render(gameState);

            // 3. Schedule the next frame
            gameLoopTimeout = setTimeout(gameLoop, FRAME_INTERVAL);

        } catch (error) {
            console.error("Error in game loop:", error);
            isRunning = false; // Stop the game on error
            startStopBtn.textContent = 'Start';
        }
    }

    function render(state) {
        // Clear previous frame's entities
        worldElement.innerHTML = '';

        // Update stats
        frameCounter.textContent = state.frame;
        entityCounter.textContent = state.entities.length;

        const worldWidth = worldElement.clientWidth;
        const worldHeight = worldElement.clientHeight;

        // Draw current entities
        state.entities.forEach(entity => {
            const entityDiv = document.createElement('div');
            entityDiv.className = `entity ${entity.type.toLowerCase()}`;
            
            // Convert server coordinates (e.g., 0-100) to pixel coordinates
            entityDiv.style.left = `${(entity.x / 100) * worldWidth}px`;
            entityDiv.style.top = `${(entity.y / 100) * worldHeight}px`;

            worldElement.appendChild(entityDiv);
        });
    }
});