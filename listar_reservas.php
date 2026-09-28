<?php
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
?>