<?php
include 'conexion.php';

echo "¡Conexión exitosa a la base de datos de reservas_cerro!";

// Opcional: muestra las tablas que creó Alejandra para verificar
$resultado = $conexion->query("SHOW TABLES");
if ($resultado) {
    echo "<br><br>Tablas en la base de datos:";
    while ($fila = $resultado->fetch_row()) {
        echo "<br>- " . $fila[0];
    }
}
?>