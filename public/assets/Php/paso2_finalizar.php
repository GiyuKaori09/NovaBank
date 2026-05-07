<?php
// paso2_finalizar.php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos del Paso 1 (desde la sesión)
    $correo = $_SESSION['reg_correo'];
    $pass_hash = password_hash($_SESSION['reg_password'], PASSWORD_DEFAULT);
    $curp = $_SESSION['reg_curp'];
    
    // Datos del Paso 2 (desde el formulario actual)
    $nombre = $_POST['nombre_completo'];
    $fecha_nac = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];

    try {
        $sql = "INSERT INTO USUARIO (nombre_completo, curp, fecha_nacimiento, direccion, correo, contrasena_hash) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $curp, $fecha_nac, $direccion, $correo, $pass_hash]);

        // Limpiamos la sesión y vamos al dashboard
        session_destroy();
        header("Location: cuenta.html");
        exit();
    } catch (PDOException $e) {
        echo "Error al crear la cuenta: " . $e->getMessage();
    }
}
?>
