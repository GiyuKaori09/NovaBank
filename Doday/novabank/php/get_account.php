<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT numero_cuenta, tipo_cuenta, saldo, estado, fecha_apertura 
        FROM CUENTA 
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$id_usuario]);

$cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cuentas);
?>