document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('simulationCanvas');
    const ctx = canvas.getContext('2d');

    const startStopBtn = document.getElementById('startStopBtn');
    const inefficientBtn = document.getElementById('inefficientBtn');
    const efficientBtn = document.getElementById('efficientBtn');
    const modeDisplay = document.getElementById('modeDisplay');
    const timeDisplay = document.getElementById('timeDisplay');

    let isRunning = false;
    let currentMode = 'inefficient';
    let dots = [];

    // --- Event Listeners ---
    startStopBtn.addEventListener('click', () => {
        isRunning = !isRunning;
        startStopBtn.textContent = isRunning ? 'Stop' : 'Start';
        if (isRunning) {
            gameLoop();
        }
    });

    inefficientBtn.addEventListener('click', () => switchMode('inefficient'));
    efficientBtn.addEventListener('click', () => switchMode('efficient'));

    function switchMode(newMode) {
        currentMode = newMode;
        modeDisplay.textContent = newMode.charAt(0).toUpperCase() + newMode.slice(1);
        inefficientBtn.classList.toggle('active', newMode === 'inefficient');
        efficientBtn.classList.toggle('active', newMode === 'efficient');
    }

    // --- Drawing Logic ---
    function drawDots() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#03dac6';
        for (const dot of dots) {
            ctx.beginPath();
            ctx.arc(dot.x, dot.y, 2, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    // --- Main Game Loop ---
    async function gameLoop() {
        if (!isRunning) return;

        try {
            const response = await fetch(`api.php?mode=${currentMode}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            dots = data.positions;
            timeDisplay.textContent = data.serverTime.toFixed(2);

            drawDots();

        } catch (error) {
            console.error("Error fetching data:", error);
            isRunning = false; // Stop the loop on error
            startStopBtn.textContent = 'Start';
        }

        requestAnimationFrame(gameLoop);
    }
});