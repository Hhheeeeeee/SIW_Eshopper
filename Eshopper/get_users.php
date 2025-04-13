<?php
session_start();
header('Content-Type: application/json');


if (isset($_SESSION["es_admin"]) && $_SESSION["es_admin"] == 1) {

    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if (!$conn) {
        echo json_encode(["success" => false, "message" => "Error de conexión"]);
        exit;
    }

    $result = $conn->query("SELECT id, nombre_usuario, correo FROM final_usuarios");

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode(["success" => true, "data" => $users]);

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No tienes permisos para ver los usuarios."]);
}
?>
