<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $curp = $_POST['curp'];
    $fecha = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    if (strlen($curp) !== 18) {
        die("CURP inválida");
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Correo inválido");
    }

    if (strlen($password) < 8) {
        die("La contraseña debe tener mínimo 8 caracteres");
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO USUARIO 
        (nombre_completo, curp, fecha_nacimiento, direccion, correo, contrasena_hash) 
        VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $curp, $fecha, $direccion, $correo, $hash]);

        echo "Usuario registrado correctamente";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>