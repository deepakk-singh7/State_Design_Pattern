document.addEventListener('DOMContentLoaded', async () => {
    // Get references to the DOM elements we will be working with.
    const breedSelector = document.getElementById('breed-selector');
    const spawnArea = document.getElementById('spawn-area');

    /**
     * Initializes the spawner UI by fetching breed names and creating buttons.
     * This function is called once when the page loads.
     */
    async function initializeSpawner() {
        let breedNames; 

        try {
            const response = await fetch('api/get_breed_names.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            // If the fetch is successful, assign the result to our variable.
            breedNames = await response.json();

        } catch (error) {
            // --- Error Handling ---
            breedSelector.innerHTML = `<div class="text-red-400">Failed to load breeds: ${error.message}.</div>`;
            console.error('Initialization error:', error);
            return;
        }
        
        // Step 2: Update the UI  
        // Clear the initial "Loading..." message.
        breedSelector.innerHTML = ''; 

        // Create a button for each successfully fetched breed name.
        breedNames.forEach(name => {
            const button = document.createElement('button');
            button.className = 'bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105';
            button.textContent = `Spawn ${name}`;
            
            // Add a click event listener. When clicked, it will call the spawnMonster function with its name.
            button.addEventListener('click', () => spawnMonster(name));
            
            breedSelector.appendChild(button);
        });
    }

    /**
     * Fetches a complete monster object from the server and renders it.
     * @param {string} breedName - The name of the monster type to spawn.
     */
    async function spawnMonster(breedName) {
        try {
            //  Request a new monster instance from the server ---
            const response = await fetch(`api/spawn_monster.php?breed=${encodeURIComponent(breedName)}`);
            if (!response.ok) {
                throw new Error(`Server error: ${response.statusText}`);
            }
            // The response body is a JSON string representing a complete Monster object.
            const monster = await response.json();
            
            //  Render the monster on the page ---
            renderMonsterCard(monster);

        } catch (error) {
            console.error(`Could not spawn ${breedName}:`, error);
        }
    }

    /**
     * Creates and appends a monster card to the DOM.
     * @param {object} monster - The monster object received from the API.
     */
    function renderMonsterCard(monster) {
        const monsterCard = document.createElement('div');
        monsterCard.className = 'bg-gray-800 rounded-lg shadow-xl p-4 flex flex-col items-center text-center border-2 border-gray-700 animate-fade-in';
        
        // Populate the card's HTML. 
        monsterCard.innerHTML = `
            <img src="${monster.breed.image}" alt="${monster.breed.name}" class="w-24 h-24 rounded-full mb-3 border-4 border-gray-600 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/150x150/333/FFF?text=Error';">
            <h3 class="text-lg font-bold text-cyan-400">${monster.breed.name}</h3>
            <p class="text-sm text-gray-500 font-mono">ID: ${monster.id}</p>
            <div class="mt-3 text-left w-full">
                <p class="text-sm"><span class="font-semibold text-gray-400">Health:</span> ${monster.breed.health}</p>
                <p class="text-sm"><span class="font-semibold text-gray-400">Attack:</span> <span class="text-gray-300 italic">"${monster.breed.attack}"</span></p>
            </div>
        `;
        
        spawnArea.appendChild(monsterCard);
    }
    // Start the entire process when the page has finished loading.
    initializeSpawner();
});
