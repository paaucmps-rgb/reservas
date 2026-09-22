<?php
// Cambio hecho por Paula
ini_set('display_errors', 0);
define('MODO_DEBUG', true);

try {
//cambios hechos por paula 
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';
const SALONES = [
    'Salón A'     => ['Salón A', 'Salón A y B'],
    'Salón B'     => ['Salón B', 'Salón A y B'],
    'Salón A y B' => ['Salón A', 'Salón B', 'Salón A y B'],
];
// Leer los datos enviados desde JavaScript (vienen en formato JSON)

//cambios hechos por paula 
$datos = json_decode(file_get_contents('php://input'), true);
if (!isset(SALONES[$salon])) {
    echo json_encode(['exito' => false, 'mensaje' => 'El salón seleccionado no es válido.']);
    exit;
}
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
//cambios hechos por paula 

$stmt->close();

// 2. Buscar el salón (recurso) por nombre
$stmt = $conexion->prepare("SELECT id_recurso FROM recursos WHERE nombre = ?");
$stmt->bind_param("s", $salon);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $idRecurso = $fila['id_recurso'];
} else {
    // Si no se encuentra el salón, mostramos un error y NO lo creamos
    echo json_encode(['exito' => false, 'mensaje' => 'El salón no existe en la base de datos.']);
    exit;
}
$stmt->close();
//Cambios hechos por Paula 
if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['exito' => true, 'mensaje' => 'Reserva guardada con éxito']);
    } else {
        throw new Exception("Error al ejecutar la consulta de inserción.");
    }

    $stmt->close();
    $conexion->close();

} catch (Throwable $e) {
    // Cambio hecho por paula - Manejo de errores en JSON
    error_log('Error en guardar_reserva.php: ' . $e->getMessage());

    $mensaje = 'No se pudo guardar la reserva por un error del servidor.';
    if (defined('MODO_DEBUG') && MODO_DEBUG) {
        $mensaje .= ' Detalle: ' . $e->getMessage();
    }

    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'exito' => false,
        'mensaje' => $mensaje
    ]);
    exit;
}
