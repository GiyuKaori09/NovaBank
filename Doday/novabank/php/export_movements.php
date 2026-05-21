<?php
require_once 'db.php';
require_once 'security.php';
session_start();

if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit; }
$id_usuario = $_SESSION['id_usuario'];

try {
    // 1. Obtenemos IDs (la misma lógica de tu movements.php)
    $sqlCuentas = "SELECT id_cuenta FROM CUENTA WHERE id_usuario = ?";
    $stmtCuentas = $conn->prepare($sqlCuentas);
    $stmtCuentas->execute([$id_usuario]);
    $cuentas = $stmtCuentas->fetchAll(PDO::FETCH_ASSOC);
    $ids = array_column($cuentas, 'id_cuenta');

    if (empty($ids)) { die("No tienes cuentas para exportar"); }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // 2. Ejecutamos la misma consulta de movimientos de tu archivo original
    $sqlMovimientos = "
        SELECT M.*, CO.numero_cuenta AS cuenta_origen, CD.numero_cuenta AS cuenta_destino
        FROM MOVIMIENTO M
        LEFT JOIN CUENTA CO ON M.id_cuenta_origen = CO.id_cuenta
        LEFT JOIN CUENTA CD ON M.id_cuenta_destino = CD.id_cuenta
        WHERE M.id_cuenta_origen IN ($placeholders) OR M.id_cuenta_destino IN ($placeholders)
        ORDER BY M.fecha_movimiento DESC";

    $stmt = $conn->prepare($sqlMovimientos);
    $stmt->execute([...$ids, ...$ids]);
    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Generar el .txt
    $filename = "movements_" . date('Ymd_His') . ".txt";
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    echo "--- NOVABANK MOVEMENTS REPORT ---\n";
    echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($movimientos as $mov) {
        echo "Type: " . ucfirst($mov['tipo_movimiento']) . " | ";
        echo "Amount: " . $mov['monto'] . " | ";
        echo "From: " . ($mov['cuenta_origen'] ?? 'EXT') . " | ";
        echo "To: " . ($mov['cuenta_destino'] ?? 'EXT') . " | ";
        echo "Date: " . $mov['fecha_movimiento'] . "\n";
    }

    // 4. Auditoría (H23)
    Security::audit($conn, 'MOVIMIENTO', $id_usuario, 'EXPORT', 'user exported movements to txt');
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
