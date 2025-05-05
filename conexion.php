<?php
$usuario = 'empleados';  // Cambia según tu usuario Oracle
$contrasena = '1234';  // Cambia por tu contraseña
$conexion = oci_connect($usuario, $contrasena, 'localhost/XE');

if (!$conexion) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}
?>
