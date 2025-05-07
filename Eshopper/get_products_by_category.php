<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Conexión
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");
if (!$conn) {
    die(json_encode(["error" => "Error de conexión: " . mysqli_connect_error()]));
}

$category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : 'all';
$minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : 600;

// Base SQL
$sql = "SELECT id, titulo, precio, imagen FROM final_productos WHERE precio BETWEEN ? AND ?";

// Añadir condiciones
$params = [$minPrice, $maxPrice];
$types = "dd";

if ($category !== 'all') {
    $sql .= " AND id_categoria = ?";
    $params[] = (string)$category; 
    $types .= "s";
}

if ($subcategory !== 'all' && $subcategory != -1) {
    $sql .= " AND category_id = ?";
    $params[] = (int)$subcategory;
    $types .= "i";
}

// Preparar consulta
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "Error en la preparación: " . $conn->error]));
}

// Vincular parámetros
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    $imageArray = json_decode($row['imagen'], true);
    $imageUrl = is_array($imageArray) ? ($imageArray[0] ?? 'images/default.jpg') : 'images/default.jpg';

    $products[] = [
        'id' => $row['id'],
        'name' => $row['titulo'],
        'price' => number_format($row['precio'], 2),
        'image' => $imageUrl
    ];
}

echo json_encode(['success' => true, 'products' => $products]);

$stmt->close();
$conn->close();
?>
