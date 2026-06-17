<?php

require_once 'db.php';

session_start();

// Simular usuario autenticado (reemplazar con sesión real)
$id_usuario = $_SESSION['id_usuario'] ?? 1;

$error   = null;
$success = null;

// Obtener saldo actual del usuario
try {
    $stmtSaldo = $conn->prepare("
        SELECT numero_cuenta, saldo
        FROM CUENTA
        WHERE id_usuario = ? AND tipo_cuenta = 'debito' AND estado = 'activa'
        LIMIT 1
    ");
    $stmtSaldo->execute([$id_usuario]);
    $cuenta = $stmtSaldo->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener cuenta: " . $e->getMessage();
}

// Procesar retiro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cuenta) {

    $monto       = floatval($_POST['monto'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');

    // Validaciones
    if ($monto <= 0) {
        $error = "El monto debe ser mayor a cero.";

    } elseif ($monto < 50) {
        $error = "El monto mínimo de retiro es $50.00.";

    } elseif ($monto > 10000) {
        $error = "El monto máximo de retiro por operación es $10,000.00.";

    } elseif ($monto > $cuenta['saldo']) {
        $error = "Saldo insuficiente. Tu saldo disponible es $" . number_format($cuenta['saldo'], 2) . ".";

    } else {

        try {
            $conn->beginTransaction();

            // Descontar saldo
            $stmtUpdate = $conn->prepare("
                UPDATE CUENTA
                SET saldo = saldo - ?
                WHERE id_usuario = ? AND tipo_cuenta = 'debito' AND estado = 'activa'
            ");
            $stmtUpdate->execute([$monto, $id_usuario]);

            // Registrar movimiento
            $stmtMov = $conn->prepare("
                INSERT INTO MOVIMIENTO (id_cuenta, tipo, monto, descripcion, fecha)
                VALUES (
                    (SELECT id_cuenta FROM CUENTA WHERE id_usuario = ? AND tipo_cuenta = 'debito' LIMIT 1),
                    'retiro',
                    ?,
                    ?,
                    NOW()
                )
            ");
            $stmtMov->execute([$id_usuario, $monto, $descripcion ?: 'Retiro en cajero']);

            $conn->commit();

            // Actualizar saldo en pantalla
            $cuenta['saldo'] -= $monto;
            $success = "Retiro de $" . number_format($monto, 2) . " realizado correctamente.";

        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Error al procesar el retiro: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaBank | Retiro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/retiro.css">
</head>
<body>

    <div class="glow"></div>

    <div class="container">
        <div class="login-card">

            <!-- Logo -->
            <div class="logo">
                <h1>Nova<span>Bank</span></h1>
            </div>

            <div class="subtitle">Retiro de efectivo</div>

            <!-- Saldo disponible -->
            <?php if ($cuenta): ?>
            <div class="saldo-card">
                <p class="saldo-label">Saldo disponible</p>
                <p class="card-balance">$<?php echo number_format($cuenta['saldo'], 2); ?></p>
                <p class="saldo-cuenta">Cuenta: **** <?php echo substr($cuenta['numero_cuenta'], -4); ?></p>
            </div>
            <?php endif; ?>

            <!-- Mensaje de error -->
            <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Mensaje de éxito -->
            <?php if ($success): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <!-- Formulario de retiro -->
            <?php if ($cuenta && !$success): ?>
            <form method="POST">

                <div class="input-group">
                    <label>Monto a retirar</label>
                    <div class="input-prefix-wrapper">
                        <span class="prefix">$</span>
                        <input
                            type="number"
                            name="monto"
                            placeholder="0.00"
                            min="50"
                            max="10000"
                            step="0.01"
                            required
                        >
                    </div>
                </div>

                <div class="input-group">
                    <label>Descripción (opcional)</label>
                    <input
                        type="text"
                        name="descripcion"
                        placeholder="Ej. Retiro para gastos personales"
                        maxlength="100"
                    >
                </div>

                <!-- Requisitos -->
                <div class="requisitos">
                    <p class="req-titulo">Requisitos del retiro</p>
                    <ul>
                        <li>Monto mínimo: <span>$50.00</span></li>
                        <li>Monto máximo por operación: <span>$10,000.00</span></li>
                        <li>Debes contar con saldo suficiente</li>
                    </ul>
                </div>

                <button class="btn" type="submit">Retirar</button>

            </form>
            <?php endif; ?>

            <?php if ($success): ?>
            <a class="btn" href="dashboard.php">Volver al inicio</a>
            <?php endif; ?>

            <!-- Footer -->
            <div class="footer">
                <a href="dashboard.php">← Volver al inicio</a>
            </div>

        </div>
    </div>

</body>
</html>
