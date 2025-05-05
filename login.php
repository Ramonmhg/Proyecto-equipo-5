<?php
session_start();
include('conexion.php');
$mensaje = "";

// LOGIN
if (isset($_POST['login'])) {
    $dni = $_POST['dni'];
    $password = $_POST['password'];
    $hora_entrada = $_POST['hora_actual'];

    $query = "SELECT NOMBRE, APELLIDOS, PASSWORD FROM EMPLEADO WHERE DNI = :dni";
    $stid = oci_parse($conexion, $query);
    oci_bind_by_name($stid, ':dni', $dni);
    oci_execute($stid);

    if ($row = oci_fetch_assoc($stid)) {
        if ($row['PASSWORD'] == $password) {
            $_SESSION['dni'] = $dni;
            $_SESSION['nombre'] = $row['NOMBRE'];
            $_SESSION['apellidos'] = $row['APELLIDOS'];

            // Registrar ENTRADA
            $insert_query = "INSERT INTO ENTRADA (ID_EMPLEADO, ENTRADA) 
                             VALUES ((SELECT ID FROM EMPLEADO WHERE DNI = :dni), 
                             TO_DATE(:hora_entrada, 'YYYY-MM-DD HH24:MI:SS'))";
            $stid_insert = oci_parse($conexion, $insert_query);
            oci_bind_by_name($stid_insert, ':dni', $dni);
            oci_bind_by_name($stid_insert, ':hora_entrada', $hora_entrada);
            oci_execute($stid_insert);

            $mensaje = "Inicio de sesión correcto a las $hora_entrada. Bienvenido, " . $_SESSION['nombre'] . " " . $_SESSION['apellidos'] . ".";
        } else {
            $mensaje = "Contraseña incorrecta. Intenta nuevamente.";
        }
    } else {
        $mensaje = "DNI no encontrado. Intenta nuevamente.";
    }
}

// LOGOUT
if (isset($_POST['logout'])) {
    if (isset($_SESSION['dni'])) {
        $dni = $_SESSION['dni'];
        $hora_salida = $_POST['hora_actual'];

        // Registrar SALIDA
        $insert_salida = "INSERT INTO SALIDA (ID_EMPLEADO, SALIDA) 
                          VALUES ((SELECT ID FROM EMPLEADO WHERE DNI = :dni), 
                          TO_DATE(:hora_salida, 'YYYY-MM-DD HH24:MI:SS'))";
        $stid_salida = oci_parse($conexion, $insert_salida);
        oci_bind_by_name($stid_salida, ':dni', $dni);
        oci_bind_by_name($stid_salida, ':hora_salida', $hora_salida);
        oci_execute($stid_salida);
    }

    session_destroy();
    $mensaje = "Has cerrado sesión correctamente a las $hora_salida.";
}

// Redirigir con mensaje
echo "<script type='text/javascript'>
        alert('$mensaje');
        window.location.href = 'index.html';
      </script>";
?>
