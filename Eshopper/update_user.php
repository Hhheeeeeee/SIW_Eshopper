<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION["es_admin"]) && $_SESSION["es_admin"] == 1) {
    // Obtener los datos del POST
    $userId = $_POST['user_id'];
    $nombreUsuario = $_POST['nombre_usuario'] ?? null;
    $contrasena = $_POST['contrasena'] ?? null;

    // Conexión a la base de datos
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if (!$conn) {
        echo json_encode(["success" => false, "message" => "Error de conexión"]);
        exit;
    }

    // Determinar qué campos actualizar
    $campos = [];
    $paramTypes = "";
    $params = [];

    if ($nombreUsuario !== null && $nombreUsuario !== "") {
        $campos[] = "nombre_usuario = ?";
        $paramTypes .= "s";
        $params[] = $nombreUsuario;
    }

    // Si la contraseña es "unchanged", no se actualiza
    if ($contrasena !== null && $contrasena !== "" && $contrasena !== "unchanged") {
        $campos[] = "contraseña = ?";
        $paramTypes .= "s";
        $params[] = password_hash($contrasena, PASSWORD_DEFAULT);
    }

    if (empty($campos)) {
        echo json_encode(["success" => false, "message" => "No se enviaron datos válidos para actualizar."]);
        exit;
    }

    $paramTypes .= "i";
    $params[] = $userId;

    $sql = "UPDATE final_usuarios SET " . implode(", ", $campos) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($paramTypes, ...$params);

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
