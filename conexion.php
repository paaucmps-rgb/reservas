<?php
// Datos de configuración para XAMPP
$servidor = "localhost";
$usuario = "root";
$password = ""; 
$base_datos = "reservas_cerro"; // Nombre exacto de la BD de Alejandra[cite: 2]

// Conexión mediante MySQLi
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>