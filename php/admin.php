<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM USUARIO";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        NovaBank | Panel Admin
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
                Panel de Administración
            </h2>

            <p>
                Gestión de usuarios del sistema
            </p>

        </div>

        <div class="divider"></div>

        <div class="table-wrapper">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Nombre</th>

                        <th>Correo</th>

                        <th>Saldo</th>

                        <th>Estado</th>

                        <th>Rol</th>

                        <th>Acción</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

                    <tr>

                        <td>
                            <?php echo $fila['id_usuario']; ?>
                        </td>

                        <td>
                            <?php echo $fila['nombre_completo']; ?>
                        </td>

                        <td>
                            <?php echo $fila['correo']; ?>
                        </td>

                        <td>

                            $

                            <?php
                            echo number_format(
                                $fila['saldo'],
                                2
                            );
                            ?>

                        </td>

                        <td>

                            <?php if($fila['estado'] == 'activo') { ?>

                                <span class="status active">

                                    Activo

                                </span>

                            <?php } else { ?>

                                <span class="status inactive">

                                    Inactivo

                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?php if($fila['rol'] == 'admin') { ?>

                                <span class="role admin">

                                    Admin

                                </span>

                            <?php } else { ?>

                                <span class="role client">

                                    Cliente

                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?php if($fila['estado'] == 'activo') { ?>

                                <a
                                    class="table-btn danger"
                                    href="cambiar_estado.php?id=<?php echo $fila['id_usuario']; ?>&estado=inactivo">

                                    Desactivar

                                </a>

                            <?php } else { ?>

                                <a
                                    class="table-btn success"
                                    href="cambiar_estado.php?id=<?php echo $fila['id_usuario']; ?>&estado=activo">

                                    Activar

                                </a>

                            <?php } ?>

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
                onclick="window.location.href='reportes.php'">

                Reportes

            </button>

        </div>

        <div class="footer">

            NovaBank © 2026

        </div>

    </div>

</div>

</body>

</html>