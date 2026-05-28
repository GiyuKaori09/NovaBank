<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $curp = strtoupper(trim($_POST['curp']));
    $fecha = $_POST['fecha_nacimiento'];
    $direccion = trim($_POST['direccion']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    if (strlen($curp) !== 18) {
        $error = "CURP inválida";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo inválido";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener mínimo 8 caracteres";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $conn->beginTransaction();

            $sql = "
                INSERT INTO USUARIO (
                    nombre_completo,
                    curp,
                    fecha_nacimiento,
                    direccion,
                    correo,
                    contrasena_hash,
                    estado
                )
                VALUES (?, ?, ?, ?, ?, ?, 'activo')
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $nombre,
                $curp,
                $fecha,
                $direccion,
                $correo,
                $hash
            ]);

            $id_usuario = $conn->lastInsertId();
            $numero_cuenta = rand(1000000000, 9999999999);

            $sqlCuenta = "
                INSERT INTO CUENTA (
                    id_usuario,
                    numero_cuenta,
                    tipo_cuenta,
                    saldo,
                    estado
                )
                VALUES (?, ?, 'debito', 0, 'activa')
            ";

            $stmtCuenta = $conn->prepare($sqlCuenta);
            $stmtCuenta->execute([
                $id_usuario,
                $numero_cuenta
            ]);

            $conn->commit();
            header("Location: login.php");
            exit;

        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>NovaBank | Registro</title>
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
                Crea tu cuenta bancaria digital
            </div>

            <?php if (isset($error)): ?>
                <div class="message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" placeholder="Juan Pérez" required>
                </div>

                <div class="input-group">
                    <label>CURP</label>
                    <input type="text" name="curp" placeholder="CURP" maxlength="18" required>
                </div>

                <div class="input-group">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" required>
                </div>

                <div class="input-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion" placeholder="Dirección" required>
                </div>

                <div class="input-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" placeholder="correo@ejemplo.com" required>
                </div>

                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button class="btn" type="submit">
                    Crear cuenta
                </button>
            </form>

            <div class="footer">
                ¿Ya tienes cuenta?<br>
                <a href="login.php">Iniciar sesión</a>
            </div>

        </div>

    </div>

</body>
</html>