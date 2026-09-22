<?php
// Incluimos la conexión
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de Conexión - Salas del Cerro</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        h1 { color: #2c3e50; font-size: 22px; }
        .success { color: #27ae60; font-weight: bold; }
        ul { padding-left: 20px; }
        li { margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Estado del Sistema (Miembro 2)</h1>
        <p class="success">✔ ¡La conexión con la base de datos <strong>reservas_cerro</strong> funciona correctamente!</p>
        
        <h2>Listado de Salas / Recursos disponibles:</h2>
        <ul>
            <?php
            // Consulta de prueba a la tabla 'recursos' creada por el Miembro 1
            $resultado = $conexion->query("SELECT * FROM recursos");

            if ($resultado && $resultado->num_rows > 0) {
                while ($recurso = $resultado->fetch_assoc()) {
                    echo "<li><strong>" . htmlspecialchars($recurso['nombre']) . "</strong> — " 
                         . htmlspecialchars($recurso['descripcion']) 
                         . " <em>(Capacidad: " . htmlspecialchars($recurso['capacidad']) . " personas)</em></li>";
                }
            } else {
                echo "<li>No hay recursos registrados todavía.</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>