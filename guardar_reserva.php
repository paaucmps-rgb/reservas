<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

// Leer los datos enviados desde JavaScript (vienen en formato JSON)
$datos = json_decode(file_get_contents('php://input'), true);

$nombre = trim($datos['nombre'] ?? '');
$salon  = trim($datos['salon'] ?? '');
$fecha  = trim($datos['fecha'] ?? '');
$franja = trim($datos['franja'] ?? '');

if ($nombre === '' || $salon === '' || $fecha === '' || $franja === '') {
    echo json_encode(['exito' => false, 'mensaje' => 'Faltan datos obligatorios.']);
    exit;
}

// 1. Buscar el usuario por nombre; si no existe, se crea
$stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $idUsuario = $fila['id_usuario'];
} else {
    $stmtInsert = $conexion->prepare("INSERT INTO usuarios (nombre) VALUES (?)");
    $stmtInsert->bind_param("s", $nombre);
    $stmtInsert->execute();
    $idUsuario = $stmtInsert->insert_id;
    $stmtInsert->close();
}
$stmt->close();

// 2. Buscar el salón (recurso) por nombre; si no existe, se crea
$stmt = $conexion->prepare("SELECT id_recurso FROM recursos WHERE nombre = ?");
$stmt->bind_param("s", $salon);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $idRecurso = $fila['id_recurso'];
} else {
    $stmtInsert = $conexion->prepare("INSERT INTO recursos (nombre) VALUES (?)");
    $stmtInsert->bind_param("s", $salon);
    $stmtInsert->execute();
    $idRecurso = $stmtInsert->insert_id;
    $stmtInsert->close();
}
$stmt->close();

// 3. Insertar la reserva
$estado = "Confirmada";
$stmt = $conexion->prepare("INSERT INTO reservas (id_usuario, id_recurso, fecha, franja, estado) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $idUsuario, $idRecurso, $fecha, $franja, $estado);

if ($stmt->execute()) {
    echo json_encode(['exito' => true, 'mensaje' => 'Reserva guardada correctamente.']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>