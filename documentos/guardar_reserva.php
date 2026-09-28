<?php

require_once "conexion.php";

header("Content-Type: application/json; charset=utf-8");

// Recibir los datos enviados desde JavaScript
$datos = json_decode(file_get_contents("php://input"), true);

$nombre = $datos["nombre"] ?? "";
$apellidos = $datos["apellidos"] ?? "";
$email = $datos["email"] ?? "";
$fecha = $datos["fecha"] ?? "";
$franja = $datos["franja"] ?? "";
$id_recurso = $datos["id_recurso"] ?? 0;

// Comprobar los datos necesarios
if (empty($nombre) || empty($apellidos) || empty($email) ||
    empty($fecha) || empty($franja) || empty($id_recurso)) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "error" => "Faltan datos de la reserva"
    ]);

    exit;
}

// --------------------------------------------------
// B5: COMPROBAR SI EL HUECO YA ESTÁ OCUPADO
// --------------------------------------------------

$consultaReserva = $conexion->prepare(
    "SELECT COUNT(*) AS total
     FROM reservas
     WHERE id_recurso = ?
       AND fecha = ?
       AND franja = ?
       AND estado = 'Confirmada'"
);

$consultaReserva->bind_param(
    "iss",
    $id_recurso,
    $fecha,
    $franja
);

$consultaReserva->execute();

$resultadoReserva = $consultaReserva->get_result();
$filaReserva = $resultadoReserva->fetch_assoc();

$consultaReserva->close();

// Si ya existe una reserva confirmada, no dejamos crear otra
if ($filaReserva["total"] > 0) {

    http_response_code(409);

    echo json_encode([
        "ok" => false,
        "error" => "El salón ya está reservado para esa fecha y franja."
    ]);

    exit;
}

// --------------------------------------------------
// BUSCAR USUARIO POR EMAIL
// --------------------------------------------------

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
        "INSERT INTO usuarios (nombre, apellidos, email)
         VALUES (?, ?, ?)"
    );

    $statInsert->bind_param(
        "sss",
        $nombre,
        $apellidos,
        $email
    );

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

// --------------------------------------------------
// GUARDAR LA RESERVA
// --------------------------------------------------

$insertReserva = $conexion->prepare(
    "INSERT INTO reservas
     (id_usuario, id_recurso, fecha, franja, estado)
     VALUES (?, ?, ?, ?, 'Confirmada')"
);

$insertReserva->bind_param(
    "iiss",
    $id_usuario,
    $id_recurso,
    $fecha,
    $franja
);

if (!$insertReserva->execute()) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "error" => "Error al guardar la reserva: " . $insertReserva->error
    ]);

    exit;
}

echo json_encode([
    "ok" => true,
    "id_usuario" => $id_usuario,
    "id_reserva" => $conexion->insert_id
]);

$insertReserva->close();
$conexion->close();

?>