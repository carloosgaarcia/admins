<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Administrador</title>
  <style>
    /* Fuente y fondo general */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1f2937, #111827);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    /* Tarjeta principal */
    .login-card {
      background-color: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      padding: 40px 35px;
      width: 350px;
      text-align: center;
      transition: all 0.3s ease-in-out;
    }

    .login-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
    }

    /* Título */
    .login-card h2 {
      color: #111827;
      margin-bottom: 20px;
      font-size: 1.6rem;
      letter-spacing: 0.5px;
    }

    /* Inputs */
    .input-group {
      margin-bottom: 15px;
      text-align: left;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 5px;
      color: #374151;
      font-size: 0.9rem;
    }

    input {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #d1d5db;
      font-size: 1rem;
      outline: none;
      transition: border-color 0.2s ease;
    }

    input:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }

    /* Botón */
    button {
      width: 100%;
      background-color: #2563eb;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
      background-color: #1d4ed8;
      transform: scale(1.02);
    }

    /* Pie de página */
    .footer {
      margin-top: 20px;
      font-size: 0.85rem;
      color: #6b7280;
    }

    /* Animación de entrada */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-card {
      animation: fadeIn 0.6s ease-in-out;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Panel de Administración</h2>
    <form action="validar_login.php" method="POST">
      <div class="input-group">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario" required>
      </div>
      <div class="input-group">
        <label for="clave">Contraseña</label>
        <input type="password" id="clave" name="clave" placeholder="Ingrese su contraseña" required>
      </div>
      <button type="submit">Iniciar sesión</button>
    </form>
    <div class="footer">© 2025 Área Administrativa</div>
  </div>
</body>
</html>