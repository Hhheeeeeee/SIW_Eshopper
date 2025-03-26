<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$servername = "dbserver"; // Cambia según tu configuración
$username = "grupo38";
$password = "lu0xaiM8Si";
$database = "db_grupo38";

// Conectar a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");


// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . $conn->connect_error]));
}

// Consulta para obtener los primeros 8 productos
$sql = "SELECT id, titulo, precio, url_producto, imagen FROM final_productos ORDER BY fecha_creacion DESC LIMIT 8";
$result = $conn->query($sql);
//error_log("Conexión exitosa");

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        //error_log("Row is -> " . $row);
        // Decodificar la imagen si está en formato JSON
        $imageArray = json_decode($row['imagen'], true);
        $imageUrl = $imageArray[0] ?? 'images/default.jpg'; // Usa la primera imagen o una por defecto

        $products[] = [
            "id" => $row["id"],
            "name" => $row["titulo"],
            "price" => $row["precio"],
            "url" => $row["url_producto"],
            "image" => $imageUrl
        ];
        //error_log("Products is -> " . $products);
    }
}

// Cerrar conexión
$conn->close();

// Devolver JSON
echo json_encode(["success" => true, "products" => $products]);
?>
