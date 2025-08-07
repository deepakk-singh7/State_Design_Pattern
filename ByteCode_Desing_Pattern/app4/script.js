// --- CONFIGURATION ---
// IMPORTANT: Change this URL to where your api.php file is hosted.
const API_URL = 'http://localhost/Program_with_deepak/ByteCode_Desing_Pattern/app4/api.php'; // Example URL

// --- DOM ELEMENTS ---
const wizardsRow = document.getElementById('wizards-row');
const gameLog = document.getElementById('game-log');
const gameOverModal = document.getElementById('game-over-modal');
const winnerText = document.getElementById('winner-text');
const gameContainer = document.getElementById('game-container');

// --- GAME LOGIC ---

/**
 * Initializes or resets the game by fetching the initial state from the backend.
 */
async function initGame() {
    gameOverModal.classList.add('hidden');
    wizardsRow.innerHTML = '<p class="text-center md:col-span-2">Summoning wizards...</p>';
    try {
        const response = await fetch(`${API_URL}?action=start`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        // console.log('initGame Data :: ', data);
        renderGameState(data);
    } catch (error) {
        console.error("Failed to initialize game:", error);
        wizardsRow.innerHTML = `<p class="text-center text-red-500 md:col-span-2">Error connecting to the arcane realm. Check API_URL and server status. ${error.message}</p>`;
    }
}

/**
 * Handles a player casting a spell.
 * @param {number} casterId - The ID of the wizard casting the spell (0 or 1).
 * @param {string} spellName - The name of the spell being cast.
 */
async function handleSpellCast(casterId, spellName) {
    // Disable all spell buttons to prevent multiple casts
    document.querySelectorAll('.spell-btn').forEach(btn => btn.disabled = true);
    gameContainer.classList.add('is-casting');

    try {
        const response = await fetch(`${API_URL}?action=cast_spell`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ casterId, spellName })
        });
        console.log('response :: ', response);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        console.log('Data :: ', data);
        
        // Remove casting animation after it finishes
        setTimeout(() => gameContainer.classList.remove('is-casting'), 700);
        
        renderGameState(data);

    } catch (error) {
        console.error("Failed to cast spell:", error);
        updateLog([`An arcane interference prevented the spell! ${error.message}`]);
        // Re-enable buttons if the cast fails
        document.querySelectorAll('.spell-btn').forEach(btn => btn.disabled = false);
    }
}

/**
 * Renders the entire game state on the UI.
 * @param {object} state - The game state object from the backend.
 */
function renderGameState(state) {
    console.log("Rendering state:", state);

    // Render wizard cards
    wizardsRow.innerHTML = state.wizards.map((wizard, id) => createWizardCardHTML(wizard, id, state.spells[id])).join('');

    // Update log
    if (state.log && state.log.length > 0) {
        updateLog(state.log);
    }

    // Check for game over
    if (state.isOver) {
        winnerText.textContent = `${state.winner} is victorious!`;
        gameOverModal.classList.remove('hidden');
    } else {
        // Re-enable buttons if game is not over
        document.querySelectorAll('.spell-btn').forEach(btn => btn.disabled = false);
    }
}

/**
 * Creates the HTML for a single wizard card.
 */
function createWizardCardHTML(wizard, id, spells) {
    const healthPercentage = (wizard.Health / 100) * 100;
    const healthColor = healthPercentage > 60 ? 'bg-green-500' : healthPercentage > 30 ? 'bg-yellow-500' : 'bg-red-600';
    
    return `
        <div class="wizard-card rounded-lg p-4 md:p-6 shadow-lg border-2 ${id === 0 ? 'border-blue-400' : 'border-red-400'}">
            <h2 class="text-3xl font-fantasy text-center mb-4">${wizard.Name}</h2>
            <div class="text-center mb-4">
                <img src="https://placehold.co/150x150/2d3748/ecc94b?text=${wizard.Name.charAt(0)}" alt="${wizard.Name}" class="mx-auto rounded-full border-4 ${id === 0 ? 'border-blue-400' : 'border-red-400'}">
            </div>
            
            <!-- Stats -->
            <div class="mb-4">
                <p class="font-bold text-lg mb-1">Health: ${wizard.Health} / 100</p>
                <div class="health-bar-bg w-full rounded-full h-4">
                    <div class="health-bar ${healthColor} h-4 rounded-full" style="width: ${healthPercentage}%"></div>
                </div>
            </div>
            <ul class="mb-6 space-y-1">
                <li><strong>Wisdom:</strong> ${wizard.Wisdom}</li>
                <li><strong>Agility:</strong> ${wizard.Agility}</li>
            </ul>

            <!-- Spells -->
            <div class="grid grid-cols-2 gap-2">
                ${spells.map(spell => `
                    <button 
                        onclick="handleSpellCast(${id}, '${spell}')"
                        class="spell-btn bg-gray-700 hover:bg-yellow-500 hover:text-black border border-gray-500 text-white font-bold py-2 px-3 rounded text-sm">
                        ${spell}
                    </button>
                `).join('')}
            </div>
        </div>
    `;
}

/**
 * Updates the game log with new messages.
 */
function updateLog(messages) {
    messages.forEach(msg => {
        const p = document.createElement('p');
        p.textContent = `> ${msg}`;
        gameLog.appendChild(p);
    });
    // Auto-scroll to the bottom
    gameLog.scrollTop = gameLog.scrollHeight;
}

// --- INITIALIZATION ---
// Start the game when the page loads.
document.addEventListener('DOMContentLoaded', initGame);
