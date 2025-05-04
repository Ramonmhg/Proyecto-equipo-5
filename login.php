<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include('conexion.php');

// Variable para el mensaje de alerta
$mensaje = "";

// Verificar si el usuario está intentando iniciar sesión
if (isset($_POST['login'])) {
    $dni = $_POST['dni'];
    $password = $_POST['password'];

    // Consultar si el usuario existe en la base de datos
    $query = "SELECT NOMBRE, APELLIDOS, PASSWORD FROM EMPLEADO WHERE DNI = :dni";
    $stid = oci_parse($conexion, $query);
    oci_bind_by_name($stid, ':dni', $dni);
    oci_execute($stid);

    // Verificar si el DNI y la contraseña coinciden
    if ($row = oci_fetch_assoc($stid)) {
        if ($row['PASSWORD'] == $password) {
            // Iniciar sesión si los datos son correctos
            $_SESSION['dni'] = $dni;
            $_SESSION['nombre'] = $row['NOMBRE'];
            $_SESSION['apellidos'] = $row['APELLIDOS'];

            // Registrar la hora de entrada en la base de datos
            $hora_entrada = date("Y-m-d H:i:s");
            $insert_query = "INSERT INTO ENTRADA (ID_EMPLEADO, ENTRADA) VALUES ((SELECT ID FROM EMPLEADO WHERE DNI = :dni), TO_DATE(:hora_entrada, 'YYYY-MM-DD HH24:MI:SS'))";
            $stid_insert = oci_parse($conexion, $insert_query);
            oci_bind_by_name($stid_insert, ':dni', $dni);
            oci_bind_by_name($stid_insert, ':hora_entrada', $hora_entrada);
            oci_execute($stid_insert);

            // Establecer el mensaje de éxito
            $mensaje = "Inicio de sesión correcto. Bienvenido, " . $_SESSION['nombre'] . " " . $_SESSION['apellidos'] . ".";
        } else {
            // Si la contraseña no es correcta
            $mensaje = "Contraseña incorrecta. Intenta nuevamente.";
        }
    } else {
        // Si el DNI no existe en la base de datos
        $mensaje = "DNI no encontrado. Intenta nuevamente.";
    }
}

// Verificar si el usuario está intentando cerrar sesión
if (isset($_POST['logout'])) {
    // Destruir la sesión
    session_destroy();

    // Establecer el mensaje de éxito para cerrar sesión
    $mensaje = "Has cerrado sesión correctamente.";

    // Eliminar la entrada de la tabla ENTRADA para el día de hoy (opcional)
    // (No es estrictamente necesario para este propósito, pero se incluye aquí para limpiar la base de datos)
    $dni = $_SESSION['dni'];
    $delete_query = "DELETE FROM ENTRADA WHERE ID_EMPLEADO = (SELECT ID FROM EMPLEADO WHERE DNI = :dni) AND TRUNC(ENTRADA) = TRUNC(SYSDATE)";
    $stid_delete = oci_parse($conexion, $delete_query);
    oci_bind_by_name($stid_delete, ':dni', $dni);
    oci_execute($stid_delete);
}

// Mostrar el mensaje de alerta utilizando JavaScript
echo "<script type='text/javascript'>
        alert('$mensaje');
        window.location.href = 'index.html'; // Redirige al index.html después de hacer clic en OK
      </script>";
?>