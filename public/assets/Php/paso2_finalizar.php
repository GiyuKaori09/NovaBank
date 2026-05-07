<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = $_SESSION['temp_reg'];
    $pass_hash = password_hash($t['password'], PASSWORD_DEFAULT);
    
    try {
        $sql = "INSERT INTO USUARIO (nombre_completo, curp, fecha_nacimiento, direccion, correo, contrasena_hash) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_POST['nombre_completo'], $t['curp'], $_POST['fecha_nacimiento'], $_POST['direccion'], $t['correo'], $pass_hash]);
        
        session_destroy();
        header("Location: cuenta.html");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
