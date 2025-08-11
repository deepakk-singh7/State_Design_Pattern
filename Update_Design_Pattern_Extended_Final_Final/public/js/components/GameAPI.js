/**
 * Handles all communication with the server API.
 */
export class GameAPI {
    constructor(baseUrl = 'api/') {
        this.baseUrl = baseUrl;
    }

    /**
     * Fetches the latest game state from the server.
     * @returns {Promise<Object|null>} The server state object or null on error.
     */
    async getState() {
        try {
            const response = await fetch(`${this.baseUrl}game_tick.php`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error("Error fetching game state:", error);
            return null;
        }
    }

    /**
     * Sends a reset request to the server.
     * @returns {Promise<boolean>} True if reset was successful.
     */
    async reset() {
        try {
            await fetch(`${this.baseUrl}game_tick.php?action=reset`);
            return true;
        } catch (error) {
            console.error("Error resetting game:", error);
            return false;
        }
    }
}