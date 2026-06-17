<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>NovaBank - Perfil</title>

    <link rel="stylesheet"
          href="../assets/css/styles.css">

</head>

<body>

    <div class="glow"></div>

    <div class="profile-container">

        <div class="profile-card">

            <div class="logo">

                <h1>
                    Nova<span>Bank</span>
                </h1>

            </div>

            <p class="subtitle">

                Panel de cuentas del usuario

            </p>

            <div class="section-title">

                Cuenta Débito

            </div>

            <div id="cuentaDebito">

                <div class="account-card loading">

                    Cargando cuenta...

                </div>

            </div>

            <div class="section-title">

                Tarjeta de Crédito

            </div>

            <div id="cuentaCredito">

                <div class="account-card loading">

                    Sin tarjeta de crédito

                </div>

            </div>

            <button id="btnCredito"
                    class="btn">

                Solicitar Tarjeta de Crédito

            </button>

            <br><br>

            <button class="btn"
                    onclick="window.location.href='dashboard.php'">

                Volver al Dashboard

            </button>

            <div id="mensaje"></div>

        </div>

    </div>

<script>

fetch('profile_accounts.php')

.then(response => response.json())

.then(data => {

    console.log(data);

    const debitoDiv =
        document.getElementById('cuentaDebito');

    debitoDiv.innerHTML = '';

    if (!data.success) {

        debitoDiv.innerHTML = `

            <div class="account-card">

                Error cargando cuentas

            </div>

        `;

        return;
    }

    if (data.cuentas.length === 0) {

        debitoDiv.innerHTML = `

            <div class="account-card">

                No tienes cuentas registradas

            </div>

        `;

        return;
    }

    data.cuentas.forEach(cuenta => {

        if (cuenta.tipo_cuenta === 'credito') {

            document
            .getElementById('cuentaCredito')
            .innerHTML = `

                <div class="account-card">

                    <p>

                        <span>Número:</span>

                        ${cuenta.numero_cuenta}

                    </p>

                    <p>

                        <span>Tipo:</span>

                        ${cuenta.tipo_cuenta}

                    </p>

                    <p>

                        <span>Saldo:</span>

                        $${cuenta.saldo}

                    </p>

                    <p>

                        <span>Estado:</span>

                        ${cuenta.estado}

                    </p>

                </div>

            `;

        } else {

            debitoDiv.innerHTML += `

                <div class="account-card">

                    <p>

                        <span>Número:</span>

                        ${cuenta.numero_cuenta}

                    </p>

                    <p>

                        <span>Tipo:</span>

                        ${cuenta.tipo_cuenta}

                    </p>

                    <p>

                        <span>Saldo:</span>

                        $${cuenta.saldo}

                    </p>

                    <p>

                        <span>Estado:</span>

                        ${cuenta.estado}

                    </p>

                </div>

            `;
        }
    });

})

.catch(error => {

    console.log(error);

});

document.getElementById('btnCredito')

.addEventListener('click', () => {

    fetch('request_credit_account.php')

    .then(response => response.text())

    .then(data => {

        document.getElementById('mensaje').innerHTML = `

            <div class="message">

                ${data}

            </div>

        `;

    })

    .catch(error => {

        console.log(error);

    });

});

</script>

</body>

</html>