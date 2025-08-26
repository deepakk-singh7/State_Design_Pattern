document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('demoCanvas');
    const ctx = canvas.getContext('2d');

    const addParticleBtn = document.getElementById('addParticleBtn');
    const addSmokeBtn = document.getElementById('addSmokeBtn');
    const particleStatusDiv = document.getElementById('particleStatus');
    const smokeStatusDiv = document.getElementById('smokeStatus');

    let activeObjects = [];

    // --- API Communication ---
    const API_URL = 'php/api.php';

    async function apiCreateObjects(type, count) {
        const x = canvas.width / 2;
        const y = canvas.height / 2;
        const response = await fetch(`${API_URL}?action=create&type=${type}&count=${count}&x=${x}&y=${y}`);
        const data = await response.json();
        if (data.success && data.objects) {
            // Add objects returned from backend to our local animation list
            activeObjects.push(...data.objects);
        }
    }

    async function apiReturnObjects(type, ids) {
        if (ids.length === 0) return;
        await fetch(`${API_URL}?action=return`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, ids })
        });
    }

    async function updateStatus() {
        try {
            const response = await fetch(`${API_URL}?action=status`);
            const data = await response.json();
            if (data.success) {
                const pStatus = data.statuses.particle;
                particleStatusDiv.textContent = `Sparkle Pool: ${pStatus.active} / ${pStatus.total} Active`;
                
                const sStatus = data.statuses.smoke;
                smokeStatusDiv.textContent = `Smoke Pool: ${sStatus.active} / ${sStatus.total} Active`;
            }
        } catch (error) {
            console.error("Failed to fetch status:", error);
        }
    }


    // --- Event Listeners ---
    addParticleBtn.addEventListener('click', () => apiCreateObjects('particle', 10));
    addSmokeBtn.addEventListener('click', () => apiCreateObjects('smoke', 5));


    // --- Animation Loop ---
    function animate() {
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const expiredByType = {};

        // Update and draw active objects
        for (let i = activeObjects.length - 1; i >= 0; i--) {
            const obj = activeObjects[i];

            // Update state
            obj.lifetime--;
            if (obj.lifetime == 0) {
                // Collect expired object ID for return
                if (!expiredByType[obj.type]) {
                    expiredByType[obj.type] = [];
                }
                expiredByType[obj.type].push(obj.id);
                // Remove from local array
                activeObjects.splice(i, 1);
                continue;
            }

            // Move object
            obj.x += obj.xVel;
            obj.y += obj.yVel;

            // Draw object
            ctx.fillStyle = obj.color;
            ctx.beginPath();
            ctx.arc(obj.x, obj.y, obj.size, 0, Math.PI * 2);
            ctx.fill();
        }

        // Notify backend about expired objects in batches
        for (const type in expiredByType) {
            apiReturnObjects(type, expiredByType[type]);
        }

        requestAnimationFrame(animate);
    }

    // --- Initialization ---
    updateStatus(); // Initial status fetch
    setInterval(updateStatus, 2000); // Periodically update status
    animate(); // Start the animation loop
});