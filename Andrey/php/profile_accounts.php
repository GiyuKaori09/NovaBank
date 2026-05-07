<?php

require_once 'db.php';

session_start();

$_SESSION['id_usuario'] = 1;

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {

    echo json_encode([
        'error' => 'No autorizado'
    ]);

    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {

    $sql = "
        SELECT *
        FROM CUENTA
        WHERE id_usuario = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$id_usuario]);

    $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cuentas);

} catch (PDOException $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>