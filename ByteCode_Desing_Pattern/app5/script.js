const API_URL = 'http://localhost/Program_with_deepak/ByteCode_Desing_Pattern/app4/api.php';

let currentTurn = 0;
let gameState = null;

async function initGame() {
    // Reset UI
    document.getElementById('game-over-modal').style.display = 'none';
    document.getElementById('turn-info').textContent = 'Loading...';
    document.getElementById('game-log').innerHTML = '<div>Initializing battle...</div>';
    document.getElementById('wizards-container').innerHTML = '<div class="loading">Loading wizards...</div>';
    
    try {
        const response = await fetch(`${API_URL}?action=start`);
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.error || `HTTP ${response.status}`);
        }
        const data = await response.json();
        console.log('Data :: ',data);
        renderGame(data);
        addToLog('Battle initialized! Let the duel begin!');
    } catch (error) {
        console.error('Init error:', error);
        document.getElementById('wizards-container').innerHTML = `<div class="loading">Error: ${error.message}</div>`;
        addToLog(`Failed to start game: ${error.message}`);
    }
}

async function castSpell(casterId, spellName) {
    if (casterId !== currentTurn) {
        addToLog(`Not ${gameState.wizards[casterId].Name}'s turn!`);
        return;
    }
    
    // Show spell being cast immediately
    addToLog(`${gameState.wizards[casterId].Name} is casting ${spellName}...`);
    
    // Disable all buttons to prevent multiple clicks
    document.querySelectorAll('.spell-btn').forEach(btn => {
        btn.disabled = true;
        btn.textContent = btn.textContent.includes('...') ? btn.textContent : btn.textContent + '...';
    });
    
    try {
        const response = await fetch(`${API_URL}?action=cast_spell`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ casterId, spellName })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.error || `HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Data, Casting :: ', data);
        renderGame(data);
    } catch (error) {
        console.error('Cast error:', error);
        addToLog(`Spell failed: ${error.message}`);
        
        // Re-enable buttons for the current player on failure
        document.querySelectorAll('.spell-btn').forEach(btn => {
            const wizardId = parseInt(btn.dataset.wizardId);
            btn.disabled = wizardId !== currentTurn || gameState.isOver;
            // Remove the "..." from button text
            btn.textContent = btn.textContent.replace('...', '');
        });
    }
}

function renderGame(state) {
    gameState = state;
    currentTurn = state.currentTurn || 0;
    
    // Update turn info
    const turnInfoElement = document.getElementById('turn-info');
    if (state.isOver) {
        turnInfoElement.textContent = 'Game Over';
        turnInfoElement.className = 'turn-info game-over';
    } else {
        turnInfoElement.textContent = `${state.wizards[currentTurn].Name}'s Turn`;
        turnInfoElement.className = 'turn-info';
    }
    
    // Update log with new messages
    if (state.log && state.log.length > 0) {
        state.log.forEach(msg => addToLog(msg));
    }
    
    // Render wizards
    const wizardsHtml = state.wizards.map((wizard, id) => {
        const isActive = id === currentTurn && !state.isOver;
        const spells = state.spells[id] || [];
        const healthPercentage = Math.max(0, (wizard.Health / 100) * 100);
        
        return `
            <div class="wizard ${isActive ? 'active' : ''} ${wizard.Health <= 0 ? 'defeated' : ''}">
                <h2>${wizard.Name}</h2>
                <div class="stats">
                    <div class="stat-item">
                        <span>Health: ${wizard.Health}/100</span>
                        <div class="health-bar">
                            <div class="health-fill" style="width: ${healthPercentage}%"></div>
                        </div>
                    </div>
                    <div class="stat-item">Wisdom: ${wizard.Wisdom}</div>
                    <div class="stat-item">Agility: ${wizard.Agility}</div>
                </div>
                <div class="spells">
                    ${spells.map(spell => `
                        <button class="spell-btn ${isActive ? 'available' : 'disabled'}" 
                                onclick="castSpell(${id}, '${spell}')"
                                data-wizard-id="${id}"
                                ${!isActive ? 'disabled' : ''}
                                title="${getSpellDescription(spell)}">
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
        let winnerText;
        if (state.winner === "It's a draw!") {
            winnerText = "It's a draw!";
        } else {
            winnerText = `${state.winner} wins!`;
        }
        document.getElementById('winner-text').textContent = winnerText;
        
        // Add a small delay before showing the modal for dramatic effect
        setTimeout(() => {
            document.getElementById('game-over-modal').style.display = 'block';
        }, 1000);
    }
}

function getSpellDescription(spellName) {
    // Complete spell descriptions for all spells
    const descriptions = {
        'Fireball': 'Deals 15 damage to opponent',
        'Heal Self': 'Restores 20 health to yourself',
        'Wisdom Boost': 'Increases your wisdom by 5',
        'Agility Boost': 'Increases your agility by 3',
        'Lightning Bolt': 'Deals 10 damage to opponent',
        'Drain Wisdom': 'Reduces opponent wisdom by 3',
        'Life Steal': 'Deal 10 damage and heal 10',
        'Arcane Missile': 'Deals 12 damage to opponent',
        'Ice Shard': 'Deals 8 damage and reduces opponent agility by 1',
        'Strength Boost': 'Increases both your wisdom and agility by 2'
    };
    return descriptions[spellName] || 'Mysterious spell';
}

function addToLog(message) {
    const log = document.getElementById('game-log');
    const div = document.createElement('div');
    div.textContent = `> ${message}`;
    
    // Add timestamp for better UX
    const timestamp = new Date().toLocaleTimeString();
    div.setAttribute('title', timestamp);
    
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
    
    // Limit log entries to prevent memory issues
    const maxLogEntries = 100;
    while (log.children.length > maxLogEntries) {
        log.removeChild(log.firstChild);
    }
}

// Add keyboard shortcuts
document.addEventListener('keydown', (event) => {
    if (event.key === 'r' || event.key === 'R') {
        if (gameState && gameState.isOver) {
            initGame();
        }
    }
});

// Start the game when the page is loaded
document.addEventListener('DOMContentLoaded', () => {
    initGame();
    
    // Add some visual feedback
    addToLog('Welcome to the Wizard Duel Arena!');
    addToLog('Press R to restart when game is over');
});