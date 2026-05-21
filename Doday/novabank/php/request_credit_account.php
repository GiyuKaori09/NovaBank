<?php

require_once 'db.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {

    die("Usuario no autenticado");
}

$id_usuario = $_SESSION['id_usuario'];

try {


    $sql = "
        SELECT id_cuenta
        FROM CUENTA
        WHERE
            id_usuario = ?
            AND tipo_cuenta = 'credito'
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$id_usuario]);

    $existe = $stmt->fetch();

    if ($existe) {

        die("Ya tienes una tarjeta de crédito");
    }


    $numero_cuenta =
        rand(1000000000, 9999999999);


    $sql = "
        INSERT INTO CUENTA (
            id_usuario,
            numero_cuenta,
            tipo_cuenta,
            saldo,
            estado
        )
        VALUES (
            ?,
            ?,
            'credito',
            5000,
            'activa'
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $id_usuario,
        $numero_cuenta
    ]);

    echo "Tarjeta de crédito creada correctamente";

} catch (PDOException $e) {

    echo "Error: " . $e->getMessage();
}
?>