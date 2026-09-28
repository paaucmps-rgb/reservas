```php
<?php

$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$base_de_datos = "reservas_cerro";

$conexion = mysqli_connect(
    $servidor,
    $usuario,
    $contraseña,
    $base_de_datos
);

if (!$conexion) {
    die("Error de conexión con MySQL: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>
```
