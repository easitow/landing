<?php
session_start();

// Rate limiting - max 10 submissions per IP per hour
$ip = $_SERVER['REMOTE_ADDR'];
$rate_file = sys_get_temp_dir() . '/easitow_rate_' . md5($ip);
$submissions = file_exists($rate_file) ? json_decode(file_get_contents($rate_file), true) : [];
$submissions = array_filter($submissions, fn($time) => $time > time() - 3600);

if (count($submissions) >= 10) {
    header("Location: /?error=rate_limit");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: /?error=invalid");
        exit();
    }
    
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    if ($email === false || empty($email)) {
        header("Location: /?error=invalid");
        exit();
    }
    
    // Basic honeypot check
    if (!empty($_POST['website'])) {
        header("Location: /?success=1");
        exit();
    }
    
    // Log to file
    $log_file = __DIR__ . '/demo_requests.txt';
    $log_entry = date('Y-m-d H:i:s') . " | " . $ip . " | " . $email . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    
    // Update rate limit
    $submissions[] = time();
    file_put_contents($rate_file, json_encode($submissions), LOCK_EX);
    
    header("Location: /?success=1");
    exit();
}

// Invalid request method
header("Location: /");
exit();
?>