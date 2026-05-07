<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['id_usuario'])) die("No autorizado");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo_cuenta'];
    $id_usuario = $_SESSION['id_usuario'];

    try {
        $conn->beginTransaction();

        // generacion cuenta
        do {
            $numero_cuenta = rand(1000000000, 9999999999);
            $check = $conn->prepare("SELECT id_cuenta FROM CUENTA WHERE numero_cuenta = ?");
            $check->execute([$numero_cuenta]);
        } while ($check->fetch());

        $sql_c = "INSERT INTO CUENTA (id_usuario, numero_cuenta, tipo_cuenta, saldo, estado) VALUES (?, ?, ?, 0.00, 'activa')";
        $stmt_c = $conn->prepare($sql_c);
        $stmt_c->execute([$id_usuario, $numero_cuenta, $tipo]);
        
        $id_c = $conn->id_c = $conn->lastInsertId();

        // visa
        do {
            $num_t = "4" . rand(100000000000000, 999999999999999);
            $check_t = $conn->prepare("SELECT id_tarjeta_debito FROM TARJETA_DEBITO WHERE numero_tarjeta_debito = ?");
            $check_t->execute([$num_t]);
        } while ($check_t->fetch());

        $f_exp = date('Y-m-d', strtotime('+5 years'));
        $cvv = password_hash(str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT), PASSWORD_BCRYPT);
        $pin = password_hash("1234", PASSWORD_BCRYPT);

        $sql_t = "INSERT INTO TARJETA_DEBITO (id_cuenta, numero_tarjeta_debito, fecha_expiracion, cvv_hash, pin_hash, estado, tipo_red) 
                  VALUES (?, ?, ?, ?, ?, 'activa', 'visa')";
        $stmt_t = $conn->prepare($sql_t);
        $stmt_t->execute([$id_c, $num_t, $f_exp, $cvv, $pin]);

        $conn->commit();
        echo "Éxito: Cuenta y Tarjeta creadas.";

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
