/**
 * @file Frontend game controller using interpolation.
 */
document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Element References ---
    const worldElement = document.getElementById('world');
    const startStopBtn = document.getElementById('startStopBtn');
    const resetBtn = document.getElementById('resetBtn');
    const frameCounter = document.getElementById('frame-counter');
    const entityCounter = document.getElementById('entity-counter');

    // --- Game State Variables ---
    let isRunning = false;
    let animationFrameId;
    let lastFrameTime = 0;

    // NEW: State buffers for interpolation
    let previousState = null;
    let currentState = null;

    // --- Control Logic ---
    startStopBtn.addEventListener('click', () => {
        isRunning = !isRunning;
        startStopBtn.textContent = isRunning ? 'Stop' : 'Start';
        if (isRunning) {
            lastFrameTime = performance.now();
            animationFrameId = requestAnimationFrame(gameLoop);
        } else {
            cancelAnimationFrame(animationFrameId);
        }
    });
    
    resetBtn.addEventListener('click', async () => {
        if (isRunning) {
            isRunning = false;
            startStopBtn.textContent = 'Start';
            cancelAnimationFrame(animationFrameId);
        }
        
        await fetch('api/game_tick.php?action=reset');
        
        worldElement.innerHTML = '';
        frameCounter.textContent = '0';
        entityCounter.textContent = '0';
        previousState = null; // Also reset client state buffers
        currentState = null;
        console.log("Game Reset!");
    });

    // --- Game Loop and Rendering ---
    async function gameLoop(timestamp) {
        if (!isRunning) return;

        const deltaTime = (timestamp - lastFrameTime) / 1000;
        lastFrameTime = timestamp;

        try {
            const response = await fetch(`api/game_tick.php?dt=${Math.min(deltaTime, 0.1)}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const serverResponse = await response.json();

            // 1. Shift the states for interpolation
            previousState = currentState;
            currentState = serverResponse;

            // 2. Render, but only if we have a state to draw
            if (currentState) {
                render();
            }

            // 3. Schedule the next frame
            animationFrameId = requestAnimationFrame(gameLoop);

        } catch (error) {
            console.error("Error in game loop:", error);
            isRunning = false;
            startStopBtn.textContent = 'Start';
        }
    }

    function render() {
        // If we don't have a previous state, we can't interpolate.
        // So, we treat the previous state as identical to the current one.
        const prevState = previousState || currentState;
        const alpha = currentState.interpolation_alpha;

        worldElement.innerHTML = '';
        frameCounter.textContent = currentState.frame;
        entityCounter.textContent = currentState.entities.length;

        const worldWidth = worldElement.clientWidth;
        const worldHeight = worldElement.clientHeight;

        // Draw entities using interpolation
        currentState.entities.forEach(currentEntity => {
            const prevEntity = prevState.entities.find(e => e.id === currentEntity.id);

            let renderX, renderY;

            if (!prevEntity) {
                // Entity is new, just draw it at its current position
                renderX = currentEntity.x;
                renderY = currentEntity.y;
            } else {
                // INTERPOLATE position for smooth motion
                renderX = prevEntity.x * (1.0 - alpha) + currentEntity.x * alpha;
                renderY = prevEntity.y * (1.0 - alpha) + currentEntity.y * alpha;
                
            }
            // // And force it to use the non-interpolated position:
            // const renderX = currentEntity.x;
            // const renderY = currentEntity.y;
                        
            const entityDiv = document.createElement('div');
            entityDiv.className = `entity ${currentEntity.type.toLowerCase()}`;
            entityDiv.style.left = `${(renderX / 100) * worldWidth}px`;
            entityDiv.style.top = `${(renderY / 100) * worldHeight}px`;

            worldElement.appendChild(entityDiv);
        });
    }
});