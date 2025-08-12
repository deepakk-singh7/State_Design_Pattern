// import { GameAPI } from './components/GameAPI.js';
// import { Renderer } from './components/Renderer.js';

// /**
//  * The main game controller. Orchestrates the game loops, state, and components.
//  */
// export class Game {
//     constructor(config) {
//         this.config = config; // Contains DOM elements
//         this.api = new GameAPI();
//         this.renderer = new Renderer(config.worldElement);
        
//         this.isRunning = false;
//         this.serverState = null;
        
//         this.updateIntervalId = null;
//         this.renderLoopId = null;
        
//         // This should match the server's intended update frequency
//         this.UPDATE_INTERVAL = 1000; // (1 Hz - only one update per second!)
//         // this.UPDATE_INTERVAL = 200; // 200ms = 5 Hz
//         // this.UPDATE_INTERVAL = 100; // 100ms = 10 Hz
//         // this.UPDATE_INTERVAL = 50; // 50ms = 20Hz
//         // this.UPDATE_INTERVAL = 20; // 20ms = 50Hz
//         // this.UPDATA_INTERVAL = 16.61 // 60Hz
//     }
    
//     /**
//      * Initializes the game by setting up event listeners.
//      */
//     init() {
//         this.config.startStopBtn.addEventListener('click', () => this.togglePause());
//         this.config.resetBtn.addEventListener('click', () => this.reset());
//     }

//     togglePause() {
//         this.isRunning = !this.isRunning;
//         this.config.startStopBtn.textContent = this.isRunning ? 'Stop' : 'Start';
        
//         if (this.isRunning) {
//             this.start();
//         } else {
//             this.stop();
//         }
//     }

//     start() {
//         this._serverUpdateLoop();
//         this.updateIntervalId = setInterval(() => this._serverUpdateLoop(), this.UPDATE_INTERVAL);
//         this.renderLoopId = requestAnimationFrame(() => this._renderLoop());
//     }

//     stop() {
//         clearInterval(this.updateIntervalId);
//         cancelAnimationFrame(this.renderLoopId);
//     }
    
//     async reset() {
//         if (this.isRunning) {
//             this.stop();
//             this.isRunning = false;
//             this.config.startStopBtn.textContent = 'Start';
//         }
        
//         await this.api.reset();
        
//         this.renderer.clear();
//         this.config.frameCounter.textContent = '0';
//         this.config.entityCounter.textContent = '0';
//         this.serverState = null;
//         console.log("Game Reset!");
//     }
    
//     /**
//      * The server update loop function.
//      * @private
//      */
//     async _serverUpdateLoop() {
//         const newState = await this.api.getState();
//         if (newState) {
//             this.serverState = newState;
//         } else {
//             this.togglePause();
//         }
//     }
    
//     /**
//      * The render loop function.
//      * @private
//      */
//     _renderLoop() {
//         if (!this.isRunning) return;

//         if (this.serverState) {
//             // We calculate time passed since the SERVER generated the state,
//             // not when our client received it. This accounts for network lag.
//             const timeSinceUpdate = (Date.now() / 1000) - this.serverState.timestamp;
            
//             this.renderer.render(this.serverState, timeSinceUpdate);
            
//             this.config.frameCounter.textContent = this.serverState.frame;
//             this.config.entityCounter.textContent = this.serverState.entities.length;
//         }
        
//         this.renderLoopId = requestAnimationFrame(() => this._renderLoop());
//     }
// }


import { GameAPI } from './components/GameAPI.js';
import { Renderer } from './components/Renderer.js';
import { AppConfig } from './config.js'; // Import the new config

/**
 * The main game controller. Orchestrates the game loops, state, and components.
 */
