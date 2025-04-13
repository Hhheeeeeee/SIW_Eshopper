<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Iniciar la sesión
session_start();

// Conectar a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

// Verificar la conexión
if (!$conn) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . mysqli_connect_error()]));
}

// Verificar si el usuario está logueado y si se proporcionó el ID del pedido
if (isset($_SESSION["user_id"]) && isset($_POST["pedido_id"])) {
    $user_id = mysqli_real_escape_string($conn, $_SESSION["user_id"]);
    $pedido_id = mysqli_real_escape_string($conn, $_POST["pedido_id"]);

    // Consulta para eliminar el pedido del carrito del usuario con estado 'Pendiente'
    $sql = "DELETE FROM final_pedidos WHERE id = '$pedido_id' AND user_id = '$user_id' AND estado = 'Pendiente'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Pedido eliminado del carrito"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar el pedido del carrito: " . $conn->error]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Usuario no logueado o ID del pedido no proporcionado"]);
}

// Cerrar la conexión
$conn->close();
?>