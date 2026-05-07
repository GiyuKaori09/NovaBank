<?php

require_once 'db.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

$id_usuario = $_SESSION['id_usuario'];

try {

    $sqlCuenta = "
        SELECT id_cuenta
        FROM CUENTA
        WHERE id_usuario = ?
        LIMIT 1
    ";

    $stmtCuenta = $conn->prepare($sqlCuenta);

    $stmtCuenta->execute([$id_usuario]);

    $cuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC);

    if (!$cuenta) {
        die("No tienes cuenta bancaria");
    }

    $id_cuenta = $cuenta['id_cuenta'];



    $sqlDepositos = "
        SELECT COUNT(*)
        FROM MOVIMIENTO
        WHERE id_cuenta = ?
        AND tipo_movimiento = 'deposito'
    ";

    $stmtDepositos = $conn->prepare($sqlDepositos);

    $stmtDepositos->execute([$id_cuenta]);

    $depositos = $stmtDepositos->fetchColumn();



    $sqlPagos = "
        SELECT COUNT(*)
        FROM MOVIMIENTO
        WHERE id_cuenta = ?
        AND (
            tipo_movimiento = 'pago'
            OR tipo_movimiento = 'transferencia'
        )
    ";

    $stmtPagos = $conn->prepare($sqlPagos);

    $stmtPagos->execute([$id_cuenta]);

    $pagos = $stmtPagos->fetchColumn();



    if ($depositos >= 5 && $pagos >= 3) {

        $sqlExiste = "
            SELECT COUNT(*)
            FROM TARJETA_CREDITO
            WHERE id_usuario = ?
        ";

        $stmtExiste = $conn->prepare($sqlExiste);

        $stmtExiste->execute([$id_usuario]);

        $yaTiene = $stmtExiste->fetchColumn();

        if ($yaTiene > 0) {
            die('Ya tienes una tarjeta de crédito');
        }



        $numeroTarjeta = '';

        for ($i = 0; $i < 16; $i++) {
            $numeroTarjeta .= rand(0,9);
        }



        $fechaExp = date('Y-m-d', strtotime('+5 years'));



        $cvv = rand(100,999);

        $cvvHash = password_hash($cvv, PASSWORD_BCRYPT);



        $sqlInsert = "
            INSERT INTO TARJETA_CREDITO (
                id_usuario,
                numero_tarjeta_credito,
                limite_credito,
                saldo_utilizado,
                dia_corte,
                tasa_interes,
                fecha_expiracion,
                cvv_hash,
                estado
            )
            VALUES (
                ?,
                ?,
                10000.00,
                0.00,
                15,
                25.50,
                ?,
                ?,
                'activa'
            )
        ";

        $stmtInsert = $conn->prepare($sqlInsert);

        $stmtInsert->execute([
            $id_usuario,
            $numeroTarjeta,
            $fechaExp,
            $cvvHash
        ]);



        echo "
        Tarjeta de crédito aprobada correctamente.
        ";

    } else {

        echo "
        No cumples con los requisitos.

        Necesitas:
        - 5 depósitos
        - 3 pagos o transferencias
        ";
    }

} catch (PDOException $e) {

    echo 'Error: ' . $e->getMessage();
}
?>