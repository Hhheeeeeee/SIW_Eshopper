<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Conexión a base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");
if (!$conn) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit;
}

// Recibir y limpiar variables
$user_id = mysqli_real_escape_string($conn, $_POST['user_id'] ?? null);
$total = mysqli_real_escape_string($conn, $_POST['total'] ?? null);
$cantidad = mysqli_real_escape_string($conn, $_POST['cantidad'] ?? null);
$estado = 'Pendiente';
$imagen = mysqli_real_escape_string($conn, $_POST['imagen'] ?? null);
$productTitle = mysqli_real_escape_string($conn, $_POST['productTitle'] ?? null);
$webId = mysqli_real_escape_string($conn, $_POST['webId'] ?? null); 
$precio_unidad = mysqli_real_escape_string($conn, $_POST["precio"] ?? null);

$fecha_pedido = date("Y-m-d H:i:s");
$fecha_creacion = date("Y-m-d H:i:s");
$ultima_modificacion = date("Y-m-d H:i:s");

// Validación básica
if (!$user_id || $total === null || !$webId) {
    echo json_encode(["success" => false, "message" => "Faltan datos del pedido."]);
    $conn->close();
    exit;
}

// Verificar si ya existe ese producto en el carrito pendiente del usuario
$checkSql = "SELECT id, cantidad FROM final_pedidos 
             WHERE user_id = '$user_id' AND webId = '$webId' AND estado = 'Pendiente' 
             LIMIT 1";
$result = mysqli_query($conn, $checkSql);

if (mysqli_num_rows($result) > 0) {
    // Ya existe el producto → Actualizar
    $row = mysqli_fetch_assoc($result);
    $nuevaCantidad = $row['cantidad'] + $cantidad;
    $nuevoTotal = $nuevaCantidad * $precio_unidad;
    $pedidoId = $row['id'];

    $updateSql = "UPDATE final_pedidos 
                  SET cantidad = '$nuevaCantidad', 
                      total = '$nuevoTotal', 
                      ultima_modificacion = '$ultima_modificacion' 
                  WHERE id = '$pedidoId'";

    if ($conn->query($updateSql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Cantidad actualizada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el pedido: " . $conn->error]);
    }
} else {
    // No existe → Insertar nuevo pedido
    $insertSql = "INSERT INTO final_pedidos (user_id, precio_unidad, cantidad, total, estado, imagen, titulo, webId, fecha_pedido, fecha_creacion, ultima_modificacion)
                  VALUES ('$user_id', '$precio_unidad','$cantidad' ,'$total', '$estado', '$imagen', '$productTitle', '$webId', '$fecha_pedido', '$fecha_creacion', '$ultima_modificacion')";

    if ($conn->query($insertSql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Pedido guardado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar el pedido: " . $conn->error]);
    }
}

$conn->close();
?>
