<?php
$usuario = 'system';  // Cambia según tu usuario Oracle
$contrasena = '******';  // Cambia por tu contraseña
$conexion = oci_connect($usuario, $contrasena, 'localhost/XE');

if (!$conexion) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}
?>