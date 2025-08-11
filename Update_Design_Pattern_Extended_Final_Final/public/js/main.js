/**
 * @file Frontend game controller using Extrapolation for smooth rendering
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
    
    // Server update control - 5 updates per second
    // Change this line for different update frequencies:
    // const UPDATE_INTERVAL = 1000;  // 1 Hz (1 update per second)
    // const UPDATE_INTERVAL = 500;   // 2 Hz (2 updates per second)  
    // const UPDATE_INTERVAL = 200;   // 5 Hz (5 updates per second) - DEFAULT
    // const UPDATE_INTERVAL = 100;   // 10 Hz (10 updates per second)
    const UPDATE_INTERVAL = Math.round(1000/60);  // 17ms for 60 Hz
    let updateIntervalId;
    
    // State for extrapolation
    let serverState = null;
    let lastServerTimestamp = 0;

    // --- Control Logic ---
    startStopBtn.addEventListener('click', () => {
        isRunning = !isRunning;
        startStopBtn.textContent = isRunning ? 'Stop' : 'Start';
        
        if (isRunning) {
            // Start server updates at 5 Hz
            updateFromServer(); // Get initial state
            updateIntervalId = setInterval(updateFromServer, UPDATE_INTERVAL);
            
            // Start render loop at 60 FPS
            animationFrameId = requestAnimationFrame(renderLoop);
        } else {
            clearInterval(updateIntervalId);
            cancelAnimationFrame(animationFrameId);
        }
    });

    resetBtn.addEventListener('click', async () => {
        if (isRunning) {
            isRunning = false;
            startStopBtn.textContent = 'Start';
            clearInterval(updateIntervalId);
            cancelAnimationFrame(animationFrameId);
        }

        await fetch('api/game_tick.php?action=reset');

        worldElement.innerHTML = '';
        frameCounter.textContent = '0';
        entityCounter.textContent = '0';
        serverState = null;
        console.log("Game Reset!");
    });

    // --- Server Update Function ---
    async function updateFromServer() {
        if (!isRunning) return;

        try {
            // Don't send deltaTime - let server handle fixed timesteps
            const response = await fetch('api/game_tick.php');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            serverState = data;
            lastServerTimestamp = Date.now() / 1000; // Client timestamp when we received this update

        } catch (error) {
            console.error("Error updating from server:", error);
            isRunning = false;
            startStopBtn.textContent = 'Start';
            clearInterval(updateIntervalId);
        }
    }

    // --- Render Loop (60 FPS) ---
    function renderLoop() {
        if (!isRunning) return;

        render();
        animationFrameId = requestAnimationFrame(renderLoop);
    }

    // --- Extrapolation Rendering ---
    function render() {
        if (!serverState) return;

        const now = Date.now() / 1000;
        const timeSinceUpdate = now - lastServerTimestamp;

        worldElement.innerHTML = '';
        frameCounter.textContent = serverState.frame;
        entityCounter.textContent = serverState.entities.length;

        const worldWidth = worldElement.clientWidth;
        const worldHeight = worldElement.clientHeight;

        // Render each entity with extrapolation where appropriate
        serverState.entities.forEach(entity => {
            let renderX = entity.x;
            let renderY = entity.y;

            // Apply extrapolation only to moving entities
            if (entity.type === 'LightningBolt' || entity.type === 'Skeleton') {
                // Extrapolate position based on velocity and time since last update
                renderX = entity.x + (entity.vx * timeSinceUpdate);
                renderY = entity.y + (entity.vy * timeSinceUpdate);

                 // Smart bounds for Skeleton (predict bouncing)
                if (entity.type === 'Skeleton') {
                    if (renderX > 100) {
                        let overshoot = renderX - 100;
                        renderX = 100 - overshoot; // Bounce back from right wall
                    } else if (renderX < 0) {
                        let overshoot = -renderX;
                        renderX = overshoot; // Bounce back from left wall
                    }
                }

                // Clamp to world bounds for safety
                renderX = Math.max(0, Math.min(100, renderX));
                renderY = Math.max(0, Math.min(100, renderY));
            }
            // Minion, Spawner, and Statue use server position directly (no extrapolation)

            const entityDiv = document.createElement('div');
            entityDiv.className = `entity ${entity.type.toLowerCase()}`;
            entityDiv.style.left = `${(renderX / 100) * worldWidth}px`;
            entityDiv.style.top = `${(renderY / 100) * worldHeight}px`;

            worldElement.appendChild(entityDiv);
        });
    }
});