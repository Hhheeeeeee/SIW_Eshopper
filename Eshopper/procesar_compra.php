<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

session_start();

// Conectar a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Error de conexión: " . mysqli_connect_error()]));
}

if (isset($_SESSION["user_id"])) {
    $user_id = mysqli_real_escape_string($conn, $_SESSION["user_id"]);

    // Actualizar el estado de los pedidos del usuario a 'Completado'
    $sql = "UPDATE final_pedidos SET estado = 'Pagado' WHERE user_id = '$user_id' AND estado = 'Pendiente'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Pedidos actualizados a Completado"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar los pedidos: " . $conn->error]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Usuario no logueado"]);
}

$conn->close();
?>