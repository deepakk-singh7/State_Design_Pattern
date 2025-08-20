const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const healthEl = document.getElementById('playerHealth');
const resetButton = document.getElementById('resetButton');

// Enum-like objects.
const Action = {
    MOVE: 'move',
    RESET: 'reset'
};

const Direction = {
    UP: 'up',
    DOWN: 'down',
    LEFT: 'left',
    RIGHT: 'right'
};

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
        // case 'ArrowUp': direction = 'up'; break;
        // case 'ArrowDown': direction = 'down'; break;
        // case 'ArrowLeft': direction = 'left'; break;
        // case 'ArrowRight': direction = 'right'; break;
        case 'ArrowUp': direction = Direction.UP; break;
        case 'ArrowDown': direction = Direction.DOWN; break;
        case 'ArrowLeft': direction = Direction.LEFT; break;
        case 'ArrowRight': direction = Direction.RIGHT; break;
    }
    if (direction) {
        e.preventDefault();
        sendAction('move', { direction });
    }
});

resetButton.addEventListener('click', () => sendAction('reset'));

sendAction('reset');