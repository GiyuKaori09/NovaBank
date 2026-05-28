<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : 0;
$estado_actual = isset($data['estado']) ? $data['estado'] : '';

if ($id_usuario > 0 && in_array($estado_actual, ['activo', 'inactivo'])) {
    
    $nuevo_estado = ($estado_actual === 'activo') ? 'inactivo' : 'activo';

    try {
        $sql = "UPDATE USUARIO SET estado = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nuevo_estado, $id_usuario]);

        echo json_encode([
            'success' => true, 
            'nuevo_estado' => $nuevo_estado
        ]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
}
?>