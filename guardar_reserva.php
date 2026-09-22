<?php
/**
 * Archivo: guardar_reserva.php
 * Descripción: Gestiona el almacenamiento seguro de reservas en el servidor.
 * Cambios realizados por Enri y Paula (Validaciones de seguridad B8 y control de excepciones).
 */

// Configuración de errores y cabecera JSON
ini_set('display_errors', 0);
define('MODO_DEBUG', true);
header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Incluir la conexión a la base de datos
    require_once 'conexion.php';

    // Definición de salones y franjas permitidas (Validación B8 - punto 4)
    const SALONES_PERMITIDOS = ['Salón A', 'Salón B', 'Salón A y B'];
    const FRANJAS_PERMITIDAS = ['Mañana', 'Tarde', 'Noche']; // Ajusta según tus franjas reales si cambian

    // 2. Leer los datos enviados desde JavaScript en formato JSON
    $datos = json_decode(file_get_contents('php://input'), true);

    if (!$datos) {
        throw new Exception("No se recibieron datos válidos.");
    }

    // 3. Extraer y limpiar cada variable con trim() (Validación B8 - punto 1)
    $nombre = trim($datos['nombre'] ?? '');
    $apellidos = trim($datos['apellidos'] ?? ''); // Por si se usa
    $salon  = trim($datos['salon'] ?? '');
    $fecha  = trim($datos['fecha'] ?? '');
    $franja = trim($datos['franja'] ?? '');
    $email  = trim($datos['email'] ?? '');

    // 4. Validar que los campos obligatorios no estén vacíos
    if ($nombre === '' || $salon === '' || $fecha === '' || $franja === '') {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'Faltan datos obligatorios.']);
        exit;
    }

    // 5. Limitar la longitud de los campos según la estructura de la base de datos (Validación B8 - punto 2)
    if (mb_strlen($nombre) > 50) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'El nombre no puede superar los 50 caracteres.']);
        exit;
    }
    if ($email !== '' && mb_strlen($email) > 100) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'El correo electrónico es demasiado largo.']);
        exit;
    }

    // 6. Validar el formato del email si se proporciona (Validación B8 - punto 3)
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'El formato del correo electrónico no es válido.']);
        exit;
    }

    // 7. Validar que el salón pertenezca a la lista permitida (Validación B8 - punto 4)
    if (!in_array($salon, SALONES_PERMITIDOS, true)) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'El salón seleccionado no es válido.']);
        exit;
    }

    // 8. Validar la fecha real y comprobar que no sea pasada (Validación B8 - punto 5)
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$d || $d->format('Y-m-d') !== $fecha) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'El formato de fecha no es válido.']);
        exit;
    }
    
    $hoy = new DateTime();
    $hoy->setTime(0, 0, 0); // Limpiar hora para comparar solo días
    if ($d < $hoy) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'No se pueden hacer reservas en fechas pasadas.']);
        exit;
    }

    // 9. Buscar el usuario por nombre; si no existe, se crea (usando consultas preparadas para evitar Inyección SQL)
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

    // 10. Buscar el salón (recurso) por nombre
    $stmt = $conexion->prepare("SELECT id_recurso FROM recursos WHERE nombre = ?");
    $stmt->bind_param("s", $salon);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        $idRecurso = $fila['id_recurso'];
    } else {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'El salón no existe en la base de datos.']);
        exit;
    }
    $stmt->close();

    // 11. Insertar la reserva de forma segura
    $estado = "Confirmada";
    $stmt = $conexion->prepare("INSERT INTO reservas (id_usuario, id_recurso, fecha, franja, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $idUsuario, $idRecurso, $fecha, $franja, $estado);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['exito' => true, 'mensaje' => 'Reserva guardada correctamente.']);
    } else {
        throw new Exception("Error al ejecutar la consulta de inserción: " . $stmt->error);
    }

    // Cerrar declaraciones y conexión
    $stmt->close();
    $conexion->close();

} catch (Throwable $e) {
    // Manejo global de errores del servidor
    error_log('Error en guardar_reserva.php: ' . $e->getMessage());

    $mensaje = 'No se pudo guardar la reserva por un error del servidor.';
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
?>