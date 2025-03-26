<?php
// Desactivar errores visibles en producción
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

console.log("URL de la petición:", "get_products_by_category.php?category=" + category + "&minPrice=" + minPrice + "&maxPrice=" + maxPrice);

// Conectar a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

// Verificar conexión
if (!$conn) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . mysqli_connect_error()]));
}

// Obtener la categoría desde el parámetro GET
$category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
//$category = $_GET['category'] ?? 'all';
$minPrice = $_GET['minPrice'] ?? 0;
$maxPrice = $_GET['maxPrice'] ?? 600;

$sql = "SELECT id, titulo, precio, imagen FROM final_productos WHERE precio BETWEEN ? AND ?";

if ($category !== 'all') {
    $sql .= " AND id_categoria = ?";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "Error en la preparación de la consulta: " . $conn->error]));
}
if ($category !== 'all') {
    $stmt->bind_param("dds", $minPrice, $maxPrice, $category);
} else {
    $stmt->bind_param("dd", $minPrice, $maxPrice);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];


while ($row = $result->fetch_assoc()) {
    // Decodificar JSON de la imagen
    $imageArray = json_decode($row['imagen'], true);
    
    // Obtener la URL de la imagen
    if (is_array($imageArray)) {
        $imageUrl = $imageArray[0] ?? 'images/default.jpg'; // Si es un array, toma la primera imagen
    } else {
        $imageUrl = 'images/default.jpg'; // Si la decodificación falla, usa una imagen por defecto
    }

    $products[] = [
        'id' => $row['id'],
        'name' => $row['titulo'],
        'price' => number_format($row['precio'], 2), // Formato correcto del precio
        'image' => $imageUrl
    ];
}

echo json_encode(['success' => true, 'products' => $products]);

// Cerrar la consulta
$stmt->close();
$conn->close();



//if (!empty($category)) {
    // Verificar si la columna "imagen" contiene un array o un objeto
    //$query = "SELECT id, titulo, precio, imagen FROM final_productos WHERE id_categoria = ?";
    
    /*$stmt = $conn->prepare($query);
    if (!$stmt) {
        die(json_encode(["error" => "Error en la preparación de la consulta: " . $conn->error]));
    }*/

    /*$stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];*/
    /*while ($row = $result->fetch_assoc()) {
        // Decodificar JSON de la imagen
        $imageArray = json_decode($row['imagen'], true);
        
        // Obtener la URL de la imagen
        if (is_array($imageArray)) {
            $imageUrl = $imageArray[0] ?? 'images/default.jpg'; // Si es un array, toma la primera imagen
        } else {
            $imageUrl = 'images/default.jpg'; // Si la decodificación falla, usa una imagen por defecto
        }

        $products[] = [
            'id' => $row['id'],
            'name' => $row['titulo'],
            'price' => number_format($row['precio'], 2), // Formato correcto del precio
            'image' => $imageUrl
        ];
    }

    echo json_encode(['success' => true, 'products' => $products]);

    // Cerrar la consulta
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó categoría']);
}

// Cerrar la conexión
//$conn->close();*/
?>
