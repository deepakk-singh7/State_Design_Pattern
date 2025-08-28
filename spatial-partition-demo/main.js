/** @type {HTMLCanvasElement} */
const canvas = document.getElementById('gameCanvas');
/** @type {CanvasRenderingContext2D} */
const ctx = canvas.getContext('2d');

/**
 * Constants for string literals used in the application.
 */
const CONSTANTS = {
    API_ACTIONS: {
        INIT: 'init',
        UPDATE: 'update'
    },
    COLORS: {
        NEAR: '#e74c3c', // Red
        NORMAL: '#3498db', // Blue
        GRID: '#eee'
    }
};

/** This will hold the configuration loaded from the server.
 * @type {object|null}
 */
let config = null;

/**
 * The array of unit objects received from the backend.
 * @type {Array<object>}
 */
let units = [];

/**
 * The ID of the currently running interval, used to stop it.
 * @type {number|null}
 */
let simulationIntervalId = null;

/**
 * Draws the grid lines based on the loaded configuration.
 */
function drawGrid() {
    ctx.strokeStyle = CONSTANTS.COLORS.GRID;
    ctx.lineWidth = 1;
    const cellSize = config.WORLD_SIZE / config.NUM_CELLS;
    for (let i = 1; i < config.NUM_CELLS; i++) {
        const pos = i * cellSize;
        ctx.beginPath();
        ctx.moveTo(pos, 0);
        ctx.lineTo(pos, config.WORLD_SIZE);
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(0, pos);
        ctx.lineTo(config.WORLD_SIZE, pos);
        ctx.stroke();
    }
}

/**
 * Draws all the units on the canvas.
 */
function drawUnits() {
    units.forEach(unit => {
        ctx.beginPath();
        ctx.arc(unit.x, unit.y, config.UNIT_RADIUS, 0, Math.PI * 2);
        ctx.fillStyle = unit.isNear ? CONSTANTS.COLORS.NEAR : CONSTANTS.COLORS.NORMAL;
        ctx.fill();
    });
}

/**
 * This is the core logic function. It fetches the new state from the
 * server and then redraws the canvas. It will be called repeatedly by setInterval.
 */
async function updateAndDraw() {
    const response = await fetch(`api.php?action=${CONSTANTS.API_ACTIONS.UPDATE}`);
    const data = await response.json();

    if (data && data.units) {
        units = data.units;
    }

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawGrid();
    drawUnits();
}

/**
 * Starts or restarts the simulation with a specified target FPS.
 * This function now uses setInterval.
 * @param {number} fps The target frames per second.
 */
function startSimulation(fps) {
    if (simulationIntervalId) {
        clearInterval(simulationIntervalId);
    }
    const interval = fps > 0 ? 1000 / fps : 16;
    simulationIntervalId = setInterval(updateAndDraw, interval);
}

/**
 * Initializes the simulation.
 * It fetches the configuration and initial state, sets up the canvas,
 * and starts the simulation loop.
 */
async function init() {
    try {
        console.log("Initializing simulation...");
        const response = await fetch(`api.php?action=${CONSTANTS.API_ACTIONS.INIT}`);
        console.log('Response :: ', response);
        const data = await response.json();
        console.log('Data :: ', data);

        if (!data || !data.config || !data.units) {
            console.error("Invalid data received from initialization API.", data);
            alert("Error: Could not initialize the simulation. Check the console for details.");
            return;
        }

        config = data.config;
        units = data.units;

        canvas.width = config.WORLD_SIZE;
        canvas.height = config.WORLD_SIZE;

        console.log(`Loaded config and ${units.length} units.`);

        startSimulation(20);

    } catch (error) {
        console.error("Failed to initialize:", error);
        alert("A critical error occurred while starting the simulation. Check the console.");
    }
}

// Kick off the application.
init();