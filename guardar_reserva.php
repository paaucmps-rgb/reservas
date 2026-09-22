<?php
<<<<<<< HEAD
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
=======
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
//Cambios hechos por Paula
// 1. Leer los datos enviados desde JavaScript en formato JSON
$datos = json_decode(file_get_contents('php://input'), true);

// 2. Extraer y limpiar cada variable con trim() PRIMERO
$nombre  = trim($datos['nombre'] ?? '');
$salon   = trim($datos['salon'] ?? '');
$fecha   = trim($datos['fecha'] ?? '');
$franja  = trim($datos['franja'] ?? '');
$email   = trim($datos['email'] ?? '');

// 3. AHORA SÍ: Validar que los campos obligatorios no estén vacíos
if ($nombre === '' || $salon === '' || $fecha === '' || $franja === '') {
    http_response_code(400);
    echo json_encode(['exito' => false, 'mensaje' => 'Todos los campos son obligatorios.']);
    exit;

>>>>>>> 77693a16fb742ba35c3b7dcbdb1937cc04c07104

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
<<<<<<< HEAD
$stmt->close();

// 2. Buscar el salón (recurso) por nombre; si no existe, se crea
=======
//cambios hechos por paula 

$stmt->close();

// 2. Buscar el salón (recurso) por nombre
>>>>>>> 77693a16fb742ba35c3b7dcbdb1937cc04c07104
$stmt = $conexion->prepare("SELECT id_recurso FROM recursos WHERE nombre = ?");
$stmt->bind_param("s", $salon);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $idRecurso = $fila['id_recurso'];
} else {
<<<<<<< HEAD
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
=======
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


    // Cerrar declaraciones y conexión DENTRO del try
    $stmt->close();
    $conexion->close();

} catch (Throwable $e) {
    // Manejo global de errores B7 - Cambios hechos por Paula
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
>>>>>>> 77693a16fb742ba35c3b7dcbdb1937cc04c07104
