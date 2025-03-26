<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Conexión a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if (!$conn) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . mysqli_connect_error()]));
}

// Obtener el ID del producto desde la URL
$product_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($product_id)) {
    die(json_encode(["success" => false, "message" => "ID de producto inválido"]));
}



// Consulta SQL para obtener las opiniones del producto
$sql = "SELECT usuario_nombre, comentario, fecha 
        FROM final_opiniones 
        WHERE producto_id = ? 
        ORDER BY fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = [
        "usuario" => $row['usuario_nombre'],
        "comentario" => $row['comentario'],
        "rating" => floatval($row['rating']),
        "fecha" => date("d M Y", strtotime($row['fecha']))//'fecha' => date("d M Y, h:i A", strtotime($row['fecha'])) // Formateo de fecha
    ];
}

echo json_encode(["success" => true, "reviews" => $reviews]);


$stmt->close();
$conn->close();


?>
