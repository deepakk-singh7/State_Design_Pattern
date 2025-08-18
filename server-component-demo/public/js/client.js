// // Get references to the HTML elements we need to interact with.
// const canvas = document.getElementById('gameCanvas');
// const ctx = canvas.getContext('2d'); 
// const healthEl = document.getElementById('playerHealth'); 
// const resetButton = document.getElementById('resetButton'); 

// /**
//  * Renders the entire game state received from the server.
//  * @param {object} state - The game state object from the server.
//  */
// function renderGameState(state) {
//     // Clear the entire canvas before drawing the new frame.
//     ctx.clearRect(0, 0, canvas.width, canvas.height);
    
//     // Loop through every entity sent by the server (e.g., player, health_pack).
//     for (const entity of Object.values(state)) {
//         // Find the component that contains rendering data.
//         const renderData = entity.components['RenderDataComponent'];
        
//         // Only draw the entity if it has a RenderDataComponent.
//         if (renderData) {
//             ctx.fillStyle = renderData.color;
//             ctx.fillRect(entity.x, entity.y, renderData.size, renderData.size);
//         }
//     }

//     // Update the player's health display on the UI.
//     healthEl.textContent = state.player.health;
// }

// /**
//  * Sends a command (action) to the server and updates the screen with the response.
//  * @param {string} action - The action to perform (e.g., 'move', 'reset').
//  * @param {object} data - Any additional data for the action (e.g., { direction: 'up' }).
//  */
// async function sendAction(action, data = {}) {
//     try {
//         // Use the Fetch API to send a POST request to our PHP backend.
//         const response = await fetch('/api/game.php', {
//             method: 'POST',
//             headers: { 'Content-Type': 'application/json' },
//             body: JSON.stringify({ action, data })
//         });
        
//         // Parse the JSON response from the server into a JavaScript object.
//         const gameState = await response.json();
        
//         // Render the new game state returned by the server.
//         renderGameState(gameState);
//     } catch (error) {
//         // Log any network or server errors to the browser console.
//         console.error("Error communicating with server:", error);
//     }
// }

// // --- Event Listeners ---

// // Listen for keyboard presses on the entire window.
// window.addEventListener('keydown', (e) => {
//     let direction = null;
//     // Determine the direction based on the key pressed.
//     switch (e.key) {
//         case 'ArrowUp': direction = 'up'; break;
//         case 'ArrowDown': direction = 'down'; break;
//         case 'ArrowLeft': direction = 'left'; break;
//         case 'ArrowRight': direction = 'right'; break;
//     }
    
//     // If a valid arrow key was pressed...
//     if (direction) {
//         e.preventDefault(); 
//         sendAction('move', { direction }); // ...send the 'move' action to the server.
//     }
// });

// // Listen for clicks on the reset button.
// resetButton.addEventListener('click', () => sendAction('reset'));

// // --- Initial Game Load ---
// // Send the 'reset' action when the script first loads to get the initial game state from the server.
// sendAction('reset');


const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const healthEl = document.getElementById('playerHealth');
const resetButton = document.getElementById('resetButton');

function renderGameState(state) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Loop through the array of entities received from the server.
    for (const entity of state) {
        const renderData = entity.components['RenderDataComponent'];
        if (renderData) {
            ctx.fillStyle = renderData.color;
            ctx.fillRect(entity.x, entity.y, renderData.size, renderData.size);
        }
    }

    // Find the player object within the state array to display its health.
    const player = state.find(e => e.id === 'player');
    if (player) {
        healthEl.textContent = player.health;
    }
}

async function sendAction(action, data = {}) {
    try {
        const response = await fetch('/api/game.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, data })
        });
        console.log('Response :: ', response);
        const gameState = await response.json();
        console.log('GameState Data :: ', gameState);
        renderGameState(gameState);
    } catch (error) {
        console.error("Error communicating with server:", error);
    }
}

window.addEventListener('keydown', (e) => {
    let direction = null;
    switch (e.key) {
        case 'ArrowUp': direction = 'up'; break;
        case 'ArrowDown': direction = 'down'; break;
        case 'ArrowLeft': direction = 'left'; break;
        case 'ArrowRight': direction = 'right'; break;
    }
    if (direction) {
        e.preventDefault();
        sendAction('move', { direction });
    }
});

resetButton.addEventListener('click', () => sendAction('reset'));

sendAction('reset');