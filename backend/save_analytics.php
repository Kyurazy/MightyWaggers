<?php
session_start();
require_once 'config.php';  // Include your database connection settings

// Get data from the AJAX request (sent from the client-side JavaScript)
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is received
if (isset($data['browser'], $data['os_version'], $data['processor'])) {
    // Get the user's IP address
    $ip_address = $_SERVER['REMOTE_ADDR'];  // Real user IP

    // Insert the analytics data into the database
    $stmt = $pdo->prepare('INSERT INTO analytics (user_id, ip_address, browser, os_version, processor) VALUES (:user_id, :ip_address, :browser, :os_version, :processor)');
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],  // Assuming you have a logged-in user
        'ip_address' => $ip_address,
        'browser' => $data['browser'],
        'os_version' => $data['os_version'],
        'processor' => $data['processor']
    ]);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>
