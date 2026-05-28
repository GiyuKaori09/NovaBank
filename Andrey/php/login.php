<?php

require_once 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    try {

        $sql = "
            SELECT *
            FROM USUARIO
            WHERE correo = ?
            AND estado = 'activo'
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([$correo]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            if (
                password_verify(
                    $password,
                    $user['contrasena_hash']
                )
            ) {

                $_SESSION['id_usuario'] =
                    $user['id_usuario'];

                $_SESSION['nombre'] =
                    $user['nombre_completo'];

                $_SESSION['correo'] =
                    $user['correo'];

                header("Location: dashboard.php");
                exit;

            } else {

                $error =
                    "Contraseña incorrecta";
            }

        } else {

            $error = "

            No existe una cuenta con ese correo.

            <br><br>

            <a href='register.php'
               style='color:#c084fc;
                      font-weight:600;
                      text-decoration:none;'>

               Crear cuenta

            </a>
            ";
        }

    } catch (PDOException $e) {

        $error =
            "Error: " .
            $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        NovaBank | Acceso
    </title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
          rel="stylesheet">

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

                Acceso seguro para tu banca digital

            </div>

            <?php if (isset($error)): ?>

                <div class="message">

                    <?php echo $error; ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="input-group">

                    <label>
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="correo"
                        placeholder="ejemplo@correo.com"
                        required
                    >

                </div>

                <div class="input-group">

                    <label>
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >

                </div>

                <button
                    class="btn"
                    type="submit">

                    Iniciar sesión

                </button>

            </form>

            <div class="footer">

                <a href="forgot_password.php">

                    ¿Olvidaste tu contraseña?

                </a>

                <br><br>

                ¿No tienes cuenta?

                <br>

                <a href="register.php">

                    Registrarse

                </a>

            </div>

        </div>

    </div>

</body>

</html>