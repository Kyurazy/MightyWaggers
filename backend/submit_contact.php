<?php
session_start();
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare('INSERT INTO contact (name, email, message) VALUES (:name, :email, :message)');
    if ($stmt->execute([
        'name' => $name,
        'email' => $email,
        'message' => $message
    ])) {
        $_SESSION['success_message'] = 'Your message has been sent successfully!';
    } else {
        $_SESSION['error_message'] = 'There was an error sending your message. Please try again later.';
    }

    header('Location: contact.php');
    exit();
}
