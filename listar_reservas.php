<?php
<<<<<<< HEAD
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

$sql = "SELECT r.id_reserva, u.nombre AS nombre, rec.nombre AS salon, r.fecha, r.franja, r.estado
        FROM reservas r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        JOIN recursos rec ON r.id_recurso = rec.id_recurso
        ORDER BY r.fecha DESC";

$resultado = $conexion->query($sql);

$reservas = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $reservas[] = $fila;
    }
}

echo json_encode($reservas);
$conexion->close();
=======
// Cambio hecho por Paula 
ini_set('display_errors', 0);
define('MODO_DEBUG', true);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once 'conexion.php';

    $sql = "SELECT r.id_reserva, u.nombre AS nombre, rec.nombre AS salon, r.fecha, r.franja, r.estado
            FROM reservas r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            JOIN recursos rec ON r.id_recurso = rec.id_recurso
            ORDER BY r.fecha DESC";

    $resultado = $conexion->query($sql);

    $reservas = [];
    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $reservas[] = $fila;
        }
    }

    echo json_encode($reservas);
    $conexion->close();

} catch (Throwable $e) {
    // Cambio hecho por paula - Error formateado como JSON
    error_log('Error en listar_reservas.php: ' . $e->getMessage());

    $mensaje = 'No se pudo obtener la lista de reservas por un error del servidor.';
    if (defined('MODO_DEBUG') && MODO_DEBUG) {
        $mensaje .= ' Detalle: ' . $e->getMessage();
    }

    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => $mensaje
    ]);
    exit;
}
>>>>>>> 77693a16fb742ba35c3b7dcbdb1937cc04c07104
?>