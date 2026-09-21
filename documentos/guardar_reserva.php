<?php

require_once "conexion.php";

header("Content-Type: application/json; charset=utf-8");

// Recibir los datos enviados desde JavaScript
$datos = json_decode(file_get_contents("php://input"), true);

$nombre = $datos["nombre"] ?? "";
$apellidos = $datos["apellidos"] ?? "";
$email = $datos["email"] ?? "";

// Comprobar que están los tres datos
if (empty($nombre) || empty($apellidos) || empty($email)) {
    http_response_code(400);
    echo json_encode([
        "ok" => false,
        "error" => "Faltan datos del usuario"
    ]);
    exit;
}

// Buscar usuario por email
$consulta = $conexion->prepare(
    "SELECT id_usuario FROM usuarios WHERE email = ?"
);

$consulta->bind_param("s", $email);
$consulta->execute();

$resultado = $consulta->get_result();

if ($resultado->num_rows > 0) {

    // El usuario ya existe
    $usuario = $resultado->fetch_assoc();
    $id_usuario = $usuario["id_usuario"];

} else {

    // El usuario no existe, así que lo creamos
    $statInsert = $conexion->prepare(
        "INSERT INTO usuarios (nombre, apellidos, email) VALUES (?, ?, ?)"
    );

    $statInsert->bind_param("sss", $nombre, $apellidos, $email);

    if (!$statInsert->execute()) {
        http_response_code(500);
        echo json_encode([
            "ok" => false,
            "error" => "Error al guardar el usuario: " . $statInsert->error
        ]);
        exit;
    }

    $id_usuario = $conexion->insert_id;

    $statInsert->close();
}

$consulta->close();

echo json_encode([
    "ok" => true,
    "id_usuario" => $id_usuario
]);

$conexion->close();
?>