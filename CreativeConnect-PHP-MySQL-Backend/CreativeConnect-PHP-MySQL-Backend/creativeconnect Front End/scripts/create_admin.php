<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('This script can only be run from the command line.');
}

require_once __DIR__ . '/../config/database.php';

[$script, $name, $email, $password] = array_pad($argv, 4, null);
$name = trim((string) $name);
$email = strtolower(trim((string) $email));
$password = (string) $password;

if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    fwrite(STDERR, "Usage: php scripts/create_admin.php \"Admin Name\" admin@example.com \"StrongPassword\"\n");
    exit(1);
}

$statement = db()->prepare(
    'INSERT INTO users (full_name, email, company, password_hash, role)
     VALUES (:full_name, :email, NULL, :password_hash, :role)
     ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), password_hash = VALUES(password_hash), role = VALUES(role)'
);
$statement->execute([
    'full_name' => $name,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'role' => 'admin',
]);

fwrite(STDOUT, "Administrator account is ready for {$email}.\n");
