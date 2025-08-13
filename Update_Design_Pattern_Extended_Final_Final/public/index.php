<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Pattern Demo</title>
    <link rel="stylesheet" href="css/style.css">
</head><body>
    <h1>Update Method Design Pattern</h1>
    
    <div id="controls">
        <button id="startStopBtn">Start</button>
        <button id="resetBtn">Reset</button>
    </div>

    <div id="game-container">
        <div id="world">
            </div>
        <!-- <div id="stats">
            <p>Frame: <span id="frame-counter">0</span></p>
            <p>Entities: <span id="entity-counter">0</span></p>
        </div> -->
                <div class="stat">
            <p>Server Updates:</p>
            <span id="frame-counter">0</span>
        </div>
        <div class="stat">
            <p>Total Entities:</p>
            <span id="entity-counter">0</span>
        </div>
        <div class="stat">
            <p>Client FPS:</p>
            <span id="client-fps-counter">0</span>
        </div>
    </div>
    <?php require_once 'api/config.php'; ?>
    <script>
        // Create a global JavaScript config object from our PHP constants
        const AppConfig = {
            UPDATE_INTERVAL_MS: <?php echo (1.0 / SERVER_TICK_RATE_HZ) * 1000; ?>,
            CLIENT_TARGET_FPS: <?php echo CLIENT_TARGET_FPS; ?>
        };
    </script>
    <script type="module" src="js/main.js"></script>
</body>
</html>