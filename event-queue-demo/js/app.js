document.addEventListener('DOMContentLoaded', () => {
    // Get the DOM element for logging output.
    const logOutput = document.getElementById('log-output');

    // --- CONSTANTS to mimic ENUMS ---
    // Centralizes API action strings to avoid typos.
    const API_ACTIONS = {
        PLAY_SOUND: 'playSound',
        UPDATE: 'update',
        RESET: 'reset',
        INIT: 'init'
    };

    /**
     * Appends new log messages to the console display.
     * @param {string[]} messages - An array of HTML strings to display.
     */
    function updateLog(messages) {
        if (messages && messages.length > 0) {
            messages.forEach(msg => {
                logOutput.innerHTML += msg;
            });
            // Automatically scroll to the bottom of the console.
            logOutput.scrollTop = logOutput.scrollHeight;
        }
    }

    /**
     * Sends a request to the server API and updates the log with the response.
     * @param {string} url - The API endpoint to fetch.
     */
    async function sendRequest(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            updateLog(data.log);
        } catch (error) {
            console.error("Fetch error:", error);
            updateLog([`<p class="error">Error communicating with server.</p>`]);
        }
    }

    /**
     * A helper function to create and send a playSound request.
     * @param {HTMLElement} buttonElement - The button that was clicked.
     */
    function playSound(buttonElement) {
        const { sound, volume } = buttonElement.dataset;
        sendRequest(`api/api.php?action=${API_ACTIONS.PLAY_SOUND}&soundId=${sound}&volume=${volume}`);
    }


    // --- EVENT LISTENERS ---
    // Attach listeners to all the control buttons.

    document.getElementById('jump-btn').addEventListener('click', (e) => playSound(e.target));
    document.getElementById('hit-btn').addEventListener('click', (e) => playSound(e.target));
    document.getElementById('coin-btn').addEventListener('click', (e) => playSound(e.target));

    // The spam button is a special case to demonstrate aggregation.
    document.getElementById('spam-btn').addEventListener('click', (e) => {
        const { sound } = e.target.dataset;
        updateLog([`<p class="info">--- Spamming 5 '${sound}' requests ---</p>`]);
        // Fire 5 requests in quick succession.
        for (let i = 0; i < 5; i++) {
            playSound(e.target);
        }
    });

    // The process button simulates the game engine's main loop update.
    document.getElementById('process-btn').addEventListener('click', () => {
        sendRequest(`api/api.php?action=${API_ACTIONS.UPDATE}`);
    });
    
    // The reset button clears the server-side session.
    document.getElementById('reset-btn').addEventListener('click', () => {
        logOutput.innerHTML = ''; // Also clear the client-side log.
        sendRequest(`api/api.php?action=${API_ACTIONS.RESET}`);
    });

    // --- INITIALIZATION ---
    // Send an initial request to ensure the server engine is ready.
    sendRequest(`api/api.php?action=${API_ACTIONS.INIT}`);
});