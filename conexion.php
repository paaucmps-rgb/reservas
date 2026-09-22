<?php
// Datos de configuración para XAMPP
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "reservas_cerro"; // Nombre exacto de la BD

// Conexión mediante MySQLi
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);
//Cambios hechos por Paula
if ($conexion->connect_error) {
    throw new Exception("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>