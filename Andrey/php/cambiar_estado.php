<?php

session_start();
include("conexion.php");

$id = $_GET['id'];
$estado = $_GET['estado'];

$sql = "UPDATE USUARIO
        SET estado='$estado'
        WHERE id_usuario='$id'";

mysqli_query($conexion, $sql);

$admin = $_SESSION['nombre'];

$sqlReporte = "INSERT INTO REPORTE (usuario, accion)
VALUES ('$admin', 'Cambió el estado de una cuenta a $estado')";

mysqli_query($conexion, $sqlReporte);

header("Location: admin.php");
exit();

?>