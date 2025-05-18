<?php
// get-user-data.php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['name' => '', 'email' => '']);
    exit;
}

$userId = $_SESSION['user_id'];

$mysqli = new mysqli("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a base de datos']);
    exit;
}

$stmt = $mysqli->prepare('SELECT nombre_usuario, correo FROM final_usuarios WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($nombre, $correo);
$stmt->fetch();

echo json_encode([
    'name' => $nombre ?? '',
    'email' => $correo ?? ''
]);

$stmt->close();
$mysqli->close();
