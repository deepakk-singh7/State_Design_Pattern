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
    let isRunning = false;
    // let gameLoopTimeout; // No longer needed
    let animationFrameId; // Use this to cancel the loop
    let lastFrameTime = 0; // Timestamp of the last frame

    // --- Control Logic ---

    /**
     * Toggles the game state between running and stopped when the start/stop button is clicked.
     */
    startStopBtn.addEventListener('click', () => {
        isRunning = !isRunning;
        startStopBtn.textContent = isRunning ? 'Stop' : 'Start';
        if (isRunning) {
            // Initialize lastFrameTime when starting the loop
            lastFrameTime = performance.now(); 
            // Start the loop
            animationFrameId = requestAnimationFrame(gameLoop);
        } else {
            // Stop the loop
            cancelAnimationFrame(animationFrameId);
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
     * It calculates delta time, fetches the game state from the backend, 
     * triggers a render, and schedules the next iteration.
     * @param {number} timestamp The current time provided by requestAnimationFrame.
     */
    async function gameLoop(timestamp) {
        if (!isRunning) return;

        // Calculate delta time in seconds
        const deltaTime = (timestamp - lastFrameTime) / 1000;
        lastFrameTime = timestamp;

        try {
            // 1. Pass deltaTime to the backend
            // We cap deltaTime to avoid huge jumps if the tab was inactive
            const response = await fetch(`api/game_tick.php?dt=${Math.min(deltaTime, 0.1)}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const gameState = await response.json();

            // 2. Render the new state
            render(gameState);

            // 3. Schedule the next frame
            animationFrameId = requestAnimationFrame(gameLoop);

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