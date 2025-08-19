<?php

// Set the content type to JSON for the response.
header('Content-Type: application/json');

// --- Autoload all our service classes ---
spl_autoload_register(function ($class_name) {
    $file = 'services/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
// include the enum definition.
require_once 'enums/ServiceType.php';

// --- get data from the frontend ---
$message = $_POST['message'] ?? 'Default Message';
// Get the service type string from the POST request.
$serviceTypeString = $_POST['service'] ?? 'disable';
// Enable logging if the 'logging' checkbox was checked.
$enableLogging = isset($_POST['logging']) && $_POST['logging'] === 'true';

// --- The Core Logic ---

// Initialize the locator, which defaults to NullNotifier.
Locator::initialize();

// Convert the input string to a ServiceType enum case.
// ServiceType::tryFrom() returns the enum case on match or null on failure.
$serviceType = ServiceType::tryFrom($serviceTypeString) ?? ServiceType::DISABLE;

// Create the appropriate service provider based on the enum case.
$provider = null;
switch ($serviceType) {
    case ServiceType::EMAIL:
        $provider = new EmailNotifier();
        break;
    case ServiceType::SMS:
        $provider = new SmsNotifier();
        break;
    default: // This handles ServiceType::DISABLE or any other case.
        $provider = new NullNotifier();
        break;
}


//  (Decorator Pattern) If logging is enabled, wrap the chosen provider.
if ($enableLogging) {
    $provider = new LoggableNotifier($provider);
}

// 4. Provide the final, configured service to the Locator.
Locator::provide($provider);

// 5. Get the service and use it, without knowing its concrete type.
$notifier = Locator::getNotifier();
$output = $notifier->send($message);


// 6. Send the result back to the frontend as JSON.
echo json_encode(['output' => $output]);