<?php

require_once 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo']);

    try {

        $sql = "
            SELECT *
            FROM USUARIO
            WHERE correo = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([$correo]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            $_SESSION['reset_correo'] =
                $correo;

            header(
                "Location: reset_password.php"
            );

            exit;

        } else {

            $error =
                "No existe una cuenta con ese correo";
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
        Recuperar contraseña
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

                Recuperar contraseña

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
                        required
                    >

                </div>

                <button
                    class="btn"
                    type="submit">

                    Continuar

                </button>

            </form>

        </div>

    </div>

</body>

</html>