<?php
include('conexion.php');

$nombre = $_POST['nombre'];
$email = $_POST['email'];

// Verificar si el correo ya existe
$verificar = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$verificar->bind_param("s", $email);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows > 0) {
  echo "EXISTE"; // el front lo interpreta como correo duplicado
} else {
  $sql = $conn->prepare("INSERT INTO usuarios (nombre, email) VALUES (?, ?)");
  $sql->bind_param("ss", $nombre, $email);

  if ($sql->execute()) {
    echo "OK";
  } else {
    echo "ERROR";
  }

  $sql->close();
}

$conn->close();
?>