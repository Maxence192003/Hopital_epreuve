<?php
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo "Hash: " . $hash . "\n";
echo "Length: " . strlen($hash) . "\n";
?>
