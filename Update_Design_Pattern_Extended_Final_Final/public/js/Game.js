// import { GameAPI } from './components/GameAPI.js';
// import { Renderer } from './components/Renderer.js';

// /**
//  * The main game controller. Orchestrates the game loops, state, and components.
//  */
// export class Game {
//      constructor(domElements, appConfig) { 
//         this.config = domElements;
//         this.api = new GameAPI();
//         this.renderer = new Renderer(this.config.worldElement, this.config.clientFpsCounter);
//         this.isRunning = false;
        
//         this.stateBuffer = [];
//         this.updateIntervalId = null;
//         this.renderLoopId = null;

//         // Use the passed-in config value
//         this.gameInterval = appConfig.UPDATE_INTERVAL_MS;
//     }

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
//         // Fetch initial state immediately
//         this._serverUpdateLoop();
//         // Set up the regular server update loop
//         this.updateIntervalId = setInterval(() => this._serverUpdateLoop(), this.gameInterval);
//         // Start the rendering loop
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
//         this.stateBuffer = []; // Clear the state buffer
//         console.log("Game Reset!");
//     }

//     /**
//      * The server update loop function. Fetches state and pushes it to a buffer.
//      * @private
//      */
//     async _serverUpdateLoop() {
//         const newState = await this.api.getState();
//         if (newState) {
//             this.stateBuffer.push(newState);
//             // Keep the buffer from growing too large
//             if (this.stateBuffer.length > 5) {
//                 this.stateBuffer.shift();
//             }
//         } else {
//             this.togglePause();
//         }
//     }

//     /**
//      * The render loop function. Handles interpolation logic.
//      * @private
//      */
//     _renderLoop() {
//         if (!this.isRunning) return;

//         // We need two states (a "from" and "to") to interpolate
//         if (this.stateBuffer.length < 2) {
//             requestAnimationFrame(() => this._renderLoop());
//             return;
//         }

//         // The "from" state is the second to last one in our buffer
//         const prevState = this.stateBuffer[this.stateBuffer.length - 2];
//         // The "to" state is the most recent one
//         const targetState = this.stateBuffer[this.stateBuffer.length - 1];

//         // How much time has passed since the "from" state was generated?
//         const timeToInterpolate = (Date.now() - this.gameInterval) / 1000;
//         const timeSincePrevState = timeToInterpolate - prevState.timestamp;
        
//         // How long is the total time between our two states?
//         const timeBetweenStates = targetState.timestamp - prevState.timestamp;

//         // Calculate our alpha (0.0 to 1.0)
//         // We ensure alpha doesn't go above 1, which would be extrapolation
//         const interpolationFactor = Math.min(1, timeSincePrevState / timeBetweenStates);

//         this.renderer.render(prevState, targetState, interpolationFactor);

//         this.config.frameCounter.textContent = targetState.frame;
//         this.config.entityCounter.textContent = targetState.entities.length;

//         requestAnimationFrame(() => this._renderLoop());
//     }
// }


import { GameAPI } from './components/GameAPI.js';
import { Renderer } from './components/Renderer.js';

/**
 * The main game controller. Orchestrates the game loops, state, and components.
 */
export class Game {
     constructor(domElements, appConfig) { 
        this.config = domElements;
        this.api = new GameAPI();
        // Pass the fpsCounterElement to the Renderer constructor
        this.renderer = new Renderer(this.config.worldElement, this.config.clientFpsCounter);
        this.isRunning = false;
        
        this.stateBuffer = [];
        this.updateIntervalId = null;
        this.renderLoopId = null;

        // Use the passed-in config value
        this.gameInterval = appConfig.UPDATE_INTERVAL_MS;
        // --- ADDED: Frame rate limiting properties ---
        this.targetFrameInterval = 1000 / appConfig.CLIENT_TARGET_FPS;
        this.lastRenderTime = performance.now();
        // ---------------------------------------------
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
        this.updateIntervalId = setInterval(() => this._serverUpdateLoop(), this.gameInterval);
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
        this.config.clientFpsCounter.textContent = '0';
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

        // --- NEW: Frame Limiter Logic ---
        const currentTime = performance.now();
        const deltaTime = currentTime - this.lastRenderTime;

        // Only render if enough time has passed
        if (deltaTime >= this.targetFrameInterval) {

            // We need two states (a "from" and "to") to interpolate
            if (this.stateBuffer.length < 2) {
                requestAnimationFrame(() => this._renderLoop());
                return;
            }

            // The "from" state is the second to last one in our buffer
            const prevState = this.stateBuffer[this.stateBuffer.length - 2];
            // The "to" state is the most recent one
            const targetState = this.stateBuffer[this.stateBuffer.length - 1];

            // How much time has passed since the "from" state was generated?
            const timeToInterpolate = (Date.now() - this.gameInterval) / 1000;
            const timeSincePrevState = timeToInterpolate - prevState.timestamp;
            
            // How long is the total time between our two states?
            const timeBetweenStates = targetState.timestamp - prevState.timestamp;

            // Calculate our alpha (0.0 to 1.0)
            // We ensure alpha doesn't go above 1, which would be extrapolation
            const interpolationFactor = Math.min(1, timeSincePrevState / timeBetweenStates);

            this.renderer.render(prevState, targetState, interpolationFactor);

            this.config.frameCounter.textContent = targetState.frame;
            this.config.entityCounter.textContent = targetState.entities.length;

            this.lastRenderTime = currentTime - (deltaTime % this.targetFrameInterval);
        }
        // ----------------------------------

        requestAnimationFrame(() => this._renderLoop());
    }
}
