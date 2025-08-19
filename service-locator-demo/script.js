document.addEventListener('DOMContentLoaded', () => {

    // Get references to the form and the output element from the DOM.
    const form = document.getElementById('notificationForm');
    const outputElement = document.getElementById('output');

    form.addEventListener('submit', async (event) => {
        event.preventDefault(); // Prevent normal form submission

        // Provide immediate feedback to the user.
        outputElement.textContent = 'Sending...';

        // Create a FormData object from the form, which captures all input values.
        const formData = new FormData(form);

        try {
            // Send data to the PHP backend using fetch
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Get the JSON response from the backend
            const result = await response.json();

            // Display the output from the PHP script
            outputElement.textContent = result.output;

        } catch (error) {
            outputElement.textContent = 'Error: ' + error.message;
            console.error('Fetch error:', error);
        }
    });
});