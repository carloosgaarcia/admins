<?php
session_start();
include('conexion.php');

// Verificar sesión
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}

// Mensajes de feedback
$mensaje = '';

// --- 1) Crear nuevo administrador ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_usuario'])) {
    $nuevo_usuario = trim($_POST['nuevo_usuario']);
    $nueva_clave = $_POST['nueva_clave'];

    if ($nuevo_usuario === '' || $nueva_clave === '') {
        $mensaje = "Ingrese usuario y contraseña para crear admin.";
    } else {
        $clave_hashed = password_hash($nueva_clave, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO administradores (usuario, clave) VALUES (?, ?)");
        $stmt->bind_param("ss", $nuevo_usuario, $clave_hashed);
        if ($stmt->execute()) {
            $mensaje = "✅ Nuevo administrador creado correctamente.";
        } else {
            if ($conn->errno === 1062) $mensaje = "❌ Ese usuario ya existe.";
            else $mensaje = "❌ Error: " . htmlspecialchars($conn->error);
        }
        $stmt->close();
    }
}

// --- 2) Resetear / actualizar contraseña ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_admin'])) {
    $admin_id = intval($_POST['admin_id']);
    $nueva_clave_edit = $_POST['nueva_clave_edit'] ?? '';

    if ($nueva_clave_edit === '') {
        $mensaje = "❌ La nueva contraseña no puede estar vacía.";
    } else {
        $clave_hashed = password_hash($nueva_clave_edit, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE administradores SET clave = ? WHERE id = ?");
        $stmt->bind_param("si", $clave_hashed, $admin_id);
        if ($stmt->execute()) {
            $mensaje = "✅ Contraseña actualizada correctamente.";
        } else {
            $mensaje = "❌ Error al actualizar: " . htmlspecialchars($conn->error);
        }
        $stmt->close();
    }
}

// --- 3) Eliminar administrador ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_admin'])) {
    $admin_id_del = intval($_POST['admin_id_del']);

    if ($admin_id_del === intval($_SESSION['admin_id'])) {
        $mensaje = "❌ No puedes eliminar tu propia cuenta mientras estás logueado.";
    } else {
        $res = $conn->query("SELECT COUNT(*) as cnt FROM administradores");
        $row = $res->fetch_assoc();
        if (intval($row['cnt']) <= 1) {
            $mensaje = "❌ No se puede eliminar al último administrador.";
        } else {
            $stmt = $conn->prepare("DELETE FROM administradores WHERE id = ?");
            $stmt->bind_param("i", $admin_id_del);
            if ($stmt->execute()) {
                $mensaje = "✅ Administrador eliminado correctamente.";
            } else {
                $mensaje = "❌ Error al eliminar admin: " . htmlspecialchars($conn->error);
            }
            $stmt->close();
        }
    }
}

// --- 4) Eliminar usuario ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario'])) {
    $usuario_id_del = intval($_POST['usuario_id_del']);
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id_del);
    if ($stmt->execute()) {
        $mensaje = "✅ Registro de usuario eliminado correctamente.";
    } else {
        $mensaje = "❌ Error al eliminar usuario: " . htmlspecialchars($conn->error);
    }
    $stmt->close();
}

