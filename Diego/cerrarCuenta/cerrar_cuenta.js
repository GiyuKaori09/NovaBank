document.getElementById('motivo').addEventListener('change', function () {
  const campoOtro = document.getElementById('campo-otro');
  campoOtro.style.display = this.value === 'otro' ? 'flex' : 'none';
});

document.getElementById('num-cuenta').addEventListener('input', function () {
  this.value = this.value.replace(/\D/g, '').slice(0, 16);
});

document.getElementById('curp').addEventListener('input', function () {
  this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});


function confirmar() {
  const nombre    = document.getElementById('nombre').value.trim();
  const apellido  = document.getElementById('apellido').value.trim();
  const cuenta    = document.getElementById('num-cuenta').value.trim();
  const curp      = document.getElementById('curp').value.trim();
  const motivo    = document.getElementById('motivo').value;
  const acepto    = document.getElementById('acepto').checked;

  if (!nombre || !apellido) {
    alert('Por favor ingresa tu nombre y apellido.');
    return;
  }
  if (cuenta.length !== 16) {
    alert('El número de cuenta debe tener 16 dígitos.');
    return;
  }
  if (curp.length !== 18) {
    alert('La CURP debe tener exactamente 18 caracteres.');
    return;
  }
  if (!motivo) {
    alert('Por favor selecciona un motivo de cierre.');
    return;
  }
  if (!acepto) {
    alert('Debes aceptar que la acción es irreversible para continuar.');
    return;
  }

  const toast = document.getElementById('toast');
  toast.classList.add('show');

  //Deshabilitar botón para evitar doble envío
  document.querySelector('.btn-confirmar').disabled = true;
  document.querySelector('.btn-confirmar').style.opacity = '0.6';

  //Redirigir a main después de 3 segundos
  setTimeout(function () {
    window.location.href = 'main.html';
  }, 3000);
}
