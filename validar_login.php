<?php
session_start();
include('conexion.php');

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

// Buscar usuario en la base de datos
$sql = $conn->prepare("SELECT * FROM administradores WHERE usuario = ?");
$sql->bind_param("s", $usuario);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    // Verificar contraseña
    if (password_verify($clave, $row['clave'])) {
        $_SESSION['logueado'] = true;
        header("Location: listado.php");
        exit;
    } else {
        echo "<script>alert('Contraseña incorrecta'); window.location='login.php';</script>";
    }
} else {
    echo "<script>alert('Usuario no encontrado'); window.location='login.php';</script>";
}

$sql->close();
$conn->close();
?>