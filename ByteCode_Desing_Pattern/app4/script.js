const API_URL = 'http://localhost/Program_with_deepak/ByteCode_Desing_Pattern/app4/api.php';

let currentTurn = 0;
let gameState = null;

async function initGame() {
    document.getElementById('game-over-modal').style.display = 'none';
    document.getElementById('turn-info').textContent = 'Loading...';
    document.getElementById('game-log').innerHTML = '<div>Initializing battle...</div>';
    document.getElementById('wizards-container').innerHTML = '<div class="loading">Loading wizards...</div>';
    
    try {
        const response = await fetch(`${API_URL}?action=start`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();
        renderGame(data);
    } catch (error) {
        console.error('Init error:', error);
        document.getElementById('wizards-container').innerHTML = `<div class="loading">Error: ${error.message}</div>`;
    }
}

async function castSpell(casterId, spellName) {
    if (casterId !== currentTurn) {
        addToLog(`Not ${gameState.wizards[casterId].Name}'s turn!`);
        return;
    }
    
    // Disable all buttons to prevent multiple clicks
    document.querySelectorAll('.spell-btn').forEach(btn => btn.disabled = true);
    
    try {
        const response = await fetch(`${API_URL}?action=cast_spell`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ casterId, spellName })
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || `HTTP ${response.status}`);
        }
        
        const data = await response.json();
        renderGame(data);
    } catch (error) {
        console.error('Cast error:', error);
        addToLog(`Spell failed: ${error.message}`);
        // Re-enable buttons for the current player on failure
        document.querySelectorAll('.spell-btn').forEach(btn => {
            btn.disabled = parseInt(btn.dataset.wizardId) !== currentTurn;
        });
    }
}

function renderGame(state) {
    gameState = state;
    currentTurn = state.currentTurn || 0;
    
    // Update turn info
    if (state.isOver) {
        document.getElementById('turn-info').textContent = 'Game Over';
    } else {
        document.getElementById('turn-info').textContent = `${state.wizards[currentTurn].Name}'s Turn`;
    }
    
    // Update log
    if (state.log && state.log.length > 0) {
        state.log.forEach(msg => addToLog(msg));
    }
    
    // Render wizards
    const wizardsHtml = state.wizards.map((wizard, id) => {
        const isActive = id === currentTurn && !state.isOver;
        const spells = state.spells[id];
                return `
            <div class="wizard ${isActive ? 'active' : ''}">
                <h2>${wizard.Name}</h2>
                <div class="stats">
                    <div>Health: ${wizard.Health}/100</div>
                    <div>Wisdom: ${wizard.Wisdom}</div>
                    <div>Agility: ${wizard.Agility}</div>
                </div>
                <div class="spells">
                    ${spells.map(spell => `
                        <button class="spell-btn" 
                                onclick="castSpell(${id}, '${spell}')"
                                data-wizard-id="${id}"
                                ${!isActive ? 'disabled' : ''}>
                            ${spell}
                        </button>
                    `).join('')}
                </div>
            </div>
        `;
    }).join('');
    
    document.getElementById('wizards-container').innerHTML = wizardsHtml;
    
    // Show game over modal if the game is finished
    if (state.isOver) {
        document.getElementById('winner-text').textContent = `${state.winner} wins!`;
        document.getElementById('game-over-modal').style.display = 'block';
    }
}

function addToLog(message) {
    const log = document.getElementById('game-log');
    const div = document.createElement('div');
    div.textContent = `> ${message}`;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
}

// Start the game when the page is loaded
document.addEventListener('DOMContentLoaded', initGame);