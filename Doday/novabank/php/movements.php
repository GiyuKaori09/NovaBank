<?php

require_once 'db.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    $sqlCuentas = "
        SELECT *
        FROM CUENTA
        WHERE id_usuario = ?
    ";

    $stmtCuentas = $conn->prepare($sqlCuentas);
    $stmtCuentas->execute([$id_usuario]);
    $cuentas = $stmtCuentas->fetchAll(PDO::FETCH_ASSOC);

    if (!$cuentas) {
        die("No tienes cuentas bancarias");
    }

    $ids = [];
    foreach ($cuentas as $cuenta) {
        $ids[] = $cuenta['id_cuenta'];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sqlMovimientos = "
        SELECT
            M.*,
            CO.numero_cuenta AS cuenta_origen,
            CD.numero_cuenta AS cuenta_destino
        FROM MOVIMIENTO M
        LEFT JOIN CUENTA CO ON M.id_cuenta_origen = CO.id_cuenta
        LEFT JOIN CUENTA CD ON M.id_cuenta_destino = CD.id_cuenta
        WHERE M.id_cuenta_origen IN ($placeholders)
           OR M.id_cuenta_destino IN ($placeholders)
        ORDER BY M.fecha_movimiento DESC
    ";

    $stmtMovimientos = $conn->prepare($sqlMovimientos);
    $stmtMovimientos->execute([...$ids, ...$ids]);
    $movimientos = $stmtMovimientos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>NovaBank | Movimientos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="glow"></div>
    <div class="profile-container">
        <div class="profile-card">
            <div class="logo">
                <h1>Nova<span>Bank</span></h1>
            </div>
            <p class="subtitle">Historial completo de movimientos</p>

            <div style="margin-bottom: 20px;">
                <a href="export_movements.php" class="btn" style="text-decoration:none; display:block; text-align:center;">
                    Exportar movimientos (.txt)
                </a>
            </div>

            <?php if (count($movimientos) > 0): ?>
                <?php foreach ($movimientos as $mov): ?>
                    <?php
                    $esSalida = in_array($mov['id_cuenta_origen'], $ids);
                    ?>
                    <div class="account-card">
                        <p>
                            <span>Movimiento:</span>
                            <?php if ($esSalida): ?>
                                Transferencia enviada
                            <?php else: ?>
                                Transferencia recibida
                            <?php endif; ?>
                        </p>
                        <p>
                            <span>Tipo:</span>
                            <?php echo ucfirst($mov['tipo_movimiento']); ?>
                        </p>
                        <p>
                            <span>Cuenta origen:</span>
                            <?php echo $mov['cuenta_origen'] ?? 'N/A'; ?>
                        </p>
                        <p>
                            <span>Cuenta destino:</span>
                            <?php echo $mov['cuenta_destino'] ?? 'N/A'; ?>
                        </p>
                        <p>
                            <span>Monto:</span>
                            <?php if ($esSalida): ?>
                                <span style="color:#ff7b7b;">
                                    - $ <?php echo number_format($mov['monto'], 2); ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#7dffb3;">
                                    + $ <?php echo number_format($mov['monto'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <span>Descripción:</span>
                            <?php echo $mov['descripcion']; ?>
                        </p>
                        <p>
                            <span>Fecha:</span>
                            <?php echo date('d/m/Y h:i A', strtotime($mov['fecha_movimiento'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="account-card">
                    No hay movimientos registrados.
                </div>
            <?php endif; ?>

            <button class="btn" onclick="window.location.href='dashboard.php'">
                Volver al Dashboard
            </button>
        </div>
    </div>
</body>
</html>
</body>

