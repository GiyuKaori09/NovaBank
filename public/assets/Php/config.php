<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'novabank_db');
define('DB_USER', 'root');
define('DB_PASS', '');
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $conn = new PDO($dsn, $user, $pass);
     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     die("Error de conexión: " . $e->getMessage());
}
?>
