async function activatePower(power) {
  const logDiv = document.getElementById('log');
  logDiv.innerHTML = "<strong>Logs:</strong><br>Activating power...";
  let logsToDisplay = [];

  try {
    const response = await fetch(`../backend/index.php?power=${power}`);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    // Expression check on data
    if (!Array.isArray(data)) {
      throw new Error("Invalid data format from server. Expected an array.");
    }
    
    logsToDisplay = data;

  } catch (err) {
    console.error('Error activating power:', err);
    logsToDisplay = [`<strong>Error:</strong> ${err.message}`];

  } finally {
    logDiv.innerHTML = "<strong>Logs:</strong><br>" + logsToDisplay.join("<br>");
  }
}