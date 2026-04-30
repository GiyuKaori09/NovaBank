<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM USUARIO WHERE correo = ? AND estado = 'activo'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$correo]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['contrasena_hash'])) {
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['nombre'] = $user['nombre_completo'];

        echo "Login exitoso";
    } else {
        echo "Credenciales incorrectas";
    }
}
?>