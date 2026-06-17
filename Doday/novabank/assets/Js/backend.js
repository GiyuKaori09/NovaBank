fetch('php/request_credit_account.php')

.then(response => response.json())

.then(data => {

    const debitoDiv = document.getElementById('cuentaDebito');
    const creditoDiv = document.getElementById('cuentaCredito');

    data.forEach(cuenta => {

        if (
            cuenta.tipo_cuenta === 'ahorro' ||
            cuenta.tipo_cuenta === 'cheques'
        ) {

            debitoDiv.innerHTML += `
                <div class="cardCuenta">

                    <h2>Cuenta Débito</h2>

                    <p><strong>Número:</strong>
                    ${cuenta.numero_cuenta}</p>

                    <p><strong>Tipo:</strong>
                    ${cuenta.tipo_cuenta}</p>

                    <p><strong>Saldo:</strong>
                    $${cuenta.saldo}</p>

                    <p><strong>Estado:</strong>
                    ${cuenta.estado}</p>

                </div>
            `;
        }

        if (cuenta.tipo_cuenta === 'credito') {

            creditoDiv.innerHTML += `
                <div class="cardCuenta">

                    <h2>Cuenta Crédito</h2>

                    <p><strong>Número:</strong>
                    ${cuenta.numero_cuenta}</p>

                    <p><strong>Saldo usado:</strong>
                    $${cuenta.saldo}</p>

                    <p><strong>Límite:</strong>
                    $${cuenta.limite_credito}</p>

                    <p><strong>Estado:</strong>
                    ${cuenta.estado}</p>

                </div>
            `;
        }

    });

});