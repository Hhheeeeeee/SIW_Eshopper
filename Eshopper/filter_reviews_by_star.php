<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Error de conexión a la base de datos."]));
}

$product_id = isset($_GET['id']) ? trim($_GET['id']) : '';
$star_rating = isset($_GET['stars']) ? floatval($_GET['stars']) : 5; // Por defecto, 5 estrellas

if (empty($product_id)) {
    die(json_encode(["success" => false, "message" => "ID de producto inválido."]));
}

// Obtener las reseñas filtradas por estrellas
$sql = "SELECT usuario_nombre, comentario, rating, fecha 
        FROM final_opiniones 
        WHERE producto_id = ? AND ROUND(rating) = ? 
        ORDER BY fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $product_id, $star_rating);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = [
        "usuario" => $row['usuario_nombre'],
        "comentario" => $row['comentario'],
        "rating" => floatval($row['rating']),
        "fecha" => date("d M Y", strtotime($row['fecha']))
    ];
}

echo json_encode(["success" => true, "reviews" => $reviews]);

$stmt->close();
$conn->close();

?>