export class Game {
    constructor(config) {
        this.config = config; // Contains DOM elements
        this.api = new GameAPI();
        this.renderer = new Renderer(config.worldElement);

        this.isRunning = false;
        
        // --- Interpolation State ---
        this.stateBuffer = []; // Buffer to store recent server states
        this.updateIntervalId = null;
        this.renderLoopId = null;

        // Load settings from config file
        this.updateInterval = AppConfig.UPDATE_INTERVAL_MS;
        this.interpolationDelay = AppConfig.INTERPOLATION_DELAY_MS;
    }

    init() {
        this.config.startStopBtn.addEventListener('click', () => this.togglePause());
        this.config.resetBtn.addEventListener('click', () => this.reset());
    }

    togglePause() {
        this.isRunning = !this.isRunning;
        this.config.startStopBtn.textContent = this.isRunning ? 'Stop' : 'Start';

        if (this.isRunning) {
            this.start();
        } else {
            this.stop();
        }
    }

    start() {
        // Fetch initial state immediately
        this._serverUpdateLoop();
        // Set up the regular server update loop
        this.updateIntervalId = setInterval(() => this._serverUpdateLoop(), this.updateInterval);
        // Start the rendering loop
        this.renderLoopId = requestAnimationFrame(() => this._renderLoop());
    }

    stop() {
        clearInterval(this.updateIntervalId);
        cancelAnimationFrame(this.renderLoopId);
    }

    async reset() {
        if (this.isRunning) {
            this.stop();
            this.isRunning = false;
            this.config.startStopBtn.textContent = 'Start';
        }
        await this.api.reset();
        this.renderer.clear();
        this.config.frameCounter.textContent = '0';
        this.config.entityCounter.textContent = '0';
        this.stateBuffer = []; // Clear the state buffer
        console.log("Game Reset!");
    }

    /**
     * The server update loop function. Fetches state and pushes it to a buffer.
     * @private
     */
    async _serverUpdateLoop() {
        const newState = await this.api.getState();
        if (newState) {
            newState.receivedAt = Date.now(); // Record when we received the state
            this.stateBuffer.push(newState);
            // Keep the buffer from growing too large
            if (this.stateBuffer.length > 5) {
                this.stateBuffer.shift();
            }
        } else {
            this.togglePause();
        }
    }

    /**
     * The render loop function. Handles interpolation logic.
     * @private
     */
    _renderLoop() {
        if (!this.isRunning) return;

        // We need at least two states to interpolate
        if (this.stateBuffer.length < 2) {
            this.renderLoopId = requestAnimationFrame(() => this._renderLoop());
            return;
        }

        // Calculate the timestamp we want to render
        const renderTimestamp = (Date.now() - this.interpolationDelay) / 1000;

        // Find the two states in our buffer that our renderTimestamp falls between
        let targetStateIndex = -1;
        for (let i = this.stateBuffer.length - 1; i >= 0; i--) {
            if (this.stateBuffer[i].timestamp <= renderTimestamp) {
                targetStateIndex = i + 1;
                break;
            }
        }

        // Ensure we have a valid target and a state before it
        if (targetStateIndex === -1 || targetStateIndex >= this.stateBuffer.length) {
            this.renderLoopId = requestAnimationFrame(() => this._renderLoop());
            return; // Not enough data to interpolate yet
        }

        const targetState = this.stateBuffer[targetStateIndex];
        const previousState = this.stateBuffer[targetStateIndex - 1];

        const timeBetweenStates = targetState.timestamp - previousState.timestamp;
        const timeSincePreviousState = renderTimestamp - previousState.timestamp;

        // This is our interpolation factor (alpha), clamped between 0 and 1
        const interpolationFactor = Math.max(0, Math.min(1, timeSincePreviousState / timeBetweenStates));
        
        // Pass the two states and the factor to the renderer
        this.renderer.render(previousState, targetState, interpolationFactor);

        // Update UI counters with the data from our "target" state
        this.config.frameCounter.textContent = targetState.frame;
        this.config.entityCounter.textContent = targetState.entities.length;

        this.renderLoopId = requestAnimationFrame(() => this._renderLoop());
    }
}