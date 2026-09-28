<?php
// Datos de conexión a MySQL (XAMPP por defecto)
$servidor = "localhost";
$usuario = "root";
$password = ""; 
$base_datos = "reservas_cerro"; // Nombre exacto de la BD de Alejandra[cite: 2]

// Crear la conexión utilizando MySQLi orientada a objetos
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Comprobar si ha fallado la conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>