<?php
// Conexión a la base de datos
require_once('../../config/db.php');


if (isset($_GET['id'])) {
    $transaction_id = $_GET['id'];

 
    $sql = "SELECT T.*, C.numero_cuenta 
            FROM TRANSACCION T 
            JOIN CUENTA C ON T.id_cuenta_origen = C.id_cuenta 
            WHERE T.id_transaccion = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        die("Transacción no encontrada.");
    }
} else {
    die("ID de transacción no proporcionado.");
}
?>
