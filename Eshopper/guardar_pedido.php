<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Conectar a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit;
}

// Recibir los datos del pedido desde la petición AJAX
$user_id = $_POST['user_id'] ?? null;
$total = $_POST['total'] ?? null;
$estado = 'Pendiente';
$imagen = $_POST['imagen'] ?? null;
$productTitle = $_POST['productTitle'] ?? null;
$webId = $_POST['webId'] ?? null; 
$fecha_pedido = date("Y-m-d H:i:s");
$fecha_creacion = date("Y-m-d H:i:s");
$ultima_modificacion = date("Y-m-d H:i:s");
$precio_unidad =$_POST["precio"] ?? null;  





// Validar que los datos necesarios estén presentes
if (!$user_id || $total === null) {
    echo json_encode(["success" => false, "message" => "Faltan datos del pedido."]);
    $conn->close();
    exit;
}

// Escapar los datos para prevenir inyección SQL (importante!)
$user_id = mysqli_real_escape_string($conn, $user_id);
$total = mysqli_real_escape_string($conn, $total);
$precio_unidad = mysqli_real_escape_string($conn, $precio_unidad);
$estado = mysqli_real_escape_string($conn, $estado);

// Insertar el nuevo pedido en la tabla 'final_pedidos'
$sql = "INSERT INTO final_pedidos (user_id, precio_unidad, total, estado, imagen, titulo, webId, fecha_pedido, fecha_creacion, ultima_modificacion)
        VALUES ('$user_id', '$precio_unidad', '$total', '$estado', '$imagen', '$productTitle', '$webId', '$fecha_pedido', '$fecha_creacion', '$ultima_modificacion')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Pedido guardado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar el pedido: " . $conn->error]);
}

$conn->close();
?>