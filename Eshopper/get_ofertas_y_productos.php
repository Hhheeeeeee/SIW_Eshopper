<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$servername = "dbserver"; // Cambia según tu configuración
$username = "grupo38";
$password = "lu0xaiM8Si";
$database = "db_grupo38";

// Conectar a la base de datos
$conn = mysqli_connect($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . $conn->connect_error]));
}

// Consulta para obtener los primeros 8 productos con la verificación de oferta
$sql = "
    SELECT p.id, IF(o.product_id IS NOT NULL, true, false) AS en_oferta FROM final_productos p
    INNER JOIN final_ofertas o ON p.id = o.product_id ORDER BY p.fecha_creacion DESC LIMIT 8
";
$result = $conn->query($sql);

$products_with_offers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Decodificar la imagen si está en formato JSON
        $imageArray = json_decode($row['imagen'], true);
        $imageUrl = $imageArray[0] ?? 'images/default.jpg'; // Usa la primera imagen o una por defecto

        // Agregar el producto con la información de oferta
        $products_with_offers[] = [
            "id" => $row["id"],
            "name" => $row["titulo"],
            "price" => $row["precio"],
            "url" => $row["url_producto"],
            "image" => $imageUrl,
            "en_oferta" => (bool)$row["en_oferta"] // Convertir el valor a booleano
        ];
    }
}

// Cerrar conexión
$conn->close();

// Devolver JSON con nombre de array modificado
echo json_encode(["success" => true, "offers" => $products_with_offers]);  // Cambié "products" a "offers"
?>