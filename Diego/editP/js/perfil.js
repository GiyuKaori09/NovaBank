const original = {
  nombre: '',
  apellido: '',
  curp: '',
  fecha: '',
  direccion: '',
  'num-cuenta': '',
  cic: ''
};

// Mostrar u ocultar campo sensible
function toggleVis(id, btn) {
  const input = document.getElementById(id);
  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.querySelector('svg').innerHTML = isHidden
    ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
    : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

// Actualizar nombre en el encabezado en tiempo real
function actualizarNombre() {
  const nombre = document.getElementById('nombre').value.trim();
  const apellido = document.getElementById('apellido').value.trim();
  document.getElementById('display-name').textContent = nombre + ' ' + apellido;
}

// Forzar CURP en mayúsculas y solo caracteres válidos
document.getElementById('curp').addEventListener('input', function () {
  this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});

// Solo dígitos en número de cuenta
document.getElementById('num-cuenta').addEventListener('input', function () {
  this.value = this.value.replace(/\D/g, '').slice(0, 16);
});

// Solo dígitos en CIC
document.getElementById('cic').addEventListener('input', function () {
  this.value = this.value.replace(/\D/g, '').slice(0, 10);
});

// Listeners para actualizar el encabezado
document.getElementById('nombre').addEventListener('input', actualizarNombre);
document.getElementById('apellido').addEventListener('input', actualizarNombre);

// Restablecer valores originales
function resetForm() {
  Object.keys(original).forEach(function (key) {
    document.getElementById(key).value = original[key];
  });
  actualizarNombre();
}

// Validar y guardar
function guardar() {
  const curp = document.getElementById('curp').value;
  const cuenta = document.getElementById('num-cuenta').value;
  const cic = document.getElementById('cic').value;

  if (curp.length !== 18) {
    alert('La CURP debe tener exactamente 18 caracteres.');
    return;
  }
  if (cuenta.length !== 16) {
    alert('El número de cuenta debe tener 16 dígitos.');
    return;
  }
  if (cic.length < 9) {
    alert('El código CIC debe tener al menos 9 dígitos.');
    return;
  }

  const toast = document.getElementById('toast');
  toast.classList.add('show');
  setTimeout(function () {
    toast.classList.remove('show');
  }, 3500);

  actualizarNombre();
}
