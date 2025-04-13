<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Iniciar la sesión (asegúrate de que session_start() esté en tu archivo de sesión)
session_start();

// Conectar a la base de datos
$conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

// Verificar la conexión
if (!$conn) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . mysqli_connect_error()]));
}

// Verificar si el usuario está logueado
if (isset($_SESSION["user_id"])) {
    $user_id = mysqli_real_escape_string($conn, $_SESSION["user_id"]);

    // Consulta para obtener los pedidos del carrito del usuario con estado 'Pendiente'
    $sql = "SELECT id, total, estado, imagen, titulo, webId FROM final_pedidos WHERE user_id = '$user_id' AND estado = 'Pendiente'";
    $result = $conn->query($sql);

    $pedidos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = [
                "id" => $row["id"],
                "total" => $row["total"],
                "estado" => $row["estado"],
                "imagen" => $row["imagen"],
                "titulo" => $row["titulo"],
                "webId" => $row["webId"]
            ];
        }
    }

    echo json_encode(["success" => true, "pedidos" => $pedidos]);

} else {
    echo json_encode(["success" => false, "message" => "Usuario no logueado"]);
}

// Cerrar la conexión
$conn->close();
?>