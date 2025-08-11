import { GameAPI } from './components/GameAPI.js';
import { Renderer } from './components/Renderer.js';

/**
 * The main game controller. Orchestrates the game loops, state, and components.
 */
export class Game {
    constructor(config) {
        this.config = config; // Contains DOM elements
        this.api = new GameAPI();
        this.renderer = new Renderer(config.worldElement);
        
        this.isRunning = false;
        this.serverState = null;
        
        this.updateIntervalId = null;
        this.renderLoopId = null;
        
        // This should match the server's intended update frequency
        this.UPDATE_INTERVAL = 200; // 200ms = 5 Hz
        // this.UPDATE_INTERVAL = 33; // (Approx. 30 Hz)
        // this.UPDATA_INTERVAL = 16.61 // 60Hz
        // this.UPDATE_INTERVAL = 1000; // (1 Hz - only one update per second!)
    }
    
    /**
     * Initializes the game by setting up event listeners.
     */
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
        this._serverUpdateLoop();
        this.updateIntervalId = setInterval(() => this._serverUpdateLoop(), this.UPDATE_INTERVAL);
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
        this.serverState = null;
        console.log("Game Reset!");
    }
    
    /**
     * The server update loop function.
     * @private
     */
    async _serverUpdateLoop() {
        const newState = await this.api.getState();
        if (newState) {
            this.serverState = newState;
        } else {
            this.togglePause();
        }
    }
    
    /**
     * The render loop function.
     * @private
     */
    _renderLoop() {
        if (!this.isRunning) return;

        if (this.serverState) {
            // We calculate time passed since the SERVER generated the state,
            // not when our client received it. This accounts for network lag.
            const timeSinceUpdate = (Date.now() / 1000) - this.serverState.timestamp;
            
            this.renderer.render(this.serverState, timeSinceUpdate);
            
            this.config.frameCounter.textContent = this.serverState.frame;
            this.config.entityCounter.textContent = this.serverState.entities.length;
        }
        
        this.renderLoopId = requestAnimationFrame(() => this._renderLoop());
    }
}