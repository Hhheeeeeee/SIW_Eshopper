<?php
session_start();
header('Content-Type: application/json');

$response = ["logged_in" => false];

if (isset($_SESSION["user_id"])) {
    $response["logged_in"] = true;
    $response["user_id"] = $_SESSION["user_id"];

    // Consultar datos del usuario para mostrar (opcional, pero útil para tener más info)
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if ($conn) {
        $stmt = $conn->prepare("SELECT nombre_usuario, foto_perfil, es_admin FROM final_usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $stmt->bind_result($nombre_usuario, $foto_perfil, $es_admin);
        if ($stmt->fetch()) {
            $response["nombre_usuario"] = $nombre_usuario;
            $response["foto_perfil"] = $foto_perfil ?: "https://astrobriga.es/wp-content/plugins/give/assets/dist/images/anonymous-user.svg";
            $response["es_admin"] = $es_admin;
        }
        $stmt->close();
        $conn->close();
    } else {
        $response["error_db"] = "Error de conexión a la base de datos";
    }
}

echo json_encode($response);
?>