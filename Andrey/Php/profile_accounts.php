<?php
require_once 'db.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

$id_usuario = $_SESSION['id_usuario'];

try {

    $sql = "SELECT
                id_cuenta,
                numero_cuenta,
                tipo_cuenta,
                saldo,
                limite_credito,
                estado,
                fecha_apertura
            FROM CUENTA
            WHERE id_usuario = ?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$id_usuario]);

    $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cuentas);

} catch (PDOException $e) {

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
?>