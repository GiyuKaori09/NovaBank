<?php

$host = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "novabank_db";

$conexion = mysqli_connect(
    $host,
    $usuario,
    $contrasena,
    $basedatos
);

if (!$conexion) {
    die("Error de conexión");
}

?>