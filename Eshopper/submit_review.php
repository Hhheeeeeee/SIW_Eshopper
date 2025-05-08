<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Iniciar la sesión
session_start();

// Conectar a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

// Verificar la conexión
if (!$conn) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . mysqli_connect_error()]));
}

// Verificar si el usuario está logueado (solo para permitir el envío, no se usa user_id en la tabla de reviews)
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Usuario no logueado"]);
    $conn->close();
    exit;
}

// Validar datos necesarios
if (!isset($_POST["id"], $_POST["name"], $_POST["userUrl"], $_POST["comment"], $_POST["rating"])) {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos"]);
    $conn->close();
    exit;
}

// Recolectar datos enviados desde el frontend
$productId = mysqli_real_escape_string($conn, $_POST['id']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$userUrl = mysqli_real_escape_string($conn, $_POST['userUrl']);
$comment = mysqli_real_escape_string($conn, $_POST['comment']);
$rating = mysqli_real_escape_string($conn, $_POST['rating']);
$createdAt = date('Y-m-d H:i:s');  // Fecha actual

// Verificar si todos los campos están presentes
if (empty($name) || empty($userUrl) || empty($comment) || empty($rating)) {
    echo json_encode(["success" => false, "message" => "Todos los campos son requeridos."]);
    $conn->close();
    exit;
}

// Validar que el rating sea un número entre 1 y 5
if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
    echo json_encode(["success" => false, "message" => "El rating debe ser un número entre 1 y 5"]);
    $conn->close();
    exit;
}

// Insertar la reseña en la base de datos
$sql = "INSERT INTO final_opiniones (producto_id, usuario_nombre, usuario_url, rating, titulo, comentario, fecha, verificado, review_url, fecha_creacion, ultima_modificacion) 
        VALUES ('$productId', '$name', '$userUrl', '$rating', 'Reseña sobre el producto', '$comment', '$createdAt', 0, '', '$createdAt', '$createdAt')";

// Ejecutar la consulta
if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Reseña enviada correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar la reseña: " . $conn->error]);
}

$conn->close();
?>
