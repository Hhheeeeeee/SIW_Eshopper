<?php

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if (!$conn) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . mysqli_connect_error()]));
}

// Obtener el ID del producto desde la URL
$product_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!empty($product_id)) {
    // Consulta SQL para obtener los detalles del producto
    $sql = "SELECT titulo, precio, imagen, url_marca, url_producto FROM final_productos WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Cambia el tipo de dato de la consulta a STRING
    $stmt->bind_param("s", $product_id);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Decodificar JSON de la imagen
        $imageArray = json_decode($row['imagen'], true);
        $imageUrl = is_array($imageArray) ? ($imageArray[0] ?? 'images/default.jpg') : 'images/default.jpg';

        $product = [
            'id' => $product_id,
            'titulo' => $row['titulo'],
            'precio' => number_format($row['precio'], 2),
            'imagen' => $imageUrl,
            'url_marca' => $row['url_marca'],
            'url_producto' => $row['url_producto']
        ];
        echo json_encode(["success" => true, "product" => $product]);
    } else {
        echo json_encode(["success" => false, "message" => "Producto no encontrado"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "ID de producto inválido"]);
}

$conn->close();

?>
