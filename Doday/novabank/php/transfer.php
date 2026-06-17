<?php

require_once 'db.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {


    $sqlCuenta = "
        SELECT *
        FROM CUENTA
        WHERE
            id_usuario = ?
            AND tipo_cuenta = 'debito'
        LIMIT 1
    ";

    $stmtCuenta = $conn->prepare($sqlCuenta);

    $stmtCuenta->execute([$id_usuario]);

    $cuentaOrigen =
        $stmtCuenta->fetch(PDO::FETCH_ASSOC);

    if (!$cuentaOrigen) {

        die("No tienes cuenta débito");
    }

} catch (PDOException $e) {

    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero_destino =
        trim($_POST['numero_destino']);

    $monto =
        floatval($_POST['monto']);

    try {

        $conn->beginTransaction();

        $sqlDestino = "
            SELECT *
            FROM CUENTA
            WHERE numero_cuenta = ?
            LIMIT 1
        ";

        $stmtDestino =
            $conn->prepare($sqlDestino);

        $stmtDestino->execute([
            $numero_destino
        ]);

        $cuentaDestino =
            $stmtDestino->fetch(PDO::FETCH_ASSOC);

        if (!$cuentaDestino) {

            throw new Exception(
                "Cuenta destino no encontrada"
            );
        }

        if ($monto <= 0) {

            throw new Exception(
                "Monto inválido"
            );
        }

        if (
            $cuentaOrigen['saldo']
            < $monto
        ) {

            throw new Exception(
                "Saldo insuficiente"
            );
        }

        if (
            $cuentaOrigen['numero_cuenta']
            ==
            $cuentaDestino['numero_cuenta']
        ) {

            throw new Exception(
                "No puedes transferirte a ti mismo"
            );
        }

        $nuevoSaldoOrigen =
            $cuentaOrigen['saldo']
            - $monto;

        $sqlUpdateOrigen = "
            UPDATE CUENTA
            SET saldo = ?
            WHERE id_cuenta = ?
        ";

        $stmtUpdateOrigen =
            $conn->prepare($sqlUpdateOrigen);

        $stmtUpdateOrigen->execute([
            $nuevoSaldoOrigen,
            $cuentaOrigen['id_cuenta']
        ]);

        $nuevoSaldoDestino =
            $cuentaDestino['saldo']
            + $monto;

        $sqlUpdateDestino = "
            UPDATE CUENTA
            SET saldo = ?
            WHERE id_cuenta = ?
        ";

        $stmtUpdateDestino =
            $conn->prepare($sqlUpdateDestino);

        $stmtUpdateDestino->execute([
            $nuevoSaldoDestino,
            $cuentaDestino['id_cuenta']
        ]);

        $sqlMovimiento = "
            INSERT INTO MOVIMIENTO (

                id_cuenta_origen,
                id_cuenta_destino,
                tipo_movimiento,
                monto,
                descripcion

            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmtMovimiento =
            $conn->prepare($sqlMovimiento);

        $stmtMovimiento->execute([

            $cuentaOrigen['id_cuenta'],
            $cuentaDestino['id_cuenta'],
            'transferencia',
            $monto,
            'Transferencia bancaria'

        ]);

        $conn->commit();

        header(
            "Location: transfer.php?ok=1"
        );

        exit;

    } catch (Exception $e) {

        $conn->rollBack();

        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        NovaBank | Transferencias
    </title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../assets/css/styles.css">

</head>

<body>

    <div class="glow"></div>

    <div class="container">

        <div class="login-card">

            <div class="logo">

                <h1>
                    Nova<span>Bank</span>
                </h1>

            </div>

            <div class="subtitle">

                Transferencia bancaria segura

            </div>

            <div class="account-card">

                <p>

                    <span>
                        Cuenta origen:
                    </span>

                    <?php
                    echo $cuentaOrigen['numero_cuenta'];
                    ?>

                </p>

                <p>

                    <span>
                        Saldo disponible:
                    </span>

                    $

                    <?php
                    echo number_format(
                        $cuentaOrigen['saldo'],
                        2
                    );
                    ?>

                </p>

            </div>

            <?php if (isset($_GET['ok'])): ?>

                <div class="message">

                    Transferencia realizada correctamente

                </div>

            <?php endif; ?>

            <?php if (isset($error)): ?>

                <div class="message">

                    <?php echo $error; ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="input-group">

                    <label>
                        Cuenta destino
                    </label>

                    <input
                        type="text"
                        name="numero_destino"
                        required
                    >

                </div>

                <div class="input-group">

                    <label>
                        Monto
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="monto"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn">

                    Transferir

                </button>

            </form>

            <br>

            <button
                class="btn"
                onclick="window.location.href='dashboard.php'">

                Volver al Dashboard

            </button>

        </div>

    </div>

</body>

</html>