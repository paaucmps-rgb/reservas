<?php
// Datos de conexión a MySQL (XAMPP por defecto)
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "reservas_cerro";

// Crear la conexión utilizando MySQLi orientada a objetos
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Comprobar si ha fallado la conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Asegurar el uso de codificación UTF-8 para evitar problemas con tildes y caracteres especiales
$conexion->set_charset("utf8mb4");
?>