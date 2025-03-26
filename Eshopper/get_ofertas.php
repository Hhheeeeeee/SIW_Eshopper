<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Configuración de la base de datos
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

// Consulta para obtener las primeras 8 ofertas con la información del producto (incluyendo la imagen)
$sql = "SELECT f.id, f.product_id, f.precio_original, f.precio_oferta, f.valor_best_seller, f.existe_cupon, f.fecha_creacion, 
        f.ultima_modificacion, p.titulo, p.imagen 
        FROM final_ofertas f
        JOIN final_productos p ON f.product_id = p.id
        ORDER BY f.fecha_creacion DESC 
        LIMIT 8"; // Puedes modificar el límite según tus necesidades

$result = $conn->query($sql);

$offers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Decodificar la imagen si está en formato JSON
        $imageArray = json_decode($row['imagen'], true);
        $imageUrl = $imageArray[0] ?? 'images/default.jpg'; // Usa la primera imagen o una por defecto

        // Insertar los datos de la oferta y la imagen del producto en el array
        $offers[] = [
            "id" => $row["id"],
            "id_producto" => $row["product_id"],
            "titulo" => $row["titulo"],
            "precio_original" => $row["precio_original"],
            "precio_oferta" => $row["precio_oferta"],
            "valor_best_seller" => $row["valor_best_seller"],
            "existe_cupon" => $row["existe_cupon"],
            "fecha_creacion" => $row["fecha_creacion"],
            "ultima_modificacion" => $row["ultima_modificacion"],
            "imagen" => $imageUrl // Imagen decodificada
        ];
    }
}

// Cerrar conexión
$conn->close();

// Devolver los datos en formato JSON
echo json_encode(["success" => true, "offers" => $offers]);
?>
