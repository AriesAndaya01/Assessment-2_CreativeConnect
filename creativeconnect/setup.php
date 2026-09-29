<?php
// Run once (safe to re-run): http://localhost/creativeconnect/setup.php
require_once __DIR__ . '/config.php';
header('Content-Type: text/plain; charset=utf-8');
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4');
    $pdo->exec('USE `' . DB_NAME . '`');
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, role ENUM("admin","staff","client") NOT NULL DEFAULT "client",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('ALTER TABLE users MODIFY role ENUM("admin","staff","client") NOT NULL DEFAULT "client"');
    $pdo->exec('CREATE TABLE IF NOT EXISTS requests (id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL, company VARCHAR(120) NOT NULL, service VARCHAR(40) NOT NULL, deadline DATE NOT NULL,
        budget INT NOT NULL, message TEXT NOT NULL, status ENUM("New","In Progress","In Review","Delivered") NOT NULL DEFAULT "New",
        assigned_to INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(email))');
    $has = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . DB_NAME . "' AND TABLE_NAME='requests' AND COLUMN_NAME='assigned_to'")->fetchColumn();
    if (!$has) $pdo->exec('ALTER TABLE requests ADD COLUMN assigned_to INT NULL');
    $pdo->exec('CREATE TABLE IF NOT EXISTS comments (id INT AUTO_INCREMENT PRIMARY KEY, request_id INT NOT NULL, user_id INT NOT NULL,
        body TEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(request_id))');
    $pdo->exec('CREATE TABLE IF NOT EXISTS activity_log (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, action VARCHAR(60) NOT NULL,
        detail VARCHAR(255) NOT NULL DEFAULT "", created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');

    $ins = $pdo->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), password_hash=VALUES(password_hash), role=VALUES(role)');
    $seed = [
        ['Aries Joshua Andaya', 'admin@creativeconnect.test', 'Admin123!', 'admin'],
        ['Phommaxay Phimmavong', 'staff1@creativeconnect.test', 'Staff123!', 'staff'],
        ['Srijana Bhandari', 'staff2@creativeconnect.test', 'Staff123!', 'staff'],
        ['Ishika', 'staff3@creativeconnect.test', 'Staff123!', 'staff'],
        ['Demo Client', 'client@creativeconnect.test', 'Client123!', 'client'],
    ];
    foreach ($seed as [$n, $em, $pw, $r]) $ins->execute([$n, $em, password_hash($pw, PASSWORD_DEFAULT), $r]);

    if ((int)$pdo->query('SELECT COUNT(*) FROM requests')->fetchColumn() === 0) {
        $uid = fn($e) => (int)$pdo->query("SELECT id FROM users WHERE email='$e'")->fetchColumn();
        $d = fn($n) => date('Y-m-d', strtotime("+$n days"));
        $rq = $pdo->prepare('INSERT INTO requests (full_name,email,company,service,deadline,budget,message,status,assigned_to) VALUES (?,?,?,?,?,?,?,?,?)');
        $rq->execute(['Demo Client', 'client@creativeconnect.test', 'Demo Co', 'video-editing', $d(14), 900, 'Edit a 3 minute product launch video from raw footage.', 'In Progress', $uid('staff3@creativeconnect.test')]);
        $rq->execute(['Demo Client', 'client@creativeconnect.test', 'Demo Co', 'web-development', $d(30), 3000, 'Build a five page marketing website with a contact form.', 'New', null]);
        $rq->execute(['Maria Santos', 'maria@example.com', 'Santos Bakery', 'graphic-design', $d(10), 450, 'Design a new logo and social media banner set.', 'In Review', $uid('staff1@creativeconnect.test')]);
        $rq->execute(['Tom Nguyen', 'tom@example.com', 'Nguyen Fitness', 'content-creation', $d(21), 600, 'Write and design four Instagram posts per week for a month.', 'Delivered', $uid('staff2@creativeconnect.test')]);
        $pdo->prepare('INSERT INTO comments (request_id,user_id,body) VALUES (1,?,?)')->execute([$uid('staff3@creativeconnect.test'), 'First cut is underway. We will share a draft link in two days.']);
    }
    echo "OK - database '" . DB_NAME . "' is ready (5 accounts + sample data).\n\nRBAC accounts:\n";
    foreach ($seed as [$n, $em, $pw, $r]) echo str_pad($r, 7) . " $em / $pw\n";
    echo "\nNext: open login.php";
} catch (Throwable $e) {
    http_response_code(500);
    echo "SETUP FAILED: " . $e->getMessage() . "\n\nIs MySQL started? Check config.php.";
}
