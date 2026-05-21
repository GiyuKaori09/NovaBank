<?php

require_once 'db.php';
require_once 'security.php'; // 1. Importamos la clase de seguridad

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $ip_origen = $_SERVER['REMOTE_ADDR']; // Capturamos la IP para el control de intentos

    try {

        // 2. VERIFICACIÓN DE BLOQUEO POR FUERZA BRUTA (H24)
        if (Security::check_access_limit($conn, $correo, $ip_origen)) {
            $error = "Acceso temporalmente bloqueado. Demasiados intentos fallidos (Máximo 5 en 15 min).";
        } else {

 
            $sql = "
                SELECT *
                FROM USUARIO
                WHERE correo = ?
                AND estado = 'activo'
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$correo]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 4. Verificación de credenciales usando tus nombres de columna
            if ($user && password_verify($password, $user['contrasena_hash'])) {

                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nombre'] = $user['nombre_completo'];
                $_SESSION['correo'] = $user['correo'];

                // 5. AUDITORÍA: Registro de inicio de sesión exitoso (H23)
                Security::audit($conn, 'USUARIO', $user['id_usuario'], 'LOGIN', 'Inicio de sesión exitoso en el sistema.');

                header("Location: dashboard.php");
                exit;

            } else {

                // 6. CONTROL INTRUSOS: Si falla, registramos el intento en la BD (H24)
                $sqlIntento = "INSERT INTO intento_acceso (correo, ip_origen) VALUES (?, ?)";
                $conn->prepare($sqlIntento)->execute([$correo, $ip_origen]);

                $error = "Correo o contraseña incorrectos";
            }
        }

    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>NovaBank | Acceso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>

    <div class="glow"></div>

    <div class="container">

        <div class="login-card">

            <div class="logo">
                <h1>Nova<span>Bank</span></h1>
            </div>

            <div class="subtitle">
                Acceso seguro para tu banca digital
            </div>

            <?php if (isset($error)): ?>
                <div class="message" style="color: #ff7b7b; background: rgba(255,123,123,0.1); padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 0.9rem; border: 1px solid rgba(255,123,123,0.2);">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="input-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" placeholder="ejemplo@correo.com" required>
                </div>

                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button class="btn" type="submit">
                    Iniciar sesión
                </button>

            </form>

            <div class="footer">
                ¿No tienes cuenta?
                <br>
                <a href="register.php">Registrarse</a>
            </div>

        </div>

    </div>

</body>

</html>




