<?php
// archivo: actualizar_cantidad.php

// Conectar a la base de datos
$conn = new mysqli("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los datos desde POST
$pedido_id = $_POST['pedido_id'] ?? null;
$cantidad = $_POST['cantidad'] ?? null;
$total = $_POST['total'] ?? null;

if ($pedido_id && $cantidad !== null && $total !== null) {
    // Preparar la consulta
    $sql = "UPDATE final_pedidos SET cantidad = ?, total = ?, ultima_modificacion = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idi", $cantidad, $total, $pedido_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Pedido actualizado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
}

$conn->close();
?>
