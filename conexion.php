<?php
$host = "localhost";
$user = "root"; // o el usuario que tengas en tu phpMyAdmin
$password = ""; // tu contraseña si tienes
$database = "proyecto";

$conexion = mysqli_connect($host, $user, $password, $database);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
