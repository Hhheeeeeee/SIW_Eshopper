<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION["es_admin"]) && $_SESSION["es_admin"] == 1) {
    // Obtener los datos del POST
    $userId = $_POST['user_id'];
    $nombreUsuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena'];

    // Conexión a la base de datos
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if (!$conn) {
        echo json_encode(["success" => false, "message" => "Error de conexión"]);
        exit;
    }

    // Actualizar usuario
    $stmt = $conn->prepare("UPDATE final_usuarios SET nombre_usuario = ?, contraseña = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombreUsuario, password_hash($contrasena, PASSWORD_DEFAULT), $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el usuario"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No tienes permisos para actualizar usuarios."]);
}
?>
