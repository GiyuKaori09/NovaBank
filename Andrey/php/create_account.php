<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("No autorizado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo = $_POST['tipo_cuenta'];

    $tipos_validos = ['ahorro', 'cheques', 'credito'];

    if (!in_array($tipo, $tipos_validos)) {
        die("Tipo de cuenta inválido");
    }

    $id_usuario = $_SESSION['id_usuario'];

    if ($tipo !== 'credito') {

        $sqlCheck = "SELECT COUNT(*) FROM CUENTA 
                     WHERE id_usuario = ? 
                     AND tipo_cuenta = ?";

        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([$id_usuario, $tipo]);

        $existe = $stmtCheck->fetchColumn();

        if ($existe > 0) {
            die("Ya tienes una cuenta de tipo $tipo");
        }
    }

    do {
        $numero = rand(1000000000, 9999999999);

        $sqlNumero = "SELECT COUNT(*) FROM CUENTA WHERE numero_cuenta = ?";
        $stmtNumero = $conn->prepare($sqlNumero);
        $stmtNumero->execute([$numero]);

    } while ($stmtNumero->fetchColumn() > 0);

    try {

        $saldoInicial = 0.00;

        $estado = 'activa';

        $sql = "INSERT INTO CUENTA 
        (id_usuario, numero_cuenta, tipo_cuenta, saldo, estado)
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $id_usuario,
            $numero,
            $tipo,
            $saldoInicial,
            $estado
        ]);

        echo "Cuenta $tipo creada correctamente";

    } catch (PDOException $e) {

        echo "Error: " . $e->getMessage();
    }
}
?>