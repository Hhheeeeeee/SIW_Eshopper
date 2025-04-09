<?php
session_start();
header('Content-Type: application/json');

/*if (!isset($_SESSION["es_admin"])){
    echo "Falla no esta admin";
} 
if ($_SESSION["es_admin"] != 1){
    echo $_SESSION["es_admin"]; 
    echo "Falla no esta admin";
} */

if (isset($_SESSION["es_admin"]) && $_SESSION["es_admin"] == 1) {

    //echo "Dentro de es admin";
    // Conectar a la base de datos
    $conn = mysqli_connect("dbserver", "grupo38", "lu0xaiM8Si", "db_grupo38");

    if (!$conn) {
        echo json_encode(["success" => false, "message" => "Error de conexión"]);
        exit;
    }

    $result = $conn->query("SELECT nombre_usuario, correo FROM final_usuarios");

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No tienes permisos para ver los usuarios."]);
}
?>
