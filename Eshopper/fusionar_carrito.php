<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
session_start();

// Conexión a base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");
if (!$conn) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
    exit;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Usuario no autenticado"]);
    exit;
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// Recibir el carrito como JSON
$carritoJson = $_POST['carrito'] ?? '';
$carrito = json_decode($carritoJson, true);

if (!$carrito || !is_array($carrito)) {
    echo json_encode(["success" => false, "message" => "Carrito inválido o vacío"]);
    exit;
}

$fecha_pedido = date("Y-m-d H:i:s");
$fecha_creacion = date("Y-m-d H:i:s");
$ultima_modificacion = date("Y-m-d H:i:s");

foreach ($carrito as $item) {
    $webId = mysqli_real_escape_string($conn, $item['webId'] ?? '');
    $cantidad = mysqli_real_escape_string($conn, $item['cantidad'] ?? 1);
    $total = mysqli_real_escape_string($conn, $item['total'] ?? 0);
    $imagen = mysqli_real_escape_string($conn, $item['imagen'] ?? '');
    $titulo = mysqli_real_escape_string($conn, $item['productTitle'] ?? ($item['titulo'] ?? ''));
    $precio_unidad = mysqli_real_escape_string($conn, $item['precio'] ?? $item['precio_unidad'] ?? 0);
    $estado = 'Pendiente';

    if (!$webId) continue;

    // Verificar si ya existe el producto en el carrito pendiente del usuario
    $checkSql = "SELECT id, cantidad FROM final_pedidos 
                 WHERE user_id = '$user_id' AND webId = '$webId' AND estado = 'Pendiente' 
                 LIMIT 1";
    $result = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($result) > 0) {
        // Ya existe el producto → actualizar cantidad y total
        $row = mysqli_fetch_assoc($result);
        $nuevaCantidad = $row['cantidad'] + $cantidad;
        $nuevoTotal = $nuevaCantidad * $precio_unidad;
        $pedidoId = $row['id'];

        $updateSql = "UPDATE final_pedidos 
                      SET cantidad = '$nuevaCantidad', 
                          total = '$nuevoTotal', 
                          ultima_modificacion = '$ultima_modificacion' 
                      WHERE id = '$pedidoId'";
        mysqli_query($conn, $updateSql);
    } else {
        // Insertar nuevo pedido
        $insertSql = "INSERT INTO final_pedidos 
                      (user_id, precio_unidad, cantidad, total, estado, imagen, titulo, webId, fecha_pedido, fecha_creacion, ultima_modificacion) 
                      VALUES 
                      ('$user_id', '$precio_unidad', '$cantidad', '$total', '$estado', '$imagen', '$titulo', '$webId', '$fecha_pedido', '$fecha_creacion', '$ultima_modificacion')";
        mysqli_query($conn, $insertSql);
    }
}

$conn->close();
echo json_encode(["success" => true, "message" => "Carrito fusionado correctamente"]);
?>
