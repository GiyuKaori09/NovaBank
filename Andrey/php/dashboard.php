<?php

require_once 'db.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

try {

    $sqlUsuario = "
        SELECT *
        FROM USUARIO
        WHERE id_usuario = ?
    ";

    $stmtUsuario = $conn->prepare($sqlUsuario);

    $stmtUsuario->execute([$id_usuario]);

    $usuario =
        $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    $sqlCuentas = "
        SELECT *
        FROM CUENTA
        WHERE id_usuario = ?
    ";

    $stmtCuentas = $conn->prepare($sqlCuentas);

    $stmtCuentas->execute([$id_usuario]);

    $cuentas =
        $stmtCuentas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        NovaBank | Dashboard
    </title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../assets/css/home.css">

</head>

<body>

<div class="glow"></div>

<div class="container">

    <div class="home-card">

        <div class="logo">

            <h1>
                Nova<span>Bank</span>
            </h1>

        </div>

        <div class="welcome">

            <h2>
                Bienvenido
            </h2>

            <p>
                <?php
                echo htmlspecialchars(
                    $usuario['nombre_completo']
                );
                ?>
            </p>

        </div>

        <div class="divider"></div>

        <div class="accounts-container">

            <?php foreach ($cuentas as $cuenta): ?>

                <div class="bank-card">

                    <h3>

                        <?php
                        echo strtoupper(
                            $cuenta['tipo_cuenta']
                        );
                        ?>

                    </h3>

                    <p class="card-number">

                        <?php
                        echo $cuenta['numero_cuenta'];
                        ?>

                    </p>

                    <div class="card-balance">

                        $

                        <?php
                        echo number_format(
                            $cuenta['saldo'],
                            2
                        );
                        ?>

                    </div>

                    <p class="card-status">

                        Estado:

                        <?php
                        echo ucfirst(
                            $cuenta['estado']
                        );
                        ?>

                    </p>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="divider"></div>

        <div class="actions">

            <button
                onclick="window.location.href='transfer.php'">

                Transferir

            </button>

            <button
                onclick="window.location.href='movements.php'">

                Historial

            </button>

            <button
                onclick="window.location.href='profile.php'">

                Perfil

            </button>

            <?php if ($usuario['rol'] == 'admin'): ?>

                <button
                    onclick="window.location.href='admin.php'">

                    Panel Admin

                </button>

                <button
                    onclick="window.location.href='reportes.php'">

                    Reportes

                </button>

            <?php endif; ?>

            <button
                onclick="window.location.href='logout.php'">

                Salir

            </button>

        </div>

        <div class="footer">

            NovaBank © 2026

        </div>

    </div>

</div>

</body>

</html>