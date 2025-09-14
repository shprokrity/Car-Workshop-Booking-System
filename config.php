<?php
// Database configuration
$host = 'localhost';
$dbname = 'workshop_booking';
$username = 'root';  
$password = '';      

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session hobe if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// login check
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// admin check
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

// send email (simple mail function)
function sendEmail($to, $subject, $message) {
    
    try {
        $headers = "From: noreply@workshop.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $result = @mail($to, $subject, $message, $headers);
        
        return true;
        
    } catch (Exception $e) {
        // If there's an error, just return true to continue
        return true;
    }
}
?>