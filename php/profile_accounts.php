<?php

require_once 'db.php';

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {

    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);

    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {

    $sql = "
        SELECT
            id_cuenta,
            numero_cuenta,
            tipo_cuenta,
            saldo,
            estado,
            fecha_creacion
        FROM CUENTA
        WHERE id_usuario = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$id_usuario]);

    $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'cuentas' => $cuentas
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>