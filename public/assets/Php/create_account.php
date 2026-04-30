<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo_cuenta'];

    if (!in_array($tipo, ['ahorro', 'cheques'])) {
        die("Tipo de cuenta inválido");
    }

    $id_usuario = $_SESSION['id_usuario'];

    $numero = rand(1000000000, 9999999999);

    try {
        $sql = "INSERT INTO CUENTA 
        (id_usuario, numero_cuenta, tipo_cuenta, saldo, estado) 
        VALUES (?, ?, ?, 0.00, 'activa')";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_usuario, $numero, $tipo]);

        echo "Cuenta creada correctamente";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>