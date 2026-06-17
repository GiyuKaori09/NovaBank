<?php

session_start();
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");
    exit();
}

$sql = "
    SELECT *
    FROM REPORTE
    ORDER BY fecha DESC
";

$resultado = mysqli_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        NovaBank | Reportes
    </title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../assets/css/home.css">

</head>

<body>

<div class="container">

    <div class="admin-card">

        <div class="logo">

            <h1>
                Nova<span>Bank</span>
            </h1>

        </div>

        <div class="welcome">

            <h2>
                Reportes de Actividad
            </h2>

            <p>
                Historial de acciones del sistema
            </p>

        </div>

        <div class="divider"></div>

        <div class="table-wrapper">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Usuario</th>

                        <th>Acción</th>

                        <th>Fecha</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

                    <tr>

                        <td>

                            <?php
                            echo $fila['id_reporte'];
                            ?>

                        </td>

                        <td>

                            <?php
                            echo $fila['usuario'];
                            ?>

                        </td>

                        <td>

                            <?php
                            echo $fila['accion'];
                            ?>

                        </td>

                        <td>

                            <?php
                            echo $fila['fecha'];
                            ?>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

        <div class="divider"></div>

        <div class="actions">

            <button
                onclick="window.location.href='dashboard.php'">

                Dashboard

            </button>

            <button
                onclick="window.location.href='admin.php'">

                Panel Admin

            </button>

        </div>

        <div class="footer">

            NovaBank © 2026

        </div>

    </div>

</div>

</body>

</html>