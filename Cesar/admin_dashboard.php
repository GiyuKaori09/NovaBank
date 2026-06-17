<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

try {
    $sql = "SELECT id_usuario, nombre_completo, correo, curp, rol, estado FROM USUARIO ORDER BY id_usuario DESC";
    $stmt = $conn->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NovaBank | Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 10px; color: white; font-size: 14px; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #444; }
        .admin-table th { background-color: #6b21a8; font-weight: 600; }
        .status-activo { color: #7dffb3; }
        .status-inactivo { color: #ff7b7b; }
        .btn-small { padding: 6px 10px; font-size: 13px; margin: 0; }
        .table-container { overflow-x: auto; background: rgba(255, 255, 255, 0.05); border-radius: 10px; padding: 15px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="profile-container" style="max-width: 1000px;">
        <div class="profile-card" style="max-width: 100%; padding: 30px;">
            <div class="logo">
                <h1>Nova<span>Bank</span></h1>
            </div>
            
            <p class="subtitle">Gestión de Usuarios del Sistema</p>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>CURP</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $user): ?>
                            <tr>
                                <td><?php echo $user['id_usuario']; ?></td>
                                <td><?php echo htmlspecialchars($user['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($user['correo']); ?></td>
                                <td><?php echo htmlspecialchars($user['curp']); ?></td>
                                <td><?php echo ucfirst($user['rol']); ?></td>
                                <td id="estado-texto-<?php echo $user['id_usuario']; ?>" 
                                    class="<?php echo $user['estado'] === 'activo' ? 'status-activo' : 'status-inactivo'; ?>">
                                    <?php echo ucfirst($user['estado']); ?>
                                </td>
                                <td>
                                    <?php if ($user['id_usuario'] !== $_SESSION['id_usuario']): ?>
                                        <button class="btn btn-small"
                                                id="btn-toggle-<?php echo $user['id_usuario']; ?>"
                                                onclick="cambiarEstado(<?php echo $user['id_usuario']; ?>, '<?php echo $user['estado']; ?>')">
                                            <?php echo $user['estado'] === 'activo' ? 'Desactivar' : 'Activar'; ?>
                                        </button>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <br><br>
            <button class="btn" onclick="window.location.href='logout.php'">Cerrar Sesión</button>
        </div>
    </div>

    <script>
        function cambiarEstado(idUsuario, estadoActual) {
            if (!confirm('¿Seguro que quieres cambiar el estado de este usuario?')) return;

            fetch('toggle_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_usuario: idUsuario, estado: estadoActual })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const nuevoEstado = data.nuevo_estado;
                    const tdEstado = document.getElementById(`estado-texto-${idUsuario}`);
                    const boton = document.getElementById(`btn-toggle-${idUsuario}`);

                    tdEstado.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
                    tdEstado.className = nuevoEstado === 'activo' ? 'status-activo' : 'status-inactivo';
                    
                    boton.textContent = nuevoEstado === 'activo' ? 'Desactivar' : 'Activar';
                    boton.setAttribute('onclick', `cambiarEstado(${idUsuario}, '${nuevoEstado}')`);
                } else {
                    alert('Error al actualizar: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>