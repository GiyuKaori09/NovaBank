<?php
// security.php
class Security {
    // valida intentos fallidos (para login.php)
    public static function check_access_limit($conn, $email) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "SELECT count(*) FROM intento_acceso WHERE correo = ? AND ip_origen = ? AND fecha_intento > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email, $ip]);
        return $stmt->fetchColumn() >= 5; 
    }

    // registra eventos (usado en login, exportacion, etc)
    public static function audit($conn, $table, $id, $action, $description) {
        $sql = "INSERT INTO auditoria (tabla_afectada, id_registro, accion, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$table, $id, $action, $description]);
    }
}
