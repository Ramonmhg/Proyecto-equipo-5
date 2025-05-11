<?php
session_start();
include('conexion.php');
$mensaje = "";

// LOGIN
if (isset($_POST['login'])) {
    $dni = $_POST['dni'];
    $password = $_POST['password'];
    $hora_entrada = $_POST['hora_actual'];

    $query = "SELECT ID, NOMBRE, APELLIDOS, PASSWORD FROM EMPLEADO WHERE DNI = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, 's', $dni);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['PASSWORD'] == $password) {
            $_SESSION['dni'] = $dni;
            $_SESSION['nombre'] = $row['NOMBRE'];
            $_SESSION['apellidos'] = $row['APELLIDOS'];
            $id_empleado = $row['ID'];

            // Registrar ENTRADA
            $insert_query = "INSERT INTO ENTRADA (ID_EMPLEADO, ENTRADA) VALUES (?, ?)";
            $stmt_insert = mysqli_prepare($conexion, $insert_query);
            mysqli_stmt_bind_param($stmt_insert, 'is', $id_empleado, $hora_entrada);
            mysqli_stmt_execute($stmt_insert);

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

        $query = "SELECT ID FROM EMPLEADO WHERE DNI = ?";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, 's', $dni);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $id_empleado = $row['ID'];

        // Registrar SALIDA
        $insert_salida = "INSERT INTO SALIDA (ID_EMPLEADO, SALIDA) VALUES (?, ?)";
        $stmt_salida = mysqli_prepare($conexion, $insert_salida);
        mysqli_stmt_bind_param($stmt_salida, 'is', $id_empleado, $hora_salida);
        mysqli_stmt_execute($stmt_salida);
    }

    $mensaje = "Has cerrado sesión " . $_SESSION['nombre'] . " " . $_SESSION['apellidos'] . " correctamente a las $hora_salida.";
    session_destroy();
}

// Redirigir con mensaje
echo "<script type='text/javascript'>
        alert('$mensaje');
        window.location.href = 'index.html';
      </script>";
?>
