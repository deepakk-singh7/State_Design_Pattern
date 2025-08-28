const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

// Constants must match the backend for correct rendering.
const WORLD_SIZE = 600;
const NUM_CELLS = 10;
const CELL_SIZE = WORLD_SIZE / NUM_CELLS;

/**
 * The array of unit objects received from the backend.
 * @type {Array<object>}
 */
let units = [];

// --- FPS Control Variables ---
let fpsInterval, then, startTime;
let animationFrameId; // Stores the ID of the animation frame for cancellation.

/**
 * Draws the grid lines on the canvas for visualization.
 */
function drawGrid() {
    ctx.strokeStyle = '#eee';
    ctx.lineWidth = 1;
    for (let i = 1; i < NUM_CELLS; i++) {
        // Draw vertical lines.
        ctx.beginPath();
        ctx.moveTo(i * CELL_SIZE, 0);
        ctx.lineTo(i * CELL_SIZE, WORLD_SIZE);
        ctx.stroke();

        // Draw horizontal lines.
        ctx.beginPath();
        ctx.moveTo(0, i * CELL_SIZE);
        ctx.lineTo(WORLD_SIZE, i * CELL_SIZE);
        ctx.stroke();
    }
}

/**
 * Draws all the units on the canvas as colored circles.
 */
function drawUnits() {
    units.forEach(unit => {
        ctx.beginPath();
        // Draw a circle for each unit. Radius is 4px.
        ctx.arc(unit.x, unit.y, 4, 0, Math.PI * 2); 
        // Color is red if 'isNear' is true, otherwise blue.
        ctx.fillStyle = unit.isNear ? '#e74c3c' : '#3498db'; 
        ctx.fill();
    });
}

/**
 * The main game loop, controlled by a fixed timestep.
 * It requests animation frames but only updates and draws at the target FPS.
 * @param {DOMHighResTimeStamp} currentTime The current time provided by requestAnimationFrame.
 */
async function gameLoop(currentTime) {
    // Keep the loop going.
    animationFrameId = requestAnimationFrame(gameLoop);

    // Calculate elapsed time since the last frame.
    const now = currentTime;
    const elapsed = now - then;

    // If enough time has elapsed, draw the next frame
    if (elapsed > fpsInterval) {
        // Get ready for next frame by setting then=now, but also adjust for 
        // potential lag by subtracting the remainder of elapsed % fpsInterval
        then = now - (elapsed % fpsInterval);

        // --- Core game logic ---
        const response = await fetch('api.php?action=update');
        units = await response.json();

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawGrid();
        drawUnits();
    }
}

// Function to start the animation with a specific FPS
function startLoop(fps) {
    // A '0' FPS means run at max speed
    if (fps === 0) {
        fpsInterval = 0;
    } else {
        fpsInterval = 1000 / fps;
    }
    
    then = window.performance.now();
    startTime = then;
    
    // Stop any previously running loop
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
    }
    
    // Start the new loop
    gameLoop();
}

// Initialization function
async function init() {
    console.log("Initializing simulation...");
    const response = await fetch('api.php?action=init');
    units = await response.json();
    console.log(`Loaded ${units.length} units.`);
    
    // Start the game loop at 20 FPS by default
    startLoop(60);
}

// Start the whole process
init();