// Consultas
$result = $conn->query("SELECT id, nombre, email, telefono, carrera FROM usuarios ORDER BY id ASC");
$result_admin = $conn->query("SELECT id, usuario FROM administradores ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Administración</title>
<style>
body {
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background: linear-gradient(135deg, #0f172a, #1e293b);
  color: #f1f5f9;
  margin: 0;
  padding: 40px 20px;
  min-height: 100vh;
}

.container {
  max-width: 1100px;
  margin: auto;
  background: rgba(255,255,255,0.05);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 0 25px rgba(0,0,0,0.3);
  animation: fadeIn 0.6s ease-in-out;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

h2, h3 {
  margin: 0 0 10px;
  color: #f8fafc;
}

.table {
  width: 100%;
  border-collapse: collapse;
  background: #1e293b;
  border-radius: 8px;
  overflow: hidden;
}

.table th, .table td {
  padding: 10px 12px;
  text-align: left;
}

.table th {
  background-color: #2563eb;
  color: white;
  font-weight: 600;
}

.table tr:nth-child(even) { background-color: #273549; }
.table tr:hover { background-color: #334155; }

.btn {
  padding: 7px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-primary { background-color: #2563eb; color: #fff; }
.btn-primary:hover { background-color: #1e40af; transform: scale(1.05); }

.btn-danger { background-color: #dc2626; color: white; }
.btn-danger:hover { background-color: #b91c1c; transform: scale(1.05); }

.btn-home {
  background-color: #10b981;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.3s ease;
}
.btn-home:hover { background-color: #059669; transform: scale(1.05); }

.notice {
  background-color: rgba(16, 185, 129, 0.1);
  border-left: 4px solid #10b981;
  color: #d1fae5;
  padding: 10px;
  margin-bottom: 18px;
  border-radius: 6px;
}

.form-inline input {
  padding: 6px;
  margin-right: 6px;
  border-radius: 6px;
  border: 1px solid #475569;
  background-color: #0f172a;
  color: #f1f5f9;
}

.small { font-size: 13px; color: #9ca3af; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h2>Panel de Administración — 
      <?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'Invitado'); ?>
    </h2>
    <div>
      <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
      <a href="http://uvm.test" class="btn-home" target="_blank">Página principal</a>
      <style>
  .btn-home {
    background-color: #28a745; /* verde profesional */
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
  }

  .btn-home:hover {
    background-color: #218838; /* tono más oscuro */
    box-shadow: 0 4px 10px rgba(40, 167, 69, 0.4);
    transform: translateY(-2px); /* leve efecto de elevación */
  }
</style>
    </div>
  </div>

  <?php if ($mensaje): ?>
    <div class="notice"><?php echo $mensaje; ?></div>
  <?php endif; ?>

  <!-- Crear nuevo admin -->
  <section style="margin-bottom:20px;">
    <h3>Crear nuevo administrador</h3>
    <form method="POST" class="form-inline">
      <input type="text" name="nuevo_usuario" placeholder="Usuario" required>
      <input type="password" name="nueva_clave" placeholder="Contraseña" required>
      <button type="submit" class="btn btn-primary">Crear Admin</button>
    </form>
    <div class="small">Las contraseñas se guardan con hash seguro.</div>
  </section>

  <!-- Administradores -->
  <section style="margin-bottom:25px;">
    <h3>Administradores</h3>
    <table class="table">
      <tr><th>ID</th><th>Usuario</th><th>Acciones</th></tr>
      <?php while($admin = $result_admin->fetch_assoc()): ?>
      <tr>
        <td><?php echo $admin['id']; ?></td>
        <td><?php echo htmlspecialchars($admin['usuario']); ?></td>
        <td>
          <form method="POST" style="display:inline-block;">
            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
            <input type="password" name="nueva_clave_edit" placeholder="Nueva contraseña" required>
            <button type="submit" name="editar_admin" class="btn btn-primary">Actualizar</button>
          </form>
          <form method="POST" style="display:inline-block;" onsubmit="return confirm('¿Eliminar administrador?');">
            <input type="hidden" name="admin_id_del" value="<?php echo $admin['id']; ?>">
            <button type="submit" name="eliminar_admin" class="btn btn-danger">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </section>

  <!-- Usuarios -->
  <section>
    <h3>Usuarios registrados</h3>
    <table class="table">
      <tr>
        <th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Carrera</th><th>Acciones</th>
      </tr>
      <?php
      $contador = 0;
      while($row = $result->fetch_assoc()):
        $contador++;
        switch ($row['carrera']) {
          case 'ingenieria_sistemas': $nombreCarrera = 'Ingeniería en Sistemas'; break;
          case 'administracion': $nombreCarrera = 'Administración'; break;
          case 'derecho': $nombreCarrera = 'Derecho'; break;
          case 'medicina': $nombreCarrera = 'Medicina'; break;
          default: $nombreCarrera = 'No especificada'; break;
        }
      ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['telefono'] ?? '—'); ?></td>
        <td><?php echo htmlspecialchars($nombreCarrera); ?></td>
        <td>
          <form method="POST" onsubmit="return confirm('¿Eliminar este usuario?');" style="display:inline-block;">
            <input type="hidden" name="usuario_id_del" value="<?php echo $row['id']; ?>">
            <button type="submit" name="eliminar_usuario" class="btn btn-danger">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>

    <p style="margin-top:10px; font-weight:bold;">
      Total de usuarios registrados: <?php echo $contador; ?>
    </p>
  </section>
</div>
</body>
</html>