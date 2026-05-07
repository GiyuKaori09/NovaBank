<?php
// paso1_procesar.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardamos temporalmente en la sesión
    $_SESSION['reg_correo'] = $_POST['correo'];
    $_SESSION['reg_password'] = $_POST['password'];
    $_SESSION['reg_curp'] = $_POST['curp'];
    $_SESSION['reg_telefono'] = $_POST['telefono'];

    // Saltamos al Paso 2 de la interfaz
    header("Location: datos_personales.html");
    exit();
}
?>
