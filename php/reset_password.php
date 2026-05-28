<?php

require_once 'db.php';

session_start();

if (!isset($_SESSION['reset_correo'])) {

    header(
        "Location: forgot_password.php"
    );

    exit;
}

$correo = $_SESSION['reset_correo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password =
        trim($_POST['password']);

    $confirmar =
        trim($_POST['confirmar']);

    if (strlen($password) < 8) {

        $error =
            "La contraseña debe tener mínimo 8 caracteres";
    }

    elseif ($password !== $confirmar) {

        $error =
            "Las contraseñas no coinciden";
    }

    else {

        try {

            $hash = password_hash(
                $password,
                PASSWORD_BCRYPT
            );

            $sql = "
                UPDATE USUARIO
                SET contrasena_hash = ?
                WHERE correo = ?
            ";

            $stmt =
                $conn->prepare($sql);

            $stmt->execute([
                $hash,
                $correo
            ]);

            unset(
                $_SESSION['reset_correo']
            );

            $_SESSION['success'] =
                "Contraseña actualizada correctamente";

            header(
                "Location: login.php"
            );

            exit;

        } catch (PDOException $e) {

            $error =
                "Error: " .
                $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Nueva contraseña
    </title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
          href="../assets/css/styles.css">

</head>

<body>

    <div class="glow"></div>

    <div class="container">

        <div class="login-card">

            <div class="logo">

                <h1>
                    Nova<span>Bank</span>
                </h1>

            </div>

            <div class="subtitle">

                Crear nueva contraseña

            </div>

            <?php if (isset($error)): ?>

                <div class="message">

                    <?php echo $error; ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="input-group">

                    <label>
                        Nueva contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                    >

                </div>

                <div class="input-group">

                    <label>
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        name="confirmar"
                        required
                    >

                </div>

                <button
                    class="btn"
                    type="submit">

                    Actualizar contraseña

                </button>

            </form>

        </div>

    </div>

</body>

</html